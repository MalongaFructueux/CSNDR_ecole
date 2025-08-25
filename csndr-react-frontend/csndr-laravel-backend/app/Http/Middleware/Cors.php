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
        // Déterminer les origines autorisées selon l'environnement
        $localOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];
        $productionOrigins = [
            'https://csndr-gestion.com',
        ];

        $allowedOrigins = app()->environment('local') ? $localOrigins : $productionOrigins;
        $requestOrigin = $request->headers->get('Origin');
        $allowOriginHeader = in_array($requestOrigin, $allowedOrigins, true)
            ? $requestOrigin
            : ($allowedOrigins[0] ?? '*');
        
        // Gérer la requête OPTIONS (prévol)
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowOriginHeader)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        $response = $next($request);
        
        // Ajouter les en-têtes CORS à la réponse
        $response->headers->set('Access-Control-Allow-Origin', $allowOriginHeader);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        
        return $response;
    }
}
