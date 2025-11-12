@extends('layouts.sidebar')

@section('title', 'Souscription')

@section('header')
Bienvenue {{ session('firstname') }}
@endsection


@section('content')
@php
    $colorText = "#212529";
    $colorPrimary = "#00574A";
    $colorAccent = "#50c2bb";
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex align-items-center" style="background: {{ $colorPrimary }}; color:#fff;">
                    <i class="ri-equalizer-line fs-4 me-2"></i>
                    <h4 class="text-uppercase mb-0 fw-bold text-white">Contrôle KYC</h4>
                </div>
                
                {{-- Alerte d'avertissement --}}
                <div class="card-body pt-3 pb-2" style="background: #f8f9fa;">
                    <div class="alert rounded-pill d-flex align-items-center justify-content-center" role="alert" style="background: {{ $colorAccent }}; color:#fff; border:none; padding: 1rem 2rem;">
                        <i class="ri-alert-line fs-3 me-3"></i>
                        <p class="mb-0 fw-normal">Prière de bien vérifier les données client avant de soumettre pour validation. Les données non valides ne seront pas traitées.</p>
                    </div>
                </div>

                <div class="card-body" style="background: #f8f9fa;">
                    <div class="container-fluid">
                        <div class="row g-4">
                            {{-- Orange Money KYC --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header d-flex align-items-center" style="background: {{ $colorAccent }}; color:#fff;">
                                        <i class="ri-smartphone-line fs-3 me-3 text-white"></i>
                                        <h4 class="text-uppercase mb-0 fw-bold">Orange Money KYC</h4>
                                    </div>
                                    <div class="card-body" style="background: #fff;">
                                        @isset($data)
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Numéro :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{$msisdn}}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Nom :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $om_lastname }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Prénom :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $om_firtsname  }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Date de naissance :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $om_birthdate }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">CIN :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em; font-weight: bold;">{{ $om_cin }}</span>
                                            </li>
                                        </ul>
                                        @else
                                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                                            <i class="ri-alert-line fs-4 me-3"></i>
                                            <div>
                                                <strong>Aucune donnée disponible</strong>
                                                <p class="mb-0">Aucune donnée Orange retournée.</p>
                                            </div>
                                        </div>
                                        @endisset
                                    </div>
                                    <div class="card-footer text-center" style="background: #f8f9fa; border-top: 1px solid #dee2e6;">
                                        <small class="text-muted">
                                            <i class="ri-information-line me-1"></i>Les informations données par Orange Money
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- ACEP KYC --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header d-flex align-items-center" style="background: {{ $colorPrimary }}; color:#fff;">
                                        <i class="ri-bank-fill fs-3 me-3"></i>
                                        <h4 class="text-uppercase mb-0 fw-bold text-white">ACEP KYC</h4>
                                    </div>
                                    <div class="card-body" style="background: #fff;">
                                        @isset($customer_firstname)
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Numéro :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $customer_mobile_no }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Nom :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $customer_lastname }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Prénom :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $customer_firstname  }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">Date de naissance :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em;">{{ $customer_date_of_birth }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="border-color: #e9ecef;">
                                                <strong style="color: {{ $colorPrimary }}; min-width: 150px;">CIN :</strong>
                                                <span style="color: {{ $colorText }}; font-size: 1.05em; font-weight: bold;">{{ $customer_cin }}</span>
                                            </li>
                                        </ul>
                                        @else
                                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                                            <i class="ri-alert-line fs-4 me-3"></i>
                                            <div>
                                                <strong>Aucune donnée disponible</strong>
                                                <p class="mb-0">Aucune donnée ACEP retournée.</p>
                                            </div>
                                        </div>
                                        @endisset
                                    </div>
                                    <div class="card-footer text-center" style="background: #f8f9fa; border-top: 1px solid #dee2e6;">
                                        <small class="text-muted">
                                            <i class="ri-information-line me-1"></i>Les informations données par Musoni
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Section de validation --}}
                @if($verified_cin != $customer_cin)
                <div class="card-footer" style="background: #f8f9fa;">
                    <div class="alert alert-danger d-flex align-items-center rounded-pill mb-4" role="alert" style="border-left: 4px solid #dc3545; padding: 1.5rem;">
                        <i class="ri-alert-line fs-2 me-3"></i>
                        <div>
                            <strong>Erreur de correspondance</strong>
                            <p class="mb-0">Les Cartes d'Identité Nationale ne correspondent pas ! Merci de vérifier.</p>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <a href="{{ route('show.subscribe') }}" class="btn btn-lg rounded-pill px-5" style="background: #dc3545; color:#fff; border:none; font-weight:bold;">
                                <i class="ri-close-line me-2"></i>Annuler demande
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="card-footer" style="background: #f8f9fa;">
                    <div class="alert alert-success d-flex align-items-center rounded-pill mb-4" role="alert" style="border-left: 4px solid #198754; padding: 1.5rem;">
                        <i class="ri-checkbox-circle-line fs-2 me-3"></i>
                        <div>
                            <strong>Identité vérifiée</strong>
                            <p class="mb-0">Vous pouvez poursuivre la souscription.</p>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-auto d-flex justify-content-center gap-3 flex-wrap">
                                <!-- Bouton Annuler -->
                                <a href="{{ route('show.subscribe') }}" class="btn btn-lg rounded-pill px-5" style="border: 2px solid #dc3545; color: #dc3545; background: transparent; font-weight:bold;">
                                    <i class="ri-close-line me-2"></i>Annuler
                                </a>

                                <!-- Formulaire avec bouton Poursuivre -->
                                <form action="{{ route('confirm.sub') }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="form-group d-none">
                                        <input type="text" class="form-control" name="msisdn" id="msisdn" value="{{$msisdn}}">
                                        <input type="text" class="form-control" name="key" id="key" value="{{$key}}" required>
                                        <input type="text" class="form-control" name="omCin" id="omCin" value="{{$om_cin}}" required>
                                    </div>
                                    <button type="submit" class="btn btn-lg rounded-pill px-5" style="background: {{ $colorPrimary }}; color:#fff; border:none; font-weight:bold;">
                                        <i class="ri-arrow-right-line me-2"></i>Poursuivre
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection