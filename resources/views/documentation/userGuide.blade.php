@extends('layouts.sidebar')

@section('title', ':: Guide d\'utilisation ::')

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

    /* Step Cards */
    .step-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 20px;
        height: 100%;
        transition: all 0.3s ease;
    }

    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
    }

    .step-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 12px;
    }

    .step-card ol,
    .step-card ul {
        margin: 0;
        padding-left: 20px;
    }

    .step-card li {
        margin-bottom: 8px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Section Titles */
    .section-title-h5 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title-h5 i {
        color: var(--primary-color);
    }

    .section-title-h6 {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 12px;
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
            <i class="ri-book-open-line"></i>
            Guide d'utilisation
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="content-card">
                <div class="content-body">
                    <!-- Navigation par onglets -->
                    <ul class="nav nav-pills nav-fill mb-4" id="userGuideTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription" type="button" role="tab">
                                <i class="ri-login-circle-line me-2"></i>Souscription
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="unsubscription-tab" data-bs-toggle="tab" data-bs-target="#unsubscription" type="button" role="tab">
                                <i class="ri-logout-circle-line me-2"></i>Résiliation
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="validation-tab" data-bs-toggle="tab" data-bs-target="#validation" type="button" role="tab">
                                <i class="ri-shield-check-line me-2"></i>Validation
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract" type="button" role="tab">
                                <i class="ri-file-add-line me-2"></i>Contrats
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="userGuideTabsContent">
                        
                        <!-- Onglet Souscription -->
                        <div class="tab-pane fade show active" id="subscription" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-login-circle-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Processus de souscription</h5>
                                            <p class="section-subtitle">Guide complet pour souscrire un client au service Bank-to-Wallet</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-info">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Prérequis</h6>
                                            <ul class="mb-0">
                                                <li>Le client doit avoir un compte bancaire actif chez ACEP</li>
                                                <li>Le numéro de téléphone doit être enregistré chez Orange Money</li>
                                                <li>Le client doit être présent physiquement avec sa pièce d'identité</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">1</div>
                                                <h6 class="step-title">Recherche du client</h6>
                                                <ol class="mb-0">
                                                    <li>Accédez à la section <strong>Services > Souscription</strong></li>
                                                    <li>Saisissez le numéro de téléphone du client</li>
                                                    <li>Cliquez sur <strong>"Vérifier"</strong></li>
                                                    <li>Le système vérifiera l'existence du client chez Orange Money</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">2</div>
                                                <h6 class="step-title">Vérification KYC</h6>
                                                <ol class="mb-0">
                                                    <li>Comparez les informations Orange Money avec la pièce d'identité</li>
                                                    <li>Vérifiez la cohérence des données bancaires</li>
                                                    <li>Confirmez l'identité du client</li>
                                                    <li>Cliquez sur <strong>"Continuer"</strong></li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">3</div>
                                                <h6 class="step-title">Souscription</h6>
                                                <ol class="mb-0">
                                                    <li>Remplissez le formulaire de souscription</li>
                                                    <li>Générez la clé d'activation</li>
                                                    <li>Enregistrez la demande</li>
                                                    <li>La demande sera soumise à validation</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">4</div>
                                                <h6 class="step-title">Activation</h6>
                                                <ol class="mb-0">
                                                    <li>Une fois validée, activez le service</li>
                                                    <li>Le client recevra un SMS de confirmation</li>
                                                    <li>Le service sera opérationnel</li>
                                                    <li>Un contrat sera généré automatiquement</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert-modern alert-warning mt-4">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Points d'attention</h6>
                                            <ul class="mb-0">
                                                <li>Vérifiez toujours l'identité du client</li>
                                                <li>Assurez-vous que le numéro de téléphone est correct</li>
                                                <li>Ne souscrivez pas si les données ne correspondent pas</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Résiliation -->
                        <div class="tab-pane fade" id="unsubscription" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-logout-circle-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Processus de résiliation</h5>
                                            <p class="section-subtitle">Guide pour résilier un service Bank-to-Wallet</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-danger">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Important</h6>
                                            <p class="mb-0">La résiliation est irréversible. Assurez-vous que le client confirme bien sa demande.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">1</div>
                                                <h6 class="step-title">Recherche du compte</h6>
                                                <ol class="mb-0">
                                                    <li>Accédez à la section <strong>Services > Résiliation</strong></li>
                                                    <li>Saisissez le numéro de compte ou de téléphone</li>
                                                    <li>Cliquez sur <strong>"Rechercher"</strong></li>
                                                    <li>Le système affichera les informations du compte</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">2</div>
                                                <h6 class="step-title">Vérification</h6>
                                                <ol class="mb-0">
                                                    <li>Vérifiez l'identité du demandeur</li>
                                                    <li>Confirmez les informations du compte</li>
                                                    <li>Renseignez le motif de résiliation</li>
                                                    <li>Cliquez sur <strong>"Continuer"</strong></li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">3</div>
                                                <h6 class="step-title">Validation</h6>
                                                <ol class="mb-0">
                                                    <li>La demande sera soumise à validation</li>
                                                    <li>Un validateur approuvera la résiliation</li>
                                                    <li>Le service sera désactivé</li>
                                                    <li>Le client recevra une notification</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <div class="step-number">4</div>
                                                <h6 class="step-title">Confirmation</h6>
                                                <ol class="mb-0">
                                                    <li>Un SMS de confirmation sera envoyé</li>
                                                    <li>Le compte sera marqué comme résilié</li>
                                                    <li>Les transactions ne seront plus possibles</li>
                                                    <li>Un rapport de résiliation sera généré</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Validation -->
                        <div class="tab-pane fade" id="validation" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-shield-check-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Processus de validation</h5>
                                            <p class="section-subtitle">Guide pour valider les demandes de souscription et résiliation</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-success">
                                        <i class="ri-check-line"></i>
                                        <div>
                                            <h6>Rôle du validateur</h6>
                                            <p class="mb-0">En tant que validateur, vous êtes responsable de vérifier et approuver les demandes de souscription et résiliation.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <h6 class="step-title">Validation des souscriptions</h6>
                                                <ol class="mb-0">
                                                    <li>Accédez à la section <strong>Validations</strong></li>
                                                    <li>Consultez les demandes en attente</li>
                                                    <li>Vérifiez les informations du client</li>
                                                    <li>Contrôlez la cohérence des données</li>
                                                    <li>Approuvez ou refusez la demande</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <h6 class="step-title">Validation des résiliations</h6>
                                                <ol class="mb-0">
                                                    <li>Consultez les demandes de résiliation</li>
                                                    <li>Vérifiez l'identité du demandeur</li>
                                                    <li>Contrôlez le motif de résiliation</li>
                                                    <li>Vérifiez qu'aucune transaction n'est en cours</li>
                                                    <li>Approuvez ou refusez la demande</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert-modern alert-info mt-4">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Critères de validation</h6>
                                            <ul class="mb-0">
                                                <li><strong>Approuver si :</strong> Toutes les informations sont correctes et cohérentes</li>
                                                <li><strong>Refuser si :</strong> Données incohérentes, client non identifié, ou motif invalide</li>
                                                <li><strong>Commenter :</strong> Toujours justifier votre décision</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Contrats -->
                        <div class="tab-pane fade" id="contract" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-header">
                                        <div class="section-icon">
                                            <i class="ri-file-add-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title">Gestion des contrats</h5>
                                            <p class="section-subtitle">Guide pour consulter et gérer les contrats</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert-modern alert-info">
                                        <i class="ri-information-line"></i>
                                        <div>
                                            <h6>Génération automatique</h6>
                                            <p class="mb-0">Les contrats sont générés automatiquement lors de l'activation d'un service.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <h6 class="step-title">Consultation des contrats</h6>
                                                <ol class="mb-0">
                                                    <li>Accédez à la section <strong>Services > Contrats</strong></li>
                                                    <li>Recherchez par numéro de compte</li>
                                                    <li>Consultez les détails du contrat</li>
                                                    <li>Téléchargez le PDF si nécessaire</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-card">
                                                <h6 class="step-title">Informations du contrat</h6>
                                                <ul class="mb-0">
                                                    <li>Données du client</li>
                                                    <li>Informations bancaires</li>
                                                    <li>Conditions du service</li>
                                                    <li>Date de souscription</li>
                                                    <li>Clé d'activation</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert-modern alert-warning mt-4">
                                        <i class="ri-error-warning-line"></i>
                                        <div>
                                            <h6>Important</h6>
                                            <ul class="mb-0">
                                                <li>Les contrats sont légalement contraignants</li>
                                                <li>Conservez une copie pour vos archives</li>
                                                <li>Vérifiez les informations avant impression</li>
                                            </ul>
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
    const tabButtons = document.querySelectorAll('#userGuideTabs button[data-bs-toggle="tab"]');
    
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
