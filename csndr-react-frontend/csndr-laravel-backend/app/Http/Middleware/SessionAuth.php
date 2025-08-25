<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug logs
        Log::info('SessionAuth middleware - Session ID: ' . $request->session()->getId());
        Log::info('SessionAuth middleware - Auth check: ' . (Auth::check() ? 'true' : 'false'));
        Log::info('SessionAuth middleware - User ID: ' . (Auth::id() ?? 'null'));
        Log::info('SessionAuth middleware - Session data: ' . json_encode($request->session()->all()));
        
        if (!Auth::check()) {
            Log::warning('SessionAuth middleware - User not authenticated');
            return response()->json([
                'error' => 'Non authentifié',
                'message' => 'Vous devez être connecté pour accéder à cette ressource'
            ], 401);
        }

        return $next($request);
    }
}
