@extends('layouts.sidebar')

@section('title', ':: Documentation technique ::')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pt-0">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card shadow-lg border-0 card-hover fade-in">
                <div class="card-header bg-gradient-dark text-white text-center d-flex align-content-center">
                    <i class="ri-code-s-slash-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Documentation technique</h4>
                </div>
                <div class="card-body bg-light">
                    
                    <!-- Navigation par onglets -->
                    <ul class="nav nav-pills nav-fill mb-4 bg-white rounded-3 p-2 shadow-sm" id="technicalGuideTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="architecture-tab" data-bs-toggle="tab" data-bs-target="#architecture" type="button" role="tab">
                                <i class="ri-building-line me-2"></i>Architecture
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab">
                                <i class="ri-api-line me-2"></i>API & Intégrations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="database-tab" data-bs-toggle="tab" data-bs-target="#database" type="button" role="tab">
                                <i class="ri-database-2-line me-2"></i>Base de données
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="ri-shield-line me-2"></i>Sécurité
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="troubleshooting-tab" data-bs-toggle="tab" data-bs-target="#troubleshooting" type="button" role="tab">
                                <i class="ri-bug-line me-2"></i>Dépannage
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="technicalGuideTabsContent">
                        
                        <!-- Onglet Architecture -->
                        <div class="tab-pane fade show active" id="architecture" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-building-line me-2"></i>Architecture du système
                                    </h5>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="ri-information-line me-2"></i>Vue d'ensemble</h6>
                                        <p class="mb-0">Système de gestion des souscriptions Bank-to-Wallet basé sur Laravel 10 avec intégration Orange Money et Musoni.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Technologies utilisées</h6>
                                            <ul>
                                                <li><strong>Backend :</strong> Laravel 10 (PHP 8.1+)</li>
                                                <li><strong>Frontend :</strong> Blade Templates + Bootstrap 5</li>
                                                <li><strong>Base de données :</strong> MySQL/MariaDB</li>
                                                <li><strong>Cache :</strong> Redis (optionnel)</li>
                                                <li><strong>Queue :</strong> Database Queue</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Serveur web</h6>
                                            <ul>
                                                <li><strong>Web Server :</strong> Apache/Nginx</li>
                                                <li><strong>PHP :</strong> Version 8.1 ou supérieure</li>
                                                <li><strong>Extensions :</strong> PDO, OpenSSL, cURL, XML</li>
                                                <li><strong>Composer :</strong> Gestionnaire de dépendances</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-primary">Structure des dossiers</h6>
                                            <pre class="bg-dark p-3 rounded"><code>app/
├── Http/
│   ├── Controllers/     # Contrôleurs de l'application
│   └── Middleware/      # Middlewares personnalisés
├── Models/              # Modèles Eloquent
└── Providers/           # Service Providers

resources/
├── views/               # Templates Blade
│   ├── layouts/         # Layouts principaux
│   └── documentation/   # Vues de documentation
└── css/                 # Styles CSS

routes/
├── web.php              # Routes web
└── api.php              # Routes API

storage/
├── logs/                # Fichiers de logs
└── app/                 # Stockage des fichiers</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet API & Intégrations -->
                        <div class="tab-pane fade" id="api" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-api-line me-2"></i>API & Intégrations
                                    </h5>
                                    
                                    <div class="alert alert-warning">
                                        <h6><i class="ri-error-warning-line me-2"></i>Endpoints critiques</h6>
                                        <p class="mb-0">Ces endpoints sont utilisés par Orange Money et ne doivent pas être modifiés sans coordination.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Orange Money API</h6>
                                            <ul>
                                                <li><strong>Endpoint :</strong> <code>POST /omrequest</code></li>
                                                <li><strong>Format :</strong> SOAP XML</li>
                                                <li><strong>Authentification :</strong> Aucune (webhook)</li>
                                                <li><strong>CSRF :</strong> Exclu</li>
                                                <li><strong>Middleware :</strong> Aucun</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Musoni API</h6>
                                            <ul>
                                                <li><strong>Base URL :</strong> <code>{{ env('API_URL') }}</code></li>
                                                <li><strong>Authentification :</strong> Basic Auth</li>
                                                <li><strong>Headers :</strong> X-Fineract-Platform-TenantId</li>
                                                <li><strong>Format :</strong> JSON</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-primary">N8N Webhooks</h6>
                                            <ul>
                                                <li><strong>Base URL :</strong> <code>https://acepmg.it4life.org/webhook/</code></li>
                                                <li><strong>Endpoints :</strong>
                                                    <ul>
                                                        <li><code>getAccBal</code> - Consultation de solde</li>
                                                        <li><code>deposit_account</code> - Dépôt</li>
                                                        <li><code>withdraw_account</code> - Retrait</li>
                                                        <li><code>statement_account</code> - Mini relevé</li>
                                                        <li><code>cancel_transfert</code> - Annulation</li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <h6><i class="ri-information-line me-2"></i>Configuration des variables d'environnement</h6>
                                        <pre class="bg-dark p-3 rounded"><code># Orange Money
ORANGE_MONEY_URL=https://api.orange.com
ORANGE_MONEY_CLIENT_ID=your_client_id
ORANGE_MONEY_CLIENT_SECRET=your_client_secret

# Musoni
API_URL=https://your-musoni-instance.com
API_USERNAME=your_username
API_PASSWORD=your_password
API_SECRET=your_tenant_id
API_KEY=your_api_key

# N8N
N8N_USERNAME=your_n8n_username
N8N_PASSWORD=your_n8n_password</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Base de données -->
                        <div class="tab-pane fade" id="database" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-database-2-line me-2"></i>Structure de la base de données
                                    </h5>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="ri-information-line me-2"></i>Tables principales</h6>
                                        <p class="mb-0">Les tables suivantes sont essentielles au fonctionnement de l'application.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Tables de gestion</h6>
                                            <ul>
                                                <li><strong>subscription</strong> - Souscriptions actives</li>
                                                <li><strong>validation</strong> - Demandes en attente</li>
                                                <li><strong>unsubscription</strong> - Résiliations</li>
                                                <li><strong>users</strong> - Utilisateurs système</li>
                                                <li><strong>settings</strong> - Configuration</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Tables de transactions</h6>
                                            <ul>
                                                <li><strong>transaction</strong> - Transactions</li>
                                                <li><strong>get_balance</strong> - Consultations de solde</li>
                                                <li><strong>mini_statement</strong> - Mini relevés</li>
                                                <li><strong>cancel_trans</strong> - Annulations</li>
                                                <li><strong>activity_log</strong> - Logs d'activité</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-primary">Schéma de la table subscription</h6>
                                            <pre class="bg-dark p-3 rounded"><code>CREATE TABLE subscription (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    client_id VARCHAR(255),
    account_no VARCHAR(255),
    msisdn VARCHAR(15),
    alias VARCHAR(255),
    code_service VARCHAR(50),
    key VARCHAR(8),
    date_sub TIMESTAMP,
    bank_agent VARCHAR(255),
    account_status ENUM('0','1') DEFAULT '1',
    libelle VARCHAR(255),
    officeName VARCHAR(255),
    mobile_no VARCHAR(15),
    client_cin VARCHAR(50),
    client_lastname VARCHAR(255),
    client_firstName VARCHAR(255),
    client_dob DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Sécurité -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-shield-line me-2"></i>Sécurité et authentification
                                    </h5>
                                    
                                    <div class="alert alert-danger">
                                        <h6><i class="ri-error-warning-line me-2"></i>Points critiques</h6>
                                        <p class="mb-0">Ces éléments sont essentiels pour la sécurité de l'application.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Authentification</h6>
                                            <ul>
                                                <li><strong>Session :</strong> Gestion des sessions utilisateur</li>
                                                <li><strong>Rôles :</strong> Système de permissions basé sur les rôles</li>
                                                <li><strong>Middleware :</strong> Vérification des permissions</li>
                                                <li><strong>Logout :</strong> Invalidation des sessions</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Protection des données</h6>
                                            <ul>
                                                <li><strong>CSRF :</strong> Protection contre les attaques CSRF</li>
                                                <li><strong>Validation :</strong> Validation stricte des entrées</li>
                                                <li><strong>Logs :</strong> Traçabilité des actions</li>
                                                <li><strong>Chiffrement :</strong> Données sensibles chiffrées</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-primary">Rôles et permissions</h6>
                                            <div class="table-responsive">
                                                <table class="table table-dark table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Rôle</th>
                                                            <th>Permissions</th>
                                                            <th>Accès</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>CREATION PRET</td>
                                                            <td>Souscription, Résiliation</td>
                                                            <td>Services uniquement</td>
                                                        </tr>
                                                        <tr>
                                                            <td>APPROBATION 1 du PRET</td>
                                                            <td>Validation</td>
                                                            <td>Validations uniquement</td>
                                                        </tr>
                                                        <tr>
                                                            <td>INFORMATIQUE</td>
                                                            <td>Toutes + Documentation technique</td>
                                                            <td>Complet</td>
                                                        </tr>
                                                        <tr>
                                                            <td>SUPER ADMIN</td>
                                                            <td>Toutes + Paramètres</td>
                                                            <td>Complet + Administration</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Dépannage -->
                        <div class="tab-pane fade" id="troubleshooting" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-bug-line me-2"></i>Guide de dépannage
                                    </h5>
                                    
                                    <div class="alert alert-warning">
                                        <h6><i class="ri-error-warning-line me-2"></i>Problèmes courants</h6>
                                        <p class="mb-0">Voici les problèmes les plus fréquents et leurs solutions.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Problèmes de connexion</h6>
                                            <ul>
                                                <li><strong>Erreur 500 :</strong> Vérifier les logs Laravel</li>
                                                <li><strong>Timeout :</strong> Vérifier la connexion Musoni</li>
                                                <li><strong>CSRF Error :</strong> Vérifier les tokens</li>
                                                <li><strong>Session expirée :</strong> Reconnecter l'utilisateur</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Problèmes Orange Money</h6>
                                            <ul>
                                                <li><strong>Webhook non reçu :</strong> Vérifier l'URL publique</li>
                                                <li><strong>XML invalide :</strong> Vérifier le format SOAP</li>
                                                <li><strong>Timeout :</strong> Vérifier la réponse dans les 30s</li>
                                                <li><strong>Erreur 302-307 :</strong> Codes d'erreur Orange</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="text-primary">Commandes de diagnostic</h6>
                                            <pre class="bg-dark p-3 rounded"><code># Vérifier les routes
php artisan route:list

# Vérifier la configuration
php artisan config:cache

# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
php artisan permissions:check

# Tester la connexion Musoni
php artisan test:musoni-connection

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear</code></pre>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <h6><i class="ri-information-line me-2"></i>Support technique</h6>
                                        <p class="mb-0">Pour les problèmes complexes, contactez l'équipe de développement avec les logs d'erreur complets.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script pour les onglets
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#technicalGuideTabs button[data-bs-toggle="tab"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Retirer la classe active de tous les boutons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Ajouter la classe active au bouton cliqué
            this.classList.add('active');
        });
    });
});
</script>
@endsection
