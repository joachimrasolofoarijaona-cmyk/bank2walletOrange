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
        Log::info('CheckMusoniAuth middleware appelé', [
            'url' => $request->url(),
            'has_username' => session()->has('username'),
            'username' => session('username'),
            'all_session' => session()->all()
        ]);

        if (!session()->has('username')) {
            Log::warning('Session username manquante, redirection vers /login');
            return redirect('/login');
        }

        Log::info('Session valide, passage au contrôleur');
        return $next($request);
    }
}
