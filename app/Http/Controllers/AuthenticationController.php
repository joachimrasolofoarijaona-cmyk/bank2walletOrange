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
    # __Function to get hierarchy by office id__
    private function getHierarchy($office_id)
    {
       # __Musoni - Utiliser config() au lieu de env() pour de meilleures performances__
        $api_username = config('app.api_username') ?: env('API_USERNAME');
        $api_password = config('app.api_password') ?: env('API_PASSWORD');
        $api_url = config('app.api_url') ?: env('API_URL');
        $api_secret = config('app.api_secret') ?: env('API_SECRET');
        $api_key = config('app.api_key') ?: env('API_KEY');

        $get_hierarchy = Http::withBasicAuth($api_username, $api_password)
            ->withHeaders([
                'X-Fineract-Platform-TenantId' => $api_secret,
                'x-api-key' => $api_key,
                'Accept' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(30) // Ajouter un timeout
            ->get($api_url . '/offices/' . $office_id);

        $parent_data = $get_hierarchy->json();
        return $parent_data;
    }

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
                $selected_roles = $data_user['selectedRoles'] ?? [];
                $roles = [];
                
                // Prévenir les variables non définies
                $hierarchy = null;
                $office_name = null;
                $parent_name = null;
                
                // Déterminer le rôle avec la plus haute priorité parmi les rôles sélectionnés
                $ccKeywords = ['CREATION CLIENT', 'CREATION COMPTE CAV', 'CREATION PRET'];
                $valKeywords = ['APPROBATION 1 DU PRET', 'APPROBATION 2 DU PRET', 'CHEF D AGENCE', 'DIRECTEUR DE RESEAU DAGENCES'];
                $adminKeywords = ['SUPER USER', 'INFORMATIQUE', 'SUPER ADMIN', 'DIRECTEUR'];
                
                // Priorité: admin (3) > val (2) > cc (1) > user (0)
                $resolvedRole = 'user';
                $resolvedPriority = 0;
                
                foreach ($selected_roles as $role) {
                    $roleName = strtoupper($role['name'] ?? '');
                    
                    // Admin
                    foreach ($adminKeywords as $kw) {
                        if (str_contains($roleName, $kw)) {
                            $resolvedRole = 'admin';
                            $resolvedPriority = 3;
                            break 2; // admin trouvé, on peut sortir
                        }
                    }
                    
                    // Validation
                    if ($resolvedPriority < 3) {
                        foreach ($valKeywords as $kw) {
                            if (str_contains($roleName, $kw)) {
                                if ($resolvedPriority < 2) {
                                    $resolvedRole = 'val';
                                    $resolvedPriority = 2;
                                }
                                // ne pas break 2; on continue pour vérifier si un rôle admin existe plus loin
                                break;
                            }
                        }
                    }
                    
                    // Création client/compte/prêt
                    if ($resolvedPriority < 2) {
                        foreach ($ccKeywords as $kw) {
                            if (str_contains($roleName, $kw)) {
                                if ($resolvedPriority < 1) {
                                    $resolvedRole = 'cc';
                                    $resolvedPriority = 1;
                                }
                                break;
                            }
                        }
                    }
                }
                
                // Récupérer la hiérarchie une seule fois si nécessaire
                if (in_array($resolvedRole, ['cc', 'val', 'admin'], true)) {
                    $officeData = $this->getHierarchy($data_user['officeId']);
                    $hierarchy = $officeData['hierarchy'] ?? null;
                    $office_name = $officeData['name'] ?? null;
                    $parent_name = $officeData['parentName'] ?? null;
                }
                
                // Assigner le rôle résolu
                $roles[] = $resolvedRole;
                


                if ($data_user['isEnabled'] === true) {
                    session([
                        'id' => $data_user['id'],
                        'username' => $data_user['username'],
                        'firstname' => $data_user['firstname'],
                        'lastname' => $data_user['lastname'],
                        'officeName' => $office_name,
                        'selectedRoles' => $data_user['selectedRoles'],
                        'roles' => $roles,
                        'parent_name' => $parent_name,
                        'hierarchy' => $hierarchy,
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
        // Sauvegarder le username avant de vider la session pour le log
        $username = session('username');

        # log 
        if ($username) {
            logActivity(
                $username,
                'logout',
                'lougout_success',
            );
        }

        // Méthode recommandée par Laravel pour la déconnexion :
        // 1. Invalider la session (vide les données et marque la session comme invalide)
        $request->session()->invalidate();
        
        // 2. Régénérer l'ID de session (crée un nouvel ID de session)
        $request->session()->regenerate();
        
        // 3. Régénérer le token CSRF pour la nouvelle session
        $request->session()->regenerateToken();

        // 4. Rediriger vers login avec un message de succès
        // La session est maintenant complètement nouvelle avec un nouveau token CSRF
        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
