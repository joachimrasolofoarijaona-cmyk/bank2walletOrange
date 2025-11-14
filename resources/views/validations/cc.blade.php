@extends('layouts.sidebar')

@section('title', ':: Mes Demandes ::')

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

    .status-badge.activated {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .status-badge.resiliated {
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

    /* Buttons - Outline Style */
    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        font-weight: 500;
        padding: 6px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
        padding: 6px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-outline-secondary:hover {
        background: var(--text-secondary);
        color: white;
        transform: translateY(-2px);
    }

    /* Active Badge */
    .active-badge {
        background: var(--accent-color);
        color: white;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        margin-left: 6px;
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
            Mes Demandes
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
                Office Name: {{ session('officeName') ?? 'NULL' }}<br>
                Parent Name: {{ session('parent_name') ?? 'NULL' }}<br>
                Current User: {{ $current_user ?? 'NULL' }}<br>
                User Fullname: {{ session('firstname') ?? '' }} {{ session('lastname') ?? '' }}<br>
                Allowed Offices: {{ implode(', ', $allowed_offices) }}<br>
                Total Validations: {{ $validations->total() }}<br>
                Filtered Count: {{ count($validations) }}
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
                            <th scope="col">Numéro de ligne</th>
                            <th scope="col">Type de Demande</th>
                            <th scope="col">Compte</th>
                            <th scope="col">Clé d'activation</th>
                            <th scope="col">Agence de validation</th>
                            <th scope="col">Validateur</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Commentaires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validations as $validation)
                        @php
                        use Illuminate\Support\Facades\DB;
                        $isSouscription = $validation->request_type === 'SOUSCRIPTION';
                        $isResiliation = $validation->request_type === 'RESILIATION';
                        $isValidationPending = ($validation->status === "0" || $validation->status === 0);
                        $isValidated = ($validation->status === "1" || $validation->status === 1);
                        $isRefused = ($validation->status === "2" || $validation->status === 2);

                        $account_subscribed = DB::table('subscription')
                            ->select('account_status')
                            ->where('account_no', $validation->account_no)
                            ->first();

                        $subscribed = ($account_subscribed && $account_subscribed->account_status == '1');
                        $final_status = $validation->final_status ?? null;
                        @endphp

                        <tr>
                            <td>
                                <a href="#" class="ticket-link">
                                    <i class="ri-hashtag"></i>
                                    {{ $validation->ticket }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="value-primary">
                                    <i class="ri-smartphone-line"></i>
                                    {{ $validation->mobile_no }}
                                </span>
                            </td>
                            <td>
                                @if($isSouscription)
                                <span class="request-badge subscription">
                                    <i class="ri-check-double-line"></i>
                                    SOUSCRIPTION
                                </span>
                                @elseif($isResiliation)
                                <span class="request-badge resiliation">
                                    <i class="ri-close-circle-line"></i>
                                    RESILIATION
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="value-accent">{{ $validation->account_no }}</span>
                            </td>
                            <td>
                                <code>{{ $validation->key }}</code>
                                @if($validation->active)
                                <span class="active-badge" title="Déjà activée">
                                    <i class="ri-flashlight-line"></i>
                                </span>
                                @endif
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
                                    {{ $validation->validator ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($isValidationPending)
                                <span class="status-badge pending">
                                    <i class="ri-time-line"></i>
                                    En attente
                                </span>
                                @elseif($isRefused)
                                <span class="status-badge refused">
                                    <i class="ri-close-line"></i>
                                    Refusé
                                </span>
                                @elseif($isValidated && $isSouscription && $final_status === null)
                                {{-- SOUSCRIPTION VALIDÉE - Bouton Activer --}}
                                <form action="{{ route('activate.service') }}" method="POST" class="mb-0 d-inline">
                                    @csrf
                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                    <input type="hidden" name="mobile_no" value="{{ $validation->mobile_no }}">
                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                    <button type="submit" class="btn btn-outline-primary" title="Activer cette souscription">
                                        <i class="ri-flashlight-line"></i>
                                        Activer
                                    </button>
                                </form>
                                @elseif($isValidated && $isResiliation && $validation->active && $final_status === null)
                                {{-- RESILIATION VALIDÉE - Bouton Résilier --}}
                                <form action="{{ route('do.unsubscribe') }}" method="POST" class="mb-0 d-inline">
                                    @csrf
                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                    <input type="hidden" name="msisdn" value="{{ $validation->mobile_no }}">
                                    <button type="submit" class="btn btn-outline-secondary" title="Résilier ce compte">
                                        <i class="ri-close-circle-line"></i>
                                        Résilier
                                    </button>
                                </form>
                                @elseif($isValidated && $isSouscription && $final_status === 'activated')
                                <span class="status-badge activated">
                                    <i class="ri-check-line"></i>
                                    Activé
                                </span>
                                @elseif($isValidated && $isResiliation && $final_status === 'resiliated')
                                <span class="status-badge resiliated">
                                    <i class="ri-close-line"></i>
                                    Résilié
                                </span>
                                @elseif($isValidated)
                                <span class="status-badge validated">
                                    <i class="ri-check-line"></i>
                                    Validé
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
