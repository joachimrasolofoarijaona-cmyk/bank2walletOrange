@extends('layouts.sidebar')

@section('title', 'Souscription')

@section('content')
@php
use Illuminate\Support\Facades\DB;
@endphp

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

    /* Subscription Card */
    .subscription-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .subscription-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .subscription-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .subscription-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .subscription-card-body {
        padding: 24px;
    }

    /* Form Styles */
    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: var(--primary-color);
    }

    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(2, 86, 74, 0.1);
        outline: none;
    }

    .form-control[readonly] {
        background: var(--bg-light);
        border-color: var(--accent-color);
        color: var(--text-primary);
    }

    /* Service Code Card */
    .service-code-card {
        background: white;
        border: 2px solid var(--accent-color);
        border-radius: 12px;
        padding: 20px;
    }

    .form-check {
        padding: 16px;
        margin-bottom: 12px;
        background: var(--bg-light);
        border-radius: 8px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .form-check:hover {
        background: white;
        border-color: var(--accent-color);
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        border: 2px solid var(--primary-color);
        margin-top: 2px;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-check-label i {
        color: var(--primary-color);
    }

    /* Account Selection */
    .account-item {
        padding: 20px;
        margin-bottom: 16px;
        background: white;
        border: 2px solid var(--accent-color);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .account-item:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }

    .account-item.disabled {
        background: var(--bg-light);
        border-color: var(--border-color);
        opacity: 0.6;
    }

    .account-item.disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .account-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .account-number {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary-color);
        margin-right: 12px;
    }

    .account-product {
        font-size: 14px;
        color: var(--text-secondary);
    }

    /* Status Badges */
    .account-status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .account-status-badge.linked {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .account-status-badge.resiliated {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .account-status-badge.available {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    /* Buttons - Outline Style */
    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
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

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 86, 74, 0.3);
    }

    .btn-outline-secondary {
        border: 2px solid var(--text-secondary);
        color: var(--text-secondary);
        background: transparent;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: var(--text-secondary);
        color: white;
        transform: translateY(-2px);
    }

    /* Alerts */
    .alert-modern {
        border-radius: 8px;
        border: none;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-modern.alert-danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    .alert-modern.alert-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    /* Info Footer */
    .info-footer {
        background: rgba(79, 201, 192, 0.1);
        border-top: 1px solid var(--accent-color);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .info-footer i {
        color: var(--accent-color);
        font-size: 24px;
        flex-shrink: 0;
    }

    .info-footer p {
        font-size: 14px;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.6;
    }

    /* Error Card */
    .error-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .error-card-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .error-card-header h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .error-card-body {
        padding: 24px;
    }

    /* Container spacing */
    .container-fluid {
        padding: 0 15px;
    }
</style>

@if(session()->has('error'))
<div class="container-fluid">
    <div class="error-card">
        <div class="error-card-header">
            <i class="ri-error-warning-line"></i>
            <h3>Erreur de souscription</h3>
        </div>
        <div class="error-card-body">
            <div class="alert-modern alert-danger">
                <i class="ri-error-warning-line"></i>
                <div>{{ session('error') }}</div>
            </div>
            <div class="text-end">
                <a href="{{ route('show.subscribe') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="ri-send-plane-line"></i>
            Formulaire de souscription
        </h1>
    </div>

    <!-- Subscription Card -->
    <div class="subscription-card">
        <div class="subscription-card-header">
            <i class="ri-file-list-line"></i>
            <h4>Détails de la souscription</h4>
        </div>
        <div class="subscription-card-body">
            <form action="{{ route('send.subscription.validation') }}" method="POST" accept-charset="UTF-8">
                @csrf
                
                {{-- Numéro de téléphone --}}
                <div class="mb-4">
                    <label class="form-label">
                        <i class="ri-smartphone-line"></i>
                        Numéro de téléphone
                    </label>
                    <input class="form-control" 
                           type="text" 
                           name="msisdn" 
                           value="{{ $msisdn ?? '' }}" 
                           readonly>
                </div>

                {{-- Clé d'activation --}}
                <div class="mb-4">
                    <label class="form-label">
                        <i class="ri-key-line"></i>
                        Clé d'activation
                    </label>
                    <input class="form-control" 
                           type="text" 
                           name="key" 
                           value="{{ $key ?? '' }}" 
                           readonly>
                </div>

                {{-- Code de service --}}
                <div class="mb-4">
                    <label class="form-label">
                        <i class="ri-service-line"></i>
                        Code de service
                    </label>
                    <div class="service-code-card">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="code_service" 
                                   id="b2w" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label" for="b2w">
                                <i class="ri-arrow-right-line"></i>
                                Bank To Wallet
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="code_service" 
                                   id="w2b" 
                                   value="2">
                            <label class="form-check-label" for="w2b">
                                <i class="ri-arrow-left-line"></i>
                                Wallet To Bank
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="code_service" 
                                   id="all" 
                                   value="3">
                            <label class="form-check-label" for="all">
                                <i class="ri-exchange-line"></i>
                                Bank To Wallet / Wallet To Bank
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Liste des comptes --}}
                <div class="mb-4">
                    <label class="form-label">
                        <i class="ri-bank-line"></i>
                        Liste des comptes à lier
                    </label>

                    @if(isset($customer_account) && count($customer_account) > 0)
                        <div>
                            @foreach($customer_account as $account)
                                @php
                                    $account_subscribed = DB::table('subscription')
                                        ->select('account_status')
                                        ->where('account_no', $account['accountNo'])
                                        ->latest('updated_at')
                                        ->first();

                                    $disable = ($account_subscribed && $account_subscribed->account_status === "1") ? 'disabled' : '';
                                @endphp

                                <div class="account-item {{ $disable ? 'disabled' : '' }}">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="accounts" 
                                               id="account-{{ $account['accountNo'] }}" 
                                               value="{{ $account['accountNo'] }}" 
                                               {{ $disable }}
                                               style="cursor: {{ $disable ? 'not-allowed' : 'pointer' }};">
                                        <label class="form-check-label w-100" 
                                               for="account-{{ $account['accountNo'] }}" 
                                               style="cursor: {{ $disable ? 'not-allowed' : 'pointer' }};">
                                            <div class="account-info">
                                                <div>
                                                    <span class="account-number">{{ $account['accountNo'] }}</span>
                                                    <span class="account-product">- {{ $account['productName'] }}</span>
                                                </div>
                                                <div>
                                                    @if($account_subscribed && $account_subscribed->account_status === "1")
                                                        <span class="account-status-badge linked">
                                                            <i class="ri-links-line"></i>
                                                            Compte déjà lié
                                                        </span>
                                                    @elseif($account_subscribed && $account_subscribed->account_status === "0")
                                                        <span class="account-status-badge resiliated">
                                                            <i class="ri-close-circle-line"></i>
                                                            Compte résilié
                                                        </span>
                                                    @else
                                                        <span class="account-status-badge available">
                                                            <i class="ri-checkbox-circle-line"></i>
                                                            Disponible
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert-modern alert-warning">
                            <i class="ri-alert-line"></i>
                            <div>
                                <strong>Aucun compte disponible</strong>
                                <p class="mb-0">Aucun compte disponible pour ce client.</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Bouton Suivant --}}
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="ri-arrow-right-line"></i>
                        Suivant
                    </button>
                </div>
            </form>
        </div>
        <div class="info-footer">
            <i class="ri-information-line"></i>
            <p class="mb-0">
                Il est impératif que le client dispose de ces informations pour pouvoir passer à la suite des opérations.
            </p>
        </div>
    </div>
</div>
@endif

@endsection
