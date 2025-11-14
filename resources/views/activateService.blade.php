@extends('layouts.sidebar')

@section('title', ':: Service Actif ::')

@section('content')
@php
$colorText = "#212529";
$colorPrimary = "#00574A";
$colorAccent = "#50c2bb";
@endphp

<div class="container-fluid pt-0">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                {{-- session error --}}
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur !</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

                {{-- session success --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

                <div class="card-header bg-gradient-primary text-white text-center d-flex align-content-center justify-content-center">
                    <i class="ri-check-double-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Service Actif</h4>
                </div>

                <div class="card-body bg-light">
                    @if(isset($msisdn))
                    {{-- Message de succès --}}
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid {{ $colorAccent }};">
                        <i class="ri-checkbox-circle-line fs-3 me-3" style="color: {{ $colorAccent }};"></i>
                        <div>
                            <h5 class="mb-1 fw-bold">Le service a été activé avec succès !</h5>
                            <p class="mb-0">Votre demande de souscription a été traitée et le service est maintenant actif.</p>
                        </div>
                    </div>

                    {{-- Détails du service --}}
                    <div class="row g-4">
                        {{-- Informations Orange Money --}}
                        <div class="col-lg-6 col-md-12">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header d-flex align-items-center" style="background: {{ $colorAccent }}; color:#fff;">
                                    <i class="ri-smartphone-line fs-4 me-2"></i>
                                    <h5 class="text-uppercase mb-0 fw-bold">Informations Orange Money</h5>
                                </div>
                                <div class="card-body" style="background: #fff;">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-phone-line me-2" style="color: {{ $colorPrimary }};"></i>Numéro de ligne</span>
                                            <span class="badge rounded-pill" style="background: {{ $colorAccent }}; color:#fff;">{{ $msisdn }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-user-line me-2" style="color: {{ $colorPrimary }};"></i>Alias</span>
                                            <code class="fw-bold" style="color: {{ $colorPrimary }};">{{ $alias }}</code>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-code-line me-2" style="color: {{ $colorPrimary }};"></i>Code Service</span>
                                            <span class="badge rounded-pill" style="background: {{ $colorPrimary }}; color:#fff;">{{ $code_service }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-file-text-line me-2" style="color: {{ $colorPrimary }};"></i>Libellé</span>
                                            <span style="color: {{ $colorText }};">{{ $libelle }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-money-dollar-circle-line me-2" style="color: {{ $colorPrimary }};"></i>Devise</span>
                                            <span class="badge rounded-pill" style="background: {{ $colorAccent }}; color:#fff;">{{ $currency }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-key-line me-2" style="color: {{ $colorPrimary }};"></i>Clé d'activation</span>
                                            <code class="fw-bold" style="color: {{ $colorPrimary }};">{{ $key }}</code>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Informations Client --}}
                        <div class="col-lg-6 col-md-12">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header d-flex align-items-center" style="background: {{ $colorPrimary }}; color:#fff;">
                                    <i class="ri-user-3-line fs-4 me-2"></i>
                                    <h5 class="text-uppercase mb-0 fw-bold">Informations Client</h5>
                                </div>
                                <div class="card-body" style="background: #fff;">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-id-card-line me-2" style="color: {{ $colorPrimary }};"></i>Client ID</span>
                                            <span style="color: {{ $colorText }};">{{ $customer_id }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-file-paper-line me-2" style="color: {{ $colorPrimary }};"></i>CIN</span>
                                            <code class="fw-bold" style="color: {{ $colorPrimary }};">{{ $customer_cin }}</code>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-user-line me-2" style="color: {{ $colorPrimary }};"></i>Prénom</span>
                                            <span style="color: {{ $colorText }};">{{ $customer_firtsname }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-user-line me-2" style="color: {{ $colorPrimary }};"></i>Nom</span>
                                            <span style="color: {{ $colorText }};">{{ $customer_lastname }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <span class="fw-semibold" style="color: {{ $colorText }};"><i class="ri-calendar-line me-2" style="color: {{ $colorPrimary }};"></i>Date de naissance</span>
                                            <span style="color: {{ $colorText }};">{{ $customer_birthdate }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message de remerciement --}}
                    <div class="text-center mt-4">
                        <div class="alert alert-light border-0 d-inline-block px-4 py-3" style="background: {{ $colorAccent }}20;">
                            
                            <span class="fw-semibold" style="color: {{ $colorPrimary }};">Merci de votre confiance.</span>
                        </div>
                    </div>
                    @else
                    {{-- Aucun service trouvé --}}
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="ri-alert-line fs-3 me-3"></i>
                        <div>
                            <h5 class="mb-1 fw-bold">Aucun service actif trouvé</h5>
                            <p class="mb-0">Aucune information de service n'a été trouvée. Veuillez contacter le support si vous pensez qu'il s'agit d'une erreur.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection