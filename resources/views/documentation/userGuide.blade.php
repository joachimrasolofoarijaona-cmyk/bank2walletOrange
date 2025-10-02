@extends('layouts.sidebar')

@section('title', ':: Guide d\'utilisation ::')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/documentation.css') }}">
@endpush

@section('content')
<div class="container-fluid pt-0">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card shadow-lg border-0 card-hover fade-in">
                <div class="card-header bg-gradient-primary text-white text-center d-flex align-content-center">
                    <i class="ri-book-open-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Guide d'utilisation</h4>
                </div>
                <div class="card-body bg-light">
                    
                    <!-- Navigation par onglets -->
                    <ul class="nav nav-pills nav-fill mb-4 bg-white rounded-3 p-2 shadow-sm" id="userGuideTabs" role="tablist">
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
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                                            <i class="ri-login-circle-line text-white fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-dark mb-1 fw-bold">Processus de souscription</h5>
                                            <p class="text-muted mb-0">Guide complet pour souscrire un client au service Bank-to-Wallet</p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info border-0 shadow-sm">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-information-line fs-5 text-info me-3 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading fw-bold mb-2">Prérequis</h6>
                                                <ul class="mb-0">
                                                    <li>Le client doit avoir un compte bancaire actif chez ACEP</li>
                                                    <li>Le numéro de téléphone doit être enregistré chez Orange Money</li>
                                                    <li>Le client doit être présent physiquement avec sa pièce d'identité</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                                            <span class="text-white fw-bold">1</span>
                                                        </div>
                                                        <h6 class="text-primary mb-0 fw-bold">Recherche du client</h6>
                                                    </div>
                                                    <ol class="mb-0">
                                                        <li>Accédez à la section <strong>Services > Souscription</strong></li>
                                                        <li>Saisissez le numéro de téléphone du client</li>
                                                        <li>Cliquez sur <strong>"Vérifier"</strong></li>
                                                        <li>Le système vérifiera l'existence du client chez Orange Money</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                                                            <span class="text-white fw-bold">2</span>
                                                        </div>
                                                        <h6 class="text-success mb-0 fw-bold">Vérification KYC</h6>
                                                    </div>
                                                    <ol class="mb-0">
                                                        <li>Comparez les informations Orange Money avec la pièce d'identité</li>
                                                        <li>Vérifiez la cohérence des données bancaires</li>
                                                        <li>Confirmez l'identité du client</li>
                                                        <li>Cliquez sur <strong>"Continuer"</strong></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-warning bg-gradient rounded-circle p-2 me-3">
                                                            <span class="text-white fw-bold">3</span>
                                                        </div>
                                                        <h6 class="text-warning mb-0 fw-bold">Souscription</h6>
                                                    </div>
                                                    <ol class="mb-0">
                                                        <li>Remplissez le formulaire de souscription</li>
                                                        <li>Générez la clé d'activation</li>
                                                        <li>Enregistrez la demande</li>
                                                        <li>La demande sera soumise à validation</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-info bg-gradient rounded-circle p-2 me-3">
                                                            <span class="text-white fw-bold">4</span>
                                                        </div>
                                                        <h6 class="text-info mb-0 fw-bold">Activation</h6>
                                                    </div>
                                                    <ol class="mb-0">
                                                        <li>Une fois validée, activez le service</li>
                                                        <li>Le client recevra un SMS de confirmation</li>
                                                        <li>Le service sera opérationnel</li>
                                                        <li>Un contrat sera généré automatiquement</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning border-0 shadow-sm mt-4">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-error-warning-line fs-5 text-warning me-3 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading fw-bold mb-2">Points d'attention</h6>
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
                        </div>

                        <!-- Onglet Résiliation -->
                        <div class="tab-pane fade" id="unsubscription" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-logout-circle-line me-2"></i>Processus de résiliation
                                    </h5>
                                    
                                    <div class="alert alert-danger">
                                        <h6><i class="ri-error-warning-line me-2"></i>Important</h6>
                                        <p class="mb-0">La résiliation est irréversible. Assurez-vous que le client confirme bien sa demande.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Étape 1 : Recherche du compte</h6>
                                            <ol>
                                                <li>Accédez à la section <strong>Services > Résiliation</strong></li>
                                                <li>Saisissez le numéro de compte ou de téléphone</li>
                                                <li>Cliquez sur <strong>"Rechercher"</strong></li>
                                                <li>Le système affichera les informations du compte</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Étape 2 : Vérification</h6>
                                            <ol>
                                                <li>Vérifiez l'identité du demandeur</li>
                                                <li>Confirmez les informations du compte</li>
                                                <li>Renseignez le motif de résiliation</li>
                                                <li>Cliquez sur <strong>"Continuer"</strong></li>
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Étape 3 : Validation</h6>
                                            <ol>
                                                <li>La demande sera soumise à validation</li>
                                                <li>Un validateur approuvera la résiliation</li>
                                                <li>Le service sera désactivé</li>
                                                <li>Le client recevra une notification</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Étape 4 : Confirmation</h6>
                                            <ol>
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

                        <!-- Onglet Validation -->
                        <div class="tab-pane fade" id="validation" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-shield-check-line me-2"></i>Processus de validation
                                    </h5>
                                    
                                    <div class="alert alert-success">
                                        <h6><i class="ri-check-line me-2"></i>Rôle du validateur</h6>
                                        <p class="mb-0">En tant que validateur, vous êtes responsable de vérifier et approuver les demandes de souscription et résiliation.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Validation des souscriptions</h6>
                                            <ol>
                                                <li>Accédez à la section <strong>Validations</strong></li>
                                                <li>Consultez les demandes en attente</li>
                                                <li>Vérifiez les informations du client</li>
                                                <li>Contrôlez la cohérence des données</li>
                                                <li>Approuvez ou refusez la demande</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Validation des résiliations</h6>
                                            <ol>
                                                <li>Consultez les demandes de résiliation</li>
                                                <li>Vérifiez l'identité du demandeur</li>
                                                <li>Contrôlez le motif de résiliation</li>
                                                <li>Vérifiez qu'aucune transaction n'est en cours</li>
                                                <li>Approuvez ou refusez la demande</li>
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <h6><i class="ri-information-line me-2"></i>Critères de validation</h6>
                                        <ul class="mb-0">
                                            <li><strong>Approuver si :</strong> Toutes les informations sont correctes et cohérentes</li>
                                            <li><strong>Refuser si :</strong> Données incohérentes, client non identifié, ou motif invalide</li>
                                            <li><strong>Commenter :</strong> Toujours justifier votre décision</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Contrats -->
                        <div class="tab-pane fade" id="contract" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-file-add-line me-2"></i>Gestion des contrats
                                    </h5>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="ri-information-line me-2"></i>Génération automatique</h6>
                                        <p class="mb-0">Les contrats sont générés automatiquement lors de l'activation d'un service.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Consultation des contrats</h6>
                                            <ol>
                                                <li>Accédez à la section <strong>Services > Contrats</strong></li>
                                                <li>Recherchez par numéro de compte</li>
                                                <li>Consultez les détails du contrat</li>
                                                <li>Téléchargez le PDF si nécessaire</li>
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Informations du contrat</h6>
                                            <ul>
                                                <li>Données du client</li>
                                                <li>Informations bancaires</li>
                                                <li>Conditions du service</li>
                                                <li>Date de souscription</li>
                                                <li>Clé d'activation</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning mt-3">
                                        <h6><i class="ri-error-warning-line me-2"></i>Important</h6>
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
