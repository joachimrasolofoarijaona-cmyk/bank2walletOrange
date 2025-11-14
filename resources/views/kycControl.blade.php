@extends('layouts.sidebar')

@section('title', 'Souscription')

@section('header')
Bienvenue {{ session('firstname') }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: #02564A;
        --accent-color: #4FC9C0;
        --bg-light: #F8F9FA;
        --text-primary: #212529;
        --text-secondary: #6C757D;
        --border-color: #E9ECEF;
        --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        --card-shadow-hover: 0 4px 16px rgba(0, 0, 0, 0.08);
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

    /* KYC Card */
    .kyc-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .kyc-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kyc-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .kyc-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    /* Warning Alert */
    .warning-banner {
        background: rgba(79, 201, 192, 0.1);
        border: 1px solid rgba(79, 201, 192, 0.2);
        border-left: 4px solid var(--accent-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .warning-banner i {
        color: var(--accent-color);
        font-size: 28px;
        flex-shrink: 0;
    }

    .warning-banner p {
        font-size: 14px;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.6;
    }

    /* KYC Info Cards */
    .kyc-info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .kyc-info-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }

    .kyc-info-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kyc-info-card-header.orange {
        background: linear-gradient(135deg, var(--accent-color) 0%, #3db5a8 100%);
        color: white;
    }

    .kyc-info-card-header.primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        color: white;
    }

    .kyc-info-card-header i {
        font-size: 24px;
    }

    .kyc-info-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .kyc-info-card-body {
        padding: 24px;
    }

    .kyc-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .kyc-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .kyc-info-item:last-child {
        border-bottom: none;
    }

    .kyc-info-label {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 180px;
        font-size: 14px;
    }

    .kyc-info-value {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .kyc-info-value code {
        background: var(--bg-light);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        font-family: 'Courier New', monospace;
    }

    .kyc-info-card-footer {
        padding: 16px 24px;
        background: var(--bg-light);
        border-top: 1px solid var(--border-color);
        text-align: center;
    }

    .kyc-info-card-footer small {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .kyc-info-card-footer i {
        color: var(--primary-color);
    }

    /* Alert Styles */
    .alert-modern {
        border-radius: 12px;
        padding: 24px;
        margin: 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .alert-modern.danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
        border-left: 4px solid #dc3545;
    }

    .alert-modern.success {
        background: rgba(25, 135, 84, 0.1);
        border: 1px solid rgba(25, 135, 84, 0.2);
        border-left: 4px solid #198754;
    }

    .alert-modern.warning {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
        border-left: 4px solid #ffc107;
    }

    .alert-modern i {
        font-size: 32px;
        flex-shrink: 0;
    }

    .alert-modern.danger i {
        color: #dc3545;
    }

    .alert-modern.success i {
        color: #198754;
    }

    .alert-modern.warning i {
        color: #ffc107;
    }

    .alert-content h5 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .alert-content.danger h5 {
        color: #dc3545;
    }

    .alert-content.success h5 {
        color: #198754;
    }

    .alert-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Action Buttons */
    .action-buttons {
        padding: 24px;
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        background: transparent;
        font-weight: 500;
        padding: 14px 32px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: none;
        font-weight: 500;
        padding: 14px 32px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }

    .btn-primary:hover {
        background: #033d35;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 86, 74, 0.3);
    }

    /* Container spacing */
    .container-fluid {
        padding: 0 15px;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="ri-equalizer-line"></i>
            Contrôle KYC
        </h1>
    </div>

    <!-- KYC Card -->
    <div class="kyc-card">
        <div class="kyc-card-header">
            <i class="ri-shield-check-line"></i>
            <h4>Vérification des données client</h4>
        </div>

        <!-- Warning Banner -->
        <div class="warning-banner">
            <i class="ri-alert-line"></i>
            <p>
                <strong>Attention :</strong> Prière de bien vérifier les données client avant de soumettre pour validation. Les données non valides ne seront pas traitées.
            </p>
        </div>

        <!-- KYC Information Cards -->
        <div class="row g-4" style="padding: 0 24px 24px 24px;">
            <!-- Orange Money KYC -->
            <div class="col-lg-6 col-md-12">
                <div class="kyc-info-card">
                    <div class="kyc-info-card-header orange">
                        <i class="ri-smartphone-line"></i>
                        <h4>Orange Money KYC</h4>
                    </div>
                    <div class="kyc-info-card-body">
                        @isset($data)
                        <ul class="kyc-info-list">
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Numéro :</span>
                                <span class="kyc-info-value">{{ $msisdn }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Nom :</span>
                                <span class="kyc-info-value">{{ $om_lastname }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Prénom :</span>
                                <span class="kyc-info-value">{{ $om_firtsname }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Date de naissance :</span>
                                <span class="kyc-info-value">{{ $om_birthdate }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">CIN :</span>
                                <code class="kyc-info-value">{{ $om_cin }}</code>
                            </li>
                        </ul>
                        @else
                        <div class="alert-modern warning">
                            <i class="ri-alert-line"></i>
                            <div class="alert-content">
                                <h5>Aucune donnée disponible</h5>
                                <p>Aucune donnée Orange retournée.</p>
                            </div>
                        </div>
                        @endisset
                    </div>
                    <div class="kyc-info-card-footer">
                        <small>
                            <i class="ri-information-line"></i>
                            Les informations données par Orange Money
                        </small>
                    </div>
                </div>
            </div>

            <!-- ACEP KYC -->
            <div class="col-lg-6 col-md-12">
                <div class="kyc-info-card">
                    <div class="kyc-info-card-header primary">
                        <i class="ri-bank-fill"></i>
                        <h4>ACEP KYC</h4>
                    </div>
                    <div class="kyc-info-card-body">
                        @isset($customer_firstname)
                        <ul class="kyc-info-list">
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Numéro :</span>
                                <span class="kyc-info-value">{{ $customer_mobile_no }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Nom :</span>
                                <span class="kyc-info-value">{{ $customer_lastname }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Prénom :</span>
                                <span class="kyc-info-value">{{ $customer_firstname }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">Date de naissance :</span>
                                <span class="kyc-info-value">{{ $customer_date_of_birth }}</span>
                            </li>
                            <li class="kyc-info-item">
                                <span class="kyc-info-label">CIN :</span>
                                <code class="kyc-info-value">{{ $customer_cin }}</code>
                            </li>
                        </ul>
                        @else
                        <div class="alert-modern warning">
                            <i class="ri-alert-line"></i>
                            <div class="alert-content">
                                <h5>Aucune donnée disponible</h5>
                                <p>Aucune donnée ACEP retournée.</p>
                            </div>
                        </div>
                        @endisset
                    </div>
                    <div class="kyc-info-card-footer">
                        <small>
                            <i class="ri-information-line"></i>
                            Les informations données par Musoni
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Section -->
        @if($verified_cin != $customer_cin)
        <div style="padding: 0 24px 24px 24px;">
            <div class="alert-modern danger">
                <i class="ri-error-warning-line"></i>
                <div class="alert-content danger">
                    <h5>Erreur de correspondance</h5>
                    <p>Les Cartes d'Identité Nationale ne correspondent pas ! Merci de vérifier.</p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('show.subscribe') }}" class="btn btn-outline-danger">
                    <i class="ri-close-line"></i>
                    Annuler demande
                </a>
            </div>
        </div>
        @else
        <div style="padding: 0 24px 24px 24px;">
            <div class="alert-modern success">
                <i class="ri-checkbox-circle-line"></i>
                <div class="alert-content success">
                    <h5>Identité vérifiée</h5>
                    <p>Vous pouvez poursuivre la souscription.</p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('show.subscribe') }}" class="btn btn-outline-danger">
                    <i class="ri-close-line"></i>
                    Annuler
                </a>
                <form action="{{ route('confirm.sub') }}" method="POST" class="d-inline">
                    @csrf
                    <div class="form-group d-none">
                        <input type="text" class="form-control" name="msisdn" id="msisdn" value="{{ $msisdn }}">
                        <input type="text" class="form-control" name="key" id="key" value="{{ $key }}" required>
                        <input type="text" class="form-control" name="omCin" id="omCin" value="{{ $om_cin }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-arrow-right-line"></i>
                        Poursuivre
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
