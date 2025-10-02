<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckMusoniAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== CheckMusoniAuth MIDDLEWARE APPELÉ ===');
        Log::info('URL: ' . $request->url());
        Log::info('Méthode: ' . $request->method());
        Log::info('Headers: ', $request->headers->all());
        Log::info('Session ID: ' . $request->session()->getId());
        Log::info('Has username: ' . (session()->has('username') ? 'OUI' : 'NON'));
        Log::info('Username: ' . session('username'));
        Log::info('All session data: ', session()->all());
        Log::info('=== FIN CheckMusoniAuth ===');

        if (!session()->has('username')) {
            Log::warning('❌ Session username manquante, redirection vers /login');
            Log::warning('URL qui a causé la redirection: ' . $request->fullUrl());
            return redirect('/login');
        }

        Log::info('✅ Session valide, passage au contrôleur');
        return $next($request);
    }
}
