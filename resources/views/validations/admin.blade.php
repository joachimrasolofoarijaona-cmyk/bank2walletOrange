@extends('layouts.sidebar')

@section('title', ':: Validations ::')

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

    /* Validation Card */
    .validation-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
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

    /* Table Styles */
    .validation-table-wrapper {
        padding: 24px;
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

    /* Ticket Link */
    .ticket-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .ticket-link:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    /* New Badge */
    .new-badge {
        background: var(--accent-color);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Request Type Badges */
    .request-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
    }

    .request-badge.subscription {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .request-badge.resiliation {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
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

    .status-badge.pending {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .status-badge.validated {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .status-badge.refused {
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

    /* Agent Badge */
    .agent-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid var(--text-secondary);
        color: var(--text-secondary);
        background: transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid var(--border-color);
    }

    /* Code styling */
    code {
        background: var(--bg-light);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        font-family: 'Courier New', monospace;
    }

    /* Value styling */
    .value-accent {
        color: var(--accent-color);
        font-weight: 500;
    }

    .value-primary {
        color: var(--primary-color);
        font-weight: 500;
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
            <i class="ri-file-list-line"></i>
            Validations - Administration
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

                @if(session('success'))
    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line"></i>
        <div>
                    <strong>Succès !</strong> {{ session('success') }}
        </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

                    {{-- Debug info (temporaire) --}}
                    @if(request()->has('debug') || session('debug_validation'))
    <div class="alert-modern alert-info">
        <i class="ri-information-line"></i>
        <div>
                        <strong>Debug Info:</strong><br>
                        <small>
                            Access: {{ $access_info['access'] }}<br>
                            Is Super Admin: {{ $access_info['is_super_admin'] ? 'OUI' : 'NON' }}<br>
                            Office Name: {{ session('officeName') ?? 'NULL' }}<br>
                            Parent Name: {{ session('parent_name') ?? 'NULL' }}<br>
                            Allowed Offices: {{ implode(', ', $allowed_offices) }}<br>
                            Total Validations: {{ $validations->total() }}<br>
                            Validations Count: {{ count($validations) }}
                        </small>
        </div>
                    </div>
                    @endif

    <!-- Validation Card -->
    <div class="validation-card">
        <div class="validation-table-wrapper">
            <div class="table-responsive">
                <table class="table modern-table" id="validation_table">
                    <thead>
                        <tr>
                            <th scope="col">N° Demande</th>
                                    <th scope="col">Date demande</th>
                                    <th scope="col">Type de Demande</th>
                                    <th scope="col">Numéro de ligne</th>
                                    <th scope="col">Compte</th>
                                    <th scope="col">Clé d'activation</th>
                                    <th scope="col">Agence de souscription</th>
                                    <th scope="col">Traitant</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Commentaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validations as $validation)
                                @php
                        $isNew = \Carbon\Carbon::parse($validation->created_at)->format('Y-m-d') === date('Y-m-d');
                        $status = $validation->status ?? null;
                        $isPending = ($status === "0" || $status === 0 || $status == 0);
                        $isRefused = ($status === "2" || $status === 2 || $status == 2);
                        $isValidated = ($status === "1" || $status === 1 || $status == 1);
                                @endphp

                                <tr>
                            <td>
                                            <div class="d-flex align-items-center gap-2">
                                    <a href="#" class="ticket-link">
                                        <i class="ri-hashtag"></i>
                                        {{ $validation->ticket }}
                                    </a>
                                    @if($isNew)
                                    <span class="new-badge">New</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                        @if($validation->request_type === 'SOUSCRIPTION')
                                <span class="request-badge subscription">
                                    <i class="ri-check-double-line"></i>
                                    {{ $validation->request_type }}
                                </span>
                                        @elseif($validation->request_type === 'RESILIATION')
                                <span class="request-badge resiliation">
                                    <i class="ri-close-circle-line"></i>
                                    {{ $validation->request_type }}
                                </span>
                                        @else
                                <strong class="value-primary">{{ $validation->request_type }}</strong>
                                        @endif
                                    </td>
                            <td>
                                <span class="value-primary">
                                    <i class="ri-smartphone-line"></i>
                                    {{ $validation->mobile_no }}
                                </span>
                            </td>
                            <td>
                                <span class="value-accent">{{ $validation->account_no }}</span>
                            </td>
                            <td>
                                <code>{{ $validation->key }}</code>
                                    </td>
                            <td>
                                <span class="office-badge">
                                    <i class="ri-building-2-line"></i>
                                    {{ $validation->office_name }}
                                </span>
                                    </td>
                            <td>
                                <span class="agent-badge">
                                    <i class="ri-user-line"></i>
                                    {{ $validation->bank_agent }}
                                </span>
                                    </td>
                            <td>
                                @if($isPending)
                                <span class="status-badge pending">
                                    <i class="ri-time-line"></i>
                                    En attente
                                </span>
                                    @elseif($isRefused)
                                <span class="status-badge refused">
                                    <i class="ri-close-line"></i>
                                    Refusé
                                </span>
                                @elseif($isValidated)
                                <span class="status-badge validated">
                                    <i class="ri-check-line"></i>
                                    Validé
                                </span>
                                @else
                                <span class="status-badge pending">
                                    <i class="ri-time-line"></i>
                                    En attente
                                </span>
                                @endif
                                    </td>
                            <td>
                                <span style="color: var(--text-secondary); font-size: 13px;">
                                    {{ $validation->motif_validation ?? $validation->motif ?? '' }}
                                </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
                    </div>

        <!-- Pagination -->
        @if($validations->hasPages())
        <div class="pagination-wrapper">
                        {{ $validations->links() }}
                    </div>
        @endif
                </div>
            </div>

{{-- DataTable scripts --}}
            <script>
                $(document).ready(function() {
                    $('#validation_table').DataTable({
                        paging: false,
                        info: false,
                        lengthChange: false,
                        searching: true,
            order: [[1, 'desc']],
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                        }
                    });
                });
            </script>

@endsection
