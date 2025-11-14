@extends('layouts.sidebar')

@section('title', ':: Recherche Client ::')

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

    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin-top: 8px;
    }

    /* Search Card */
    .search-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 24px;
        margin-bottom: 24px;
    }

    .search-form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .search-input {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
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

    /* Results Card */
    .results-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .results-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
    }

    .results-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .results-card-title i {
        color: var(--primary-color);
    }

    /* Table Styles */
    .modern-table {
        margin: 0;
    }

    .table-responsive {
        padding: 0;
        margin: 0;
    }

    .results-card .table-responsive {
        padding: 0 24px 24px 24px;
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

    /* Client ID Link */
    .client-id-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .client-id-link:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge.active {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .status-badge.inactive {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    /* Office Badge */
    .office-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 16px 24px;
    }

    .info-section {
        margin-bottom: 24px;
    }

    .info-section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border-color);
    }

    .info-table {
        margin: 0;
    }

    .info-table td {
        padding: 10px 0;
        border: none;
        font-size: 14px;
    }

    .info-table td:first-child {
        font-weight: 600;
        color: var(--text-secondary);
        width: 40%;
    }

    .info-table td:last-child {
        color: var(--text-primary);
    }

    .info-table code {
        background: var(--bg-light);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        color: var(--primary-color);
        border: 1px solid var(--border-color);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    /* Container spacing */
    .container-fluid {
        padding: 0 15px;
    }

    /* Results section spacing */
    .results-section {
        margin-top: 24px;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--border-color);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 16px;
        margin: 0;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="ri-search-line"></i>
            Recherche de Client
        </h1>
        <p class="page-subtitle">Recherchez un client par son numéro de ligne Orange Money</p>
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

                @if(session('success'))
    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line"></i>
        <div>
                    <strong>Succès !</strong> {{ session('success') }}
        </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

    <!-- Search Card -->
    <div class="search-card">
        <form action="{{ route('client.search.post') }}" method="POST">
                        @csrf
            <div class="row g-3">
                            <div class="col-md-8">
                    <label for="msisdn" class="search-form-label">
                        <i class="ri-phone-line me-2"></i>Numéro de ligne
                    </label>
                                <input type="text" 
                           class="form-control search-input" 
                                       id="msisdn" 
                                       name="msisdn" 
                                       placeholder="Ex: 0321234567" 
                                       value="{{ $msisdn ?? '' }}"
                                       required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="ri-search-line"></i>
                        Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
    </div>

    <!-- Results Card -->
    @if(isset($clients) && $clients->count() > 0)
    <div class="results-card results-section">
        <div class="results-card-header">
            <h5 class="results-card-title">
                <i class="ri-user-search-line"></i>
                Résultats de la recherche ({{ $clients->count() }})
            </h5>
        </div>
                    <div class="table-responsive">
            <table class="table modern-table" id="clientsTable">
                <thead>
                                <tr>
                        <th scope="col">ID Client</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénoms</th>
                                    <th scope="col">Numéro de ligne</th>
                                    <th scope="col">Agence</th>
                                    <th scope="col">Compte</th>
                                    <th scope="col">Libellé</th>
                                    <th scope="col">Date souscription</th>
                                </tr>
                            </thead>
                <tbody>
                                @foreach($clients as $client)
                                @php
                                $isActive = $client->account_status == "1";
                                @endphp
                    <tr>
                        <td>
                                        <a href="#" 
                               class="client-id-link" 
                                           onclick="showClientDetails({{ json_encode($client) }}); return false;">
                                <i class="ri-hashtag"></i>
                                {{ $client->client_id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($isActive)
                            <span class="status-badge active">
                                <i class="ri-check-line"></i>
                                Actif
                                        </span>
                                        @else
                            <span class="status-badge inactive">
                                <i class="ri-close-line"></i>
                                Inactif
                                        </span>
                                        @endif
                                    </td>
                        <td>{{ $client->client_lastname ?? 'N/A' }}</td>
                        <td>{{ $client->client_firstName ?? 'N/A' }}</td>
                        <td>
                            <span style="color: var(--accent-color); font-weight: 500;">
                                            {{ $client->msisdn ?? $client->mobile_no ?? 'N/A' }}
                                        </span>
                                    </td>
                        <td>
                            <span class="office-badge">
                                <i class="ri-building-2-line"></i>
                                {{ $client->officeName ?? 'N/A' }}
                                        </span>
                                    </td>
                        <td>
                            <span style="color: var(--accent-color); font-weight: 500;">
                                {{ $client->account_no ?? 'N/A' }}
                            </span>
                                    </td>
                        <td>{{ $client->libelle ?? 'N/A' }}</td>
                        <td>
                                        @if($client->date_sub)
                                        {{ \Carbon\Carbon::parse($client->date_sub)->format('d/m/Y') }}
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    @elseif(isset($clients) && $clients->count() == 0)
    <div class="results-card results-section">
        <div class="empty-state">
            <i class="ri-search-line"></i>
            <p>Aucun résultat trouvé</p>
        </div>
    </div>
    @endif
</div>

<!-- Modal pour afficher les détails complets -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientDetailsModalLabel">
                    <i class="ri-user-line"></i>
                    Détails de la Souscription
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div id="clientDetailsContent">
                    {{-- Le contenu sera injecté par JavaScript --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showClientDetails(client) {
        const modal = new bootstrap.Modal(document.getElementById('clientDetailsModal'));
        const content = document.getElementById('clientDetailsContent');
        
        const statusBadge = client.account_status == "1" 
            ? '<span class="status-badge active"><i class="ri-check-line"></i> Actif</span>'
            : '<span class="status-badge inactive"><i class="ri-close-line"></i> Inactif</span>';
        
        const dateSub = client.date_sub 
            ? new Date(client.date_sub).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : 'N/A';
        
        const dateDob = client.client_dob 
            ? new Date(client.client_dob).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : 'N/A';

        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <h6 class="info-section-title">
                            <i class="ri-user-line me-2"></i>Informations Client
                        </h6>
                        <table class="table info-table">
                            <tr>
                                <td>ID Client:</td>
                                <td><strong>${client.client_id || 'N/A'}</strong></td>
                        </tr>
                        <tr>
                                <td>Nom:</td>
                            <td>${client.client_lastname || 'N/A'}</td>
                        </tr>
                        <tr>
                                <td>Prénoms:</td>
                            <td>${client.client_firstName || 'N/A'}</td>
                        </tr>
                        <tr>
                                <td>CIN:</td>
                            <td>${client.client_cin || 'N/A'}</td>
                        </tr>
                        <tr>
                                <td>Date de naissance:</td>
                            <td>${dateDob}</td>
                        </tr>
                    </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section">
                        <h6 class="info-section-title">
                            <i class="ri-file-list-line me-2"></i>Informations Souscription
                        </h6>
                        <table class="table info-table">
                            <tr>
                                <td>Statut:</td>
                            <td>${statusBadge}</td>
                        </tr>
                        <tr>
                                <td>Numéro de ligne:</td>
                                <td><strong style="color: var(--accent-color);">${client.msisdn || client.mobile_no || 'N/A'}</strong></td>
                        </tr>
                        <tr>
                                <td>Compte:</td>
                                <td><strong style="color: var(--accent-color);">${client.account_no || 'N/A'}</strong></td>
                        </tr>
                        <tr>
                                <td>Libellé:</td>
                            <td>${client.libelle || 'N/A'}</td>
                        </tr>
                        <tr>
                                <td>Code service:</td>
                                <td><code>${client.code_service || 'N/A'}</code></td>
                        </tr>
                        <tr>
                                <td>Date souscription:</td>
                            <td>${dateSub}</td>
                        </tr>
                    </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="info-section">
                        <h6 class="info-section-title">
                            <i class="ri-settings-3-line me-2"></i>Informations Techniques
                        </h6>
                        <table class="table info-table">
                            <tr>
                                <td style="width: 30%;">Alias:</td>
                            <td><code>${client.alias || 'N/A'}</code></td>
                        </tr>
                        <tr>
                                <td>Clé d'activation:</td>
                            <td><code>${client.key || 'N/A'}</code></td>
                        </tr>
                        <tr>
                                <td>Agence:</td>
                                <td><span class="office-badge"><i class="ri-building-2-line"></i> ${client.officeName || 'N/A'}</span></td>
                        </tr>
                        <tr>
                                <td>Agent:</td>
                            <td>${client.bank_agent || 'N/A'}</td>
                        </tr>
                    </table>
                    </div>
                </div>
            </div>
        `;
        
        modal.show();
    }

    $(document).ready(function() {
        $('#clientsTable').DataTable({
            paging: true,
            pageLength: 10,
            info: true,
            lengthChange: true,
            searching: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                // Réappliquer les styles après le rendu de DataTables
                $('.status-badge, .office-badge, .client-id-link').each(function() {
                    $(this).css('display', 'inline-flex');
                });
            }
        });
    });
</script>

@endsection
