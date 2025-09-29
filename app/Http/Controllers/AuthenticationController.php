<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        return view('authentication');
    }
    public function authentication(Request $request)
    {
        // Musoni - Utiliser config() au lieu de env() pour de meilleures performances
        $api_username = config('app.api_username') ?: env('API_USERNAME');
        $api_password = config('app.api_password') ?: env('API_PASSWORD');
        $api_url = config('app.api_url') ?: env('API_URL');
        $api_secret = config('app.api_secret') ?: env('API_SECRET');
        $api_key = config('app.api_key') ?: env('API_KEY');

        // Vérifier que toutes les variables d'environnement sont définies
        if (!$api_username || !$api_password || !$api_url || !$api_secret || !$api_key) {
            Log::error('Variables d\'environnement manquantes pour l\'API Musoni', [
                'api_username' => $api_username ? 'défini' : 'manquant',
                'api_password' => $api_password ? 'défini' : 'manquant',
                'api_url' => $api_url ? 'défini' : 'manquant',
                'api_secret' => $api_secret ? 'défini' : 'manquant',
                'api_key' => $api_key ? 'défini' : 'manquant',
            ]);

            return back()->withErrors(['login' => 'Erreur de configuration du serveur. Veuillez contacter l\'administrateur.']);
        }

        // Validation des inputs dans le formulaire
        $request->validate([
            'matricule' => 'required|string|max:5',
            'password' => 'required|string|min:8',
        ]);

        $user_matricule = $request->input('matricule');
        $user_password = $request->input('password');

        try {
            // Authenticate user by Musoni Credentials
            $credentials = Http::withBasicAuth($api_username, $api_password)
                ->withHeaders([
                    'X-Fineract-Platform-TenantId' => $api_secret,
                    'x-api-key' => $api_key,
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(30) // Ajouter un timeout
                ->post($api_url . '/authentication', [
                    'username' => $user_matricule,
                    'password' => $user_password,
                ]);

            $data = $credentials->json();

            // If users exists 
            if (isset($data['authenticated']) && $data['authenticated'] === true) {
                // :: if account is enable ::
                // Get user infos and selected roles
                $get_user_infos = Http::withBasicAuth($api_username, $api_password)
                    ->withHeaders([
                        'X-Fineract-Platform-TenantId' => $api_secret,
                        'x-api-key' => $api_key,
                        'Accept' => 'application/json',
                    ])
                    ->withoutVerifying()
                    ->timeout(30) // Ajouter un timeout
                    ->get($api_url . '/users/' . $data['userId']);

                if (!$get_user_infos->successful()) {
                    Log::error('Erreur lors de la récupération des informations utilisateur', [
                        'status' => $get_user_infos->status(),
                        'response' => $get_user_infos->body()
                    ]);
                    return back()->withErrors(['login' => 'Erreur lors de la récupération des informations utilisateur.']);
                }

                $data_user = $get_user_infos->json();

                if ($data_user['isEnabled'] === true) {

                    session([
                        'id' => $data_user['id'],
                        'username' => $data_user['username'],
                        'firstname' => $data_user['firstname'],
                        'lastname' => $data_user['lastname'],
                        'officeName' => $data_user['officeName'],
                        'selectedRoles' => $data_user['selectedRoles'],
                        'api_token' => $data['base64EncodedAuthenticationKey'] ?? null, // optionnel
                    ]);

                    # log 
                    logActivity(
                        session('username'),
                        'login',
                        'success_login'
                       
                    );

                    // Rediriger vers la route /accueil au lieu d'afficher directement la vue
                    return redirect()->route('show.index');
                } else {
                    # log 
                    logActivity(
                        session('username'),
                        'login',
                        'fraud_login_tentative',
                    );
                    return back()->withErrors(['login' => 'Compte utilisateur désactivé.']);
                }
            } elseif (isset($data['developerMessage']) && $data['developerMessage'] === 'Invalid authentication details were passed in api request.') {
                # log 
                logActivity(
                    session('username'),
                    'login',
                    'error_credentials',
                );
                return back()->withErrors(['login' => "Identifiant ou mot de passe non reconnu."]);
            } else {
                Log::warning('Tentative de connexion échouée', [
                    'matricule' => $user_matricule,
                    'response' => $data
                ]);
                # log 
                logActivity(
                    session('username'),
                    'login',
                    'error_credentials',
                );
                return back()->withErrors(['login' => 'Identifiants invalides.']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'authentification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            # log 
            logActivity(
                session('username'),
                'login',
                'error_server_connection',
            );

            return back()->withErrors(['login' => 'Erreur de connexion au serveur. Veuillez réessayer.']);
        }
    }


    public function destroy(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        # log 
        logActivity(
            session('username'),
            'logout',
            'lougout_success',
        );

        return redirect('/login');
    }
}
