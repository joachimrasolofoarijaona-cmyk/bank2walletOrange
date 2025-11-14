@extends('layouts.sidebar')

@section('title', ':: Souscription ::')

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

    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .content-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .content-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .content-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        text-transform: none;
    }

    .content-card-body {
        padding: 24px;
    }

    /* Condition List */
    .condition-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
        margin-bottom: 16px;
        background: var(--bg-light);
        border-radius: 12px;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .condition-item:hover {
        background: white;
        box-shadow: var(--card-shadow);
        transform: translateX(4px);
    }

    .condition-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        font-size: 28px;
        flex-shrink: 0;
    }

    .condition-text {
        flex: 1;
        font-size: 14px;
        color: var(--text-primary);
        line-height: 1.6;
    }

    .condition-text strong {
        color: var(--accent-color);
        font-weight: 600;
    }

    /* Form Styles */
    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
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

    /* Buttons - Outline Style */
    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 86, 74, 0.3);
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

    .alert-modern i {
        font-size: 24px;
    }

    /* Warning Footer */
    .warning-footer {
        background: rgba(255, 193, 7, 0.1);
        border-top: 1px solid rgba(255, 193, 7, 0.2);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        border-radius: 0 0 12px 12px;
    }

    .warning-footer i {
        color: #ffc107;
        font-size: 32px;
        flex-shrink: 0;
    }

    .warning-footer h5 {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.5;
    }

    /* Container spacing */
    .container-fluid {
        padding: 0 15px;
    }

    .section-spacing {
        margin-bottom: 24px;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="ri-links-line"></i>
            Souscription
        </h1>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line"></i>
        <div>
            <strong>Erreur !</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="row section-spacing">
        <!-- Conditions Card -->
        <div class="col-lg-6 col-md-12 col-xs-12">
            <div class="content-card">
                <div class="content-card-header">
                    <i class="ri-bookmark-line"></i>
                    <h4>Conditions de souscription</h4>
                </div>
                <div class="content-card-body">
                    <div class="condition-item">
                        <div class="condition-icon">
                            <i class="ri-smartphone-line"></i>
                                        </div>
                        <div class="condition-text">
                                                            Avoir un numéro Orange actif et souscrit au service Orange Money en son nom ainsi qu'un compte ouvert ACEP avec numéro Orange enregistré dans la base MUSONI
                                                        </div>
                                                    </div>

                    <div class="condition-item">
                        <div class="condition-icon">
                            <i class="ri-key-2-line"></i>
                                                        </div>
                        <div class="condition-text">
                            Disposer d'une clé d'activation fournie par Orange après avoir fait le code <strong>#144*4#</strong> puis <i class="ri-phone-line"></i>
                                                        </div>
                                                    </div>

                    <div class="condition-item">
                        <div class="condition-icon">
                            <i class="ri-bank-line"></i>
                                                        </div>
                        <div class="condition-text">
                                                            Fournir ce code à l'agent bancaire pour procéder à l'étape suivante de la souscription
                                                        </div>
                                                    </div>
                                        </div>
                                    </div>
                                </div>

        <!-- Form Card -->
                            <div class="col-lg-6 col-md-12 col-xs-12">
            <div class="content-card">
                <div class="content-card-header">
                                            <i class="ri-survey-line"></i>
                    <h4>Formulaire de souscription</h4>
                                        </div>
                <div class="content-card-body">
                    <form action="{{ route('send.subscribe') }}" method="POST" accept-charset="UTF-8">
                                            @csrf
                        <div class="mb-4">
                            <label for="msisdn" class="form-label">
                                <i class="ri-smartphone-line"></i>
                                Numéro Orange
                                                </label>
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Ex: 0321234567" 
                                   value="" 
                                   name="msisdn" 
                                   id="msisdn" 
                                   required>
                                            </div>

                        <div class="mb-4">
                            <label for="key" class="form-label">
                                <i class="ri-key-2-line"></i>
                                Clé d'activation
                                                </label>
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Clé d'activation" 
                                   value="" 
                                   name="key" 
                                   id="key" 
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="ri-send-plane-line"></i>
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
                <div class="warning-footer">
                    <i class="ri-error-warning-line"></i>
                    <h5>
                        Il est impératif que le client dispose de ces informations pour pouvoir passer à la suite des opérations.
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
