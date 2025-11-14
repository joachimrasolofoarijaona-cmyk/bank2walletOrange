@extends('layouts.sidebar')

@section('title', ':: Service Actif ::')

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

    /* Success Alert */
    .success-alert {
        background: rgba(25, 135, 84, 0.1);
        border: 1px solid rgba(25, 135, 84, 0.2);
        border-left: 4px solid #198754;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .success-alert i {
        color: #198754;
        font-size: 32px;
        flex-shrink: 0;
    }

    .success-alert-content h5 {
        font-size: 18px;
        font-weight: 600;
        color: #198754;
        margin-bottom: 8px;
    }

    .success-alert-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }

    .info-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-card-header.orange {
        background: linear-gradient(135deg, var(--accent-color) 0%, #3db5a8 100%);
        color: white;
    }

    .info-card-header.primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        color: white;
    }

    .info-card-header i {
        font-size: 24px;
    }

    .info-card-header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .info-card-body {
        padding: 24px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .info-list-item:last-child {
        border-bottom: none;
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
    }

    .info-label i {
        color: var(--primary-color);
    }

    .info-value {
        font-size: 14px;
        color: var(--text-primary);
    }

    .info-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .info-badge.primary {
        background: rgba(2, 86, 74, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(2, 86, 74, 0.2);
    }

    .info-badge.accent {
        background: rgba(79, 201, 192, 0.1);
        color: var(--accent-color);
        border: 1px solid rgba(79, 201, 192, 0.2);
    }

    code {
        background: var(--bg-light);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        font-family: 'Courier New', monospace;
    }

    /* Warning Alert */
    .warning-alert {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
        border-left: 4px solid #ffc107;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .warning-alert i {
        color: #ffc107;
        font-size: 32px;
        flex-shrink: 0;
    }

    .warning-alert-content h5 {
        font-size: 18px;
        font-weight: 600;
        color: #856404;
        margin-bottom: 8px;
    }

    .warning-alert-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Thank You Message */
    .thank-you-message {
        background: rgba(79, 201, 192, 0.1);
        border: 1px solid rgba(79, 201, 192, 0.2);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 24px;
    }

    .thank-you-message strong {
        color: var(--primary-color);
        font-size: 16px;
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
            <i class="ri-check-double-line"></i>
            Service Actif
        </h1>
    </div>

    @if(isset($msisdn))
    <!-- Success Alert -->
    <div class="success-alert">
        <i class="ri-checkbox-circle-line"></i>
        <div class="success-alert-content">
            <h5>Le service a été activé avec succès !</h5>
            <p>Votre demande de souscription a été traitée et le service est maintenant actif.</p>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="row g-4">
        <!-- Orange Money Information -->
        <div class="col-lg-6 col-md-12">
            <div class="info-card">
                <div class="info-card-header orange">
                    <i class="ri-smartphone-line"></i>
                    <h5>Informations Orange Money</h5>
                </div>
                <div class="info-card-body">
                    <ul class="info-list">
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-phone-line"></i>
                                Numéro de ligne
                            </span>
                            <span class="info-badge accent">{{ $msisdn }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-user-line"></i>
                                Alias
                            </span>
                            <code>{{ $alias }}</code>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-code-line"></i>
                                Code Service
                            </span>
                            <span class="info-badge primary">{{ $code_service }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-file-text-line"></i>
                                Libellé
                            </span>
                            <span class="info-value">{{ $libelle }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-money-dollar-circle-line"></i>
                                Devise
                            </span>
                            <span class="info-badge accent">{{ $currency }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-key-line"></i>
                                Clé d'activation
                            </span>
                            <code>{{ $key }}</code>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="col-lg-6 col-md-12">
            <div class="info-card">
                <div class="info-card-header primary">
                    <i class="ri-user-3-line"></i>
                    <h5>Informations Client</h5>
                </div>
                <div class="info-card-body">
                    <ul class="info-list">
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-id-card-line"></i>
                                Client ID
                            </span>
                            <span class="info-value">{{ $customer_id }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-file-paper-line"></i>
                                CIN
                            </span>
                            <code>{{ $customer_cin }}</code>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-user-line"></i>
                                Prénom
                            </span>
                            <span class="info-value">{{ $customer_firtsname }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-user-line"></i>
                                Nom
                            </span>
                            <span class="info-value">{{ $customer_lastname }}</span>
                        </li>
                        <li class="info-list-item">
                            <span class="info-label">
                                <i class="ri-calendar-line"></i>
                                Date de naissance
                            </span>
                            <span class="info-value">{{ $customer_birthdate }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Message -->
    <div class="thank-you-message">
        <strong>Merci de votre confiance.</strong>
    </div>
    @else
    <!-- Warning Alert -->
    <div class="warning-alert">
        <i class="ri-alert-line"></i>
        <div class="warning-alert-content">
            <h5>Aucun service actif trouvé</h5>
            <p>Aucune information de service n'a été trouvée. Veuillez contacter le support si vous pensez qu'il s'agit d'une erreur.</p>
        </div>
    </div>
    @endif
</div>

@endsection
