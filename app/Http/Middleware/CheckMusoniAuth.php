<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckMusoniAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->has('username')) {
            return redirect('/login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Vérifier l'expiration de la session
        $lastActivity = session('last_activity');
        $sessionLifetime = config('session.lifetime', 120); // minutes
        
        if ($lastActivity && (time() - $lastActivity) > ($sessionLifetime * 60)) {
            // Session expirée
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('Session expirée', [
                'username' => session('username'),
                'last_activity' => $lastActivity,
                'expired_after' => $sessionLifetime . ' minutes'
            ]);
            
            return redirect('/login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Mettre à jour le timestamp de dernière activité
        session(['last_activity' => time()]);

        // Vérifier l'intégrité de la session (protection contre session hijacking)
        $sessionFingerprint = $this->generateSessionFingerprint($request);
        $storedFingerprint = session('session_fingerprint');

        if ($storedFingerprint && $storedFingerprint !== $sessionFingerprint) {
            // Fingerprint différent = possible session hijacking
            Log::warning('Tentative de session hijacking détectée', [
                'username' => session('username'),
                'stored_fingerprint' => $storedFingerprint,
                'current_fingerprint' => $sessionFingerprint,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('error', 'Suspicion d\'activité suspecte détectée. Veuillez vous reconnecter.');
        }

        // Stocker le fingerprint si ce n'est pas déjà fait
        if (!$storedFingerprint) {
            session(['session_fingerprint' => $sessionFingerprint]);
        }

        return $next($request);
    }

    /**
     * Génère un fingerprint unique pour la session basé sur IP et User-Agent
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function generateSessionFingerprint(Request $request): string
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Utiliser un hash pour protéger les informations
        return hash('sha256', $ip . '|' . $userAgent . '|' . config('app.key'));
    }
}
