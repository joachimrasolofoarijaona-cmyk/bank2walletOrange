@extends('layouts.sidebar')

@section('title', ':: Résiliation ::')

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

    /* Search Card */
    .search-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .search-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .search-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .search-card-body {
        padding: 24px;
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

    .form-select {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-select:focus {
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

    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        background: transparent;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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

    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-danger:hover:not(:disabled) {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-danger:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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

    .alert-modern.alert-success {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border-left: 4px solid #198754;
    }

    .alert-modern.alert-info {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border-left: 4px solid #0d6efd;
    }

    .alert-modern.alert-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    /* Table Styles */
    .results-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .results-card .table-responsive {
        padding: 0 24px 24px 24px;
    }

    .modern-table {
        margin: 0;
        font-size: 14px;
    }

    .modern-table thead {
        background: var(--bg-light);
    }

    .modern-table thead th {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 2px solid var(--border-color);
    }

    .modern-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: var(--bg-light);
        transform: translateX(2px);
    }

    .modern-table tbody td {
        padding: 16px;
        font-size: 14px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* Service Badge */
    .service-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .service-badge.b2w {
        background: rgba(2, 86, 74, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(2, 86, 74, 0.2);
    }

    .service-badge.w2b {
        background: rgba(79, 201, 192, 0.1);
        color: var(--accent-color);
        border: 1px solid rgba(79, 201, 192, 0.2);
    }

    .service-badge.both {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.2);
    }

    /* Status Badge */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge.resiliated {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }

    .modal-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .modal-body {
        padding: 24px;
        background: white;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 16px 24px;
        background: white;
    }

    .info-section {
        margin-bottom: 20px;
    }

    .info-item {
        padding: 12px 16px;
        margin-bottom: 12px;
        background: var(--bg-light);
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
    }

    .info-item strong {
        color: var(--text-primary);
        font-weight: 600;
        margin-right: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-check {
        padding: 16px;
        background: var(--bg-light);
        border-radius: 8px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .form-check:hover {
        border-color: var(--primary-color);
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
            <i class="ri-close-circle-line"></i>
            Résiliation
        </h1>
    </div>

    <!-- Search Card -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="search-card">
                <div class="search-card-header">
                    <i class="ri-search-line"></i>
                    <h4>Trouver un client</h4>
                </div>
                <div class="search-card-body">
                    <form method="POST" action="{{ route('search.customer') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="msisdn" class="form-label">
                                <i class="ri-smartphone-line"></i>
                                Numéro Orange
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="msisdn" 
                                   name="msisdn" 
                                   placeholder="032**** ou 037****" 
                                   required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="ri-search-line"></i>
                                Chercher Numéro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('error') && session('error') !== '')
    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line"></i>
        <div>
            <strong>Erreur !</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line"></i>
        <div>
            <strong>Succès !</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    {{-- Display table if customer is found --}}
    @if(isset($customer) && !$customer->isEmpty())
    <div class="results-card">
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Ligne</th>
                        <th scope="col">Client</th>
                        <th scope="col">Compte Rattaché</th>
                        <th scope="col">Service</th>
                        <th scope="col">Type de compte</th>
                        <th scope="col">Date de souscription</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer as $cust)
                    <tr>
                        <td>{{ $cust->id }}</td>
                        <td>
                            <span class="value-primary">
                                <i class="ri-smartphone-line"></i>
                                {{ $cust->msisdn }}
                            </span>
                        </td>
                        <td>{{ $cust->client_lastname }} {{ $cust->client_firstname }}</td>
                        <td>
                            <code style="color: var(--primary-color);">{{ $cust->account_no }}</code>
                        </td>
                        <td>
                            @if($cust->code_service === "1")
                            <span class="service-badge b2w">
                                <i class="ri-arrow-right-line"></i>
                                Bank To Wallet
                            </span>
                            @elseif($cust->code_service === "2")
                            <span class="service-badge w2b">
                                <i class="ri-arrow-left-line"></i>
                                Wallet To Bank
                            </span>
                            @elseif($cust->code_service === "3")
                            <span class="service-badge both">
                                <i class="ri-exchange-line"></i>
                                Bank To Wallet / Wallet To Bank
                            </span>
                            @else
                            <span class="service-badge">Service Inconnu</span>
                            @endif
                        </td>
                        <td>{{ $cust->libelle }}</td>
                        <td>{{ \Carbon\Carbon::parse($cust->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($cust->account_status === "1")
                            <button type="button"
                                class="btn btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-{{ $cust->id }}">
                                <i class="ri-delete-bin-line"></i>
                                Résilier
                            </button>
                            @else
                            <span class="status-badge resiliated">
                                <i class="ri-close-line"></i>
                                Compte Résilié
                            </span>
                            @endif
                        </td>
                    </tr>

                    {{-- Modal pour chaque client --}}
                    <div class="modal fade" id="modal-{{ $cust->id }}" tabindex="-1" aria-labelledby="ModalLabel-{{ $cust->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title" id="ModalLabel-{{ $cust->id }}">
                                        <i class="ri-close-circle-line me-2"></i>Résilier un client
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('send.unsubscribe.validation') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="msisdn" value="{{ $cust->msisdn }}">
                                        <input type="hidden" name="account_no" value="{{ $cust->account_no }}">
                                        <input type="hidden" name="libelle" value="{{ $cust->libelle }}">

                                        <div class="alert-modern alert-warning">
                                            <i class="ri-alert-line"></i>
                                            <div>
                                                <strong>Attention</strong><br>
                                                Prière de bien confirmer la ligne concernée !
                                            </div>
                                        </div>

                                        <div class="info-section">
                                            <div class="info-item">
                                                <strong>Numéro Orange :</strong> {{ $cust->msisdn }}
                                            </div>
                                            <div class="info-item">
                                                <strong>Compte :</strong> {{ $cust->account_no }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="origin-{{ $cust->id }}" class="form-label">
                                                <i class="ri-building-line"></i>
                                                Origine de résiliation
                                            </label>
                                            <select name="origin" id="origin-{{ $cust->id }}" class="form-select">
                                                <option value="Orange">1- Orange</option>
                                                <option value="Bank">2- ACEP</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="motif-{{ $cust->id }}" class="form-label">
                                                <i class="ri-file-list-line"></i>
                                                Motif de résiliation
                                            </label>
                                            <select name="motif" 
                                                    id="motif-{{ $cust->id }}" 
                                                    class="form-select"
                                                    onchange="showDiv('autre_motif_div-{{ $cust->id }}', this)">
                                                <option value="Changement de numéro de téléphone">Changement de numéro de téléphone</option>
                                                <option value="Insatisfaction du service">Insatisfaction du service</option>
                                                <option value="Frais jugés trop élevés">Frais jugés trop élevés</option>
                                                <option value="Utilisation d'un autre service de transfert d'argent">Utilisation d'un autre service</option>
                                                <option value="Autre">Autre (À préciser)</option>
                                            </select>
                                        </div>

                                        <div id="autre_motif_div-{{ $cust->id }}" class="form-group" style="display: none;">
                                            <label class="form-label">
                                                <i class="ri-edit-line"></i>
                                                Autre motif
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Précisez le motif" 
                                                   name="motif_autre">
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input confirm-checkbox"
                                                   id="confirm-{{ $cust->id }}"
                                                   onclick="terms_changed(this, 'submit-btn-{{ $cust->id }}')">
                                            <label class="form-check-label" for="confirm-{{ $cust->id }}">
                                                Je confirme la sélection
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit" 
                                                    id="submit-btn-{{ $cust->id }}" 
                                                    class="btn btn-danger" 
                                                    disabled>
                                                <i class="ri-check-line me-2"></i>Valider
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="ri-close-line me-2"></i>Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(isset($customer) && $customer->isEmpty())
    <div class="alert-modern alert-info">
        <i class="ri-information-line"></i>
        <div>
            <strong>Information</strong><br>
            Aucun client trouvé avec ce numéro de téléphone.
        </div>
    </div>
    @endif
</div>

<script>
    function showDiv(divId, element) {
        const div = document.getElementById(divId);
        div.style.display = (element.value === 'Autre') ? 'block' : 'none';
    }

    function terms_changed(checkbox, buttonId) {
        document.getElementById(buttonId).disabled = !checkbox.checked;
    }
</script>

@endsection
