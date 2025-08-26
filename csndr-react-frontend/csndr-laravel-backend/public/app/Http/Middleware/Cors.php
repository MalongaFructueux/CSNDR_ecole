<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            // En développement, autoriser dynamiquement l'origine de la requête.
            // En production, utiliser une liste blanche stricte.
            $allowedOrigins = ['http://localhost:3000', 'https://csndr-gestion.com'];
            
            $origin = $request->header('Origin');
            
            // Si l'origine n'est pas dans la liste des origines autorisées et qu'on est en production
            if (!in_array($origin, $allowedOrigins) && app()->environment('production')) {
                $origin = $allowedOrigins[1]; // Utiliser le domaine de production par défaut
            } elseif (!in_array($origin, $allowedOrigins) && app()->environment('local')) {
                $origin = $allowedOrigins[0]; // Utiliser localhost en développement
            }

            // Gérer la requête OPTIONS (prévol)
            if ($request->isMethod('OPTIONS')) {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
                    ->header('Access-Control-Allow-Credentials', 'true')
                    ->header('Vary', 'Origin');
            }

            $response = $next($request);
            
            // Ajouter les en-têtes CORS à la réponse
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Vary', 'Origin');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            return $response;
        } catch (\Exception $e) {
            // Log l'erreur et retourner une réponse d'erreur
            \Log::error('Erreur CORS: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur CORS'], 500);
        }
    }
}
