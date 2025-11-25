@extends('layouts.sidebar')

@section('title', ':: Documentation technique ::')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/documentation.css') }}">
<style>
    /* Modern Design Variables */
    :root {
        --primary-color: #02564A;
        --accent-color: #4FC9C0;
        --bg-light: #F8F9FA;
        --text-primary: #212529;
        --text-secondary: #6C757D;
        --border-color: #E9ECEF;
        --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    /* Page Header */
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary-color);
    }

    /* Tabs Navigation */
    .nav-pills {
        background: white;
        border-radius: 12px;
        padding: 8px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }

    .nav-pills .nav-link {
        border: 2px solid transparent;
        color: var(--text-secondary);
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .nav-pills .nav-link:hover {
        color: var(--primary-color);
        background: rgba(2, 86, 74, 0.05);
    }

    .nav-pills .nav-link.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* Content Card */
    .content-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .content-body {
        padding: 24px;
        background: var(--bg-light);
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .section-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        color: white;
        font-size: 24px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .section-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
    }

    /* Alerts */
    .alert-modern {
        border-radius: 8px;
        border: none;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-modern.alert-info {
        background: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
        border-left: 4px solid #0dcaf0;
    }

    .alert-modern.alert-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .alert-modern.alert-danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    .alert-modern.alert-success {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border-left: 4px solid #198754;
    }

    .alert-modern i {
        font-size: 20px;
        margin-top: 2px;
    }

    .alert-modern h6 {
        font-weight: 600;
        margin-bottom: 8px;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 20px;
        height: 100%;
    }

    .info-card h6 {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 16px;
    }

    .info-card ul {
        margin: 0;
        padding-left: 20px;
    }

    .info-card li {
        margin-bottom: 10px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Code Blocks */
    pre {
        background: #1e1e1e;
        color: #d4d4d4;
        border-radius: 8px;
        padding: 20px;
        overflow-x: auto;
        border: 1px solid var(--border-color);
        font-size: 13px;
        line-height: 1.6;
    }

    pre code {
        color: #d4d4d4;
        font-family: 'Courier New', monospace;
    }

    /* Tables */
    .table-modern {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table-modern thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        color: white;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid var(--border-color);
    }

    .table-modern tbody tr:hover {
        background: rgba(2, 86, 74, 0.05);
    }

    /* Section Titles */
    .section-title-h6 {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 16px;
    }

    /* Lists */
    ol, ul {
        color: var(--text-secondary);
        line-height: 1.8;
    }

    ol li, ul li {
        margin-bottom: 8px;
    }

    /* Container spacing */
    .container-fluid {
        padding: 0 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid pt-0">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="ri-code-s-slash-line"></i>
            Documentation technique
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="content-card">
                <div class="content-body">
                    <!-- Navigation par onglets -->
                    <ul class="nav nav-pills nav-fill mb-4" id="technicalGuideTabs" role="tablist">
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
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-building-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Architecture du système</h5>
                                            <p class="section-subtitle">Vue d'ensemble de l'architecture technique</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-info">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Vue d'ensemble</h6>
                                            <p class="mb-0">Système de gestion des souscriptions Bank-to-Wallet basé sur Laravel 10 avec intégration Orange Money et Musoni.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Technologies utilisées</h6>
                                                <ul>
                                                    <li><strong>Backend :</strong> Laravel 10 (PHP 8.1+)</li>
                                                    <li><strong>Frontend :</strong> Blade Templates + Bootstrap 5</li>
                                                    <li><strong>Base de données :</strong> MySQL/MariaDB</li>
                                                    <li><strong>Cache :</strong> Redis (optionnel)</li>
                                                    <li><strong>Queue :</strong> Database Queue</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Serveur web</h6>
                                                <ul>
                                                    <li><strong>Web Server :</strong> Apache/Nginx</li>
                                                    <li><strong>PHP :</strong> Version 8.1 ou supérieure</li>
                                                    <li><strong>Extensions :</strong> PDO, OpenSSL, cURL, XML</li>
                                                    <li><strong>Composer :</strong> Gestionnaire de dépendances</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="section-title-h6">Structure des dossiers</h6>
                                            <pre><code>app/
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
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-api-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">API & Intégrations</h5>
                                            <p class="section-subtitle">Documentation des API et endpoints</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-warning">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Endpoints critiques</h6>
                                            <p class="mb-0">Ces endpoints sont utilisés par Orange Money et ne doivent pas être modifiés sans coordination.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Orange Money API</h6>
                                                <ul>
                                                    <li><strong>Endpoint :</strong> <code>POST /omrequest</code></li>
                                                    <li><strong>Format :</strong> SOAP XML</li>
                                                    <li><strong>Authentification :</strong> Aucune (webhook)</li>
                                                    <li><strong>CSRF :</strong> Exclu</li>
                                                    <li><strong>Middleware :</strong> Aucun</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Musoni API</h6>
                                                <ul>
                                                    <li><strong>Base URL :</strong> <code>{{ env('API_URL') }}</code></li>
                                                    <li><strong>Authentification :</strong> Basic Auth</li>
                                                    <li><strong>Headers :</strong> X-Fineract-Platform-TenantId</li>
                                                    <li><strong>Format :</strong> JSON</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="info-card">
                                                <h6>N8N Webhooks</h6>
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
                                    </div>

                                    <div class="alert-modern alert-info mt-4">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Configuration des variables d'environnement</h6>
                                            <pre><code># Orange Money
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
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-database-2-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Structure de la base de données</h5>
                                            <p class="section-subtitle">Documentation des tables et schémas</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-info">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Tables principales</h6>
                                            <p class="mb-0">Les tables suivantes sont essentielles au fonctionnement de l'application.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Tables de gestion</h6>
                                                <ul>
                                                    <li><strong>subscription</strong> - Souscriptions actives</li>
                                                    <li><strong>validation</strong> - Demandes en attente</li>
                                                    <li><strong>unsubscription</strong> - Résiliations</li>
                                                    <li><strong>users</strong> - Utilisateurs système</li>
                                                    <li><strong>settings</strong> - Configuration</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Tables de transactions</h6>
                                                <ul>
                                                    <li><strong>transaction</strong> - Transactions</li>
                                                    <li><strong>get_balance</strong> - Consultations de solde</li>
                                                    <li><strong>mini_statement</strong> - Mini relevés</li>
                                                    <li><strong>cancel_trans</strong> - Annulations</li>
                                                    <li><strong>activity_log</strong> - Logs d'activité</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="section-title-h6">Schéma de la table subscription</h6>
                                            <pre><code>CREATE TABLE subscription (
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
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-shield-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Sécurité et authentification</h5>
                                            <p class="section-subtitle">Guide de sécurité et gestion des permissions</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-danger">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Points critiques</h6>
                                            <p class="mb-0">Ces éléments sont essentiels pour la sécurité de l'application.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Authentification</h6>
                                                <ul>
                                                    <li><strong>Session :</strong> Gestion des sessions utilisateur</li>
                                                    <li><strong>Rôles :</strong> Système de permissions basé sur les rôles</li>
                                                    <li><strong>Middleware :</strong> Vérification des permissions</li>
                                                    <li><strong>Logout :</strong> Invalidation des sessions</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Protection des données</h6>
                                                <ul>
                                                    <li><strong>CSRF :</strong> Protection contre les attaques CSRF</li>
                                                    <li><strong>Validation :</strong> Validation stricte des entrées</li>
                                                    <li><strong>Logs :</strong> Traçabilité des actions</li>
                                                    <li><strong>Chiffrement :</strong> Données sensibles chiffrées</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="section-title-h6">Rôles et permissions</h6>
                                            <div class="table-responsive">
                                                <table class="table table-modern">
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
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-bug-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Guide de dépannage</h5>
                                            <p class="section-subtitle">Solutions aux problèmes courants</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-warning">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Problèmes courants</h6>
                                            <p class="mb-0">Voici les problèmes les plus fréquents et leurs solutions.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Problèmes de connexion</h6>
                                                <ul>
                                                    <li><strong>Erreur 500 :</strong> Vérifier les logs Laravel</li>
                                                    <li><strong>Timeout :</strong> Vérifier la connexion Musoni</li>
                                                    <li><strong>CSRF Error :</strong> Vérifier les tokens</li>
                                                    <li><strong>Session expirée :</strong> Reconnecter l'utilisateur</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h6>Problèmes Orange Money</h6>
                                                <ul>
                                                    <li><strong>Webhook non reçu :</strong> Vérifier l'URL publique</li>
                                                    <li><strong>XML invalide :</strong> Vérifier le format SOAP</li>
                                                    <li><strong>Timeout :</strong> Vérifier la réponse dans les 30s</li>
                                                    <li><strong>Erreur 302-307 :</strong> Codes d'erreur Orange</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="section-title-h6">Commandes de diagnostic</h6>
                                            <pre><code># Vérifier les routes
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

                                    <div class="alert-modern alert-info mt-4">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Support technique</h6>
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
