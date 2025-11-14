@extends('layouts.sidebar')

@section('title', ':: Paramètres ::')

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

    /* Configuration Card */
    .config-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
        height: 100%;
    }

    .config-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .config-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .config-card-header h5 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .config-card-body {
        padding: 24px;
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

    /* Service Status */
    .service-status {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg-light);
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .service-status i {
        font-size: 32px;
    }

    .service-status.active i {
        color: #198754;
    }

    .service-status.inactive i {
        color: #dc3545;
    }

    .service-status h5 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    /* Form Styles */
    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .form-control,
    textarea.form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        color: var(--text-primary);
    }

    .form-control:focus,
    textarea.form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(2, 86, 74, 0.1);
        outline: none;
    }

    .form-check {
        padding: 16px;
        background: var(--bg-light);
        border-radius: 8px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        margin-top: 16px;
    }

    .form-check:hover {
        border-color: var(--primary-color);
    }

    .form-check-input {
        width: 48px;
        height: 24px;
        margin-top: 2px;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        margin-left: 12px;
    }

    /* Logs Cards */
    .logs-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .logs-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logs-card-header i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .logs-card-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .logs-card-body {
        padding: 24px;
    }

    /* Table Styles */
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
    }

    .modern-table tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .table-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .table-link:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    /* Action Badges */
    .action-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .action-badge.activation {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .action-badge.desactivation {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
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
    }

    .modal-body {
        padding: 24px;
        background: white;
    }

    .modal-body p {
        margin-bottom: 12px;
        font-size: 14px;
        color: var(--text-primary);
    }

    .modal-body strong {
        color: var(--primary-color);
        font-weight: 600;
        min-width: 140px;
        display: inline-block;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 16px 24px;
        background: white;
    }

    /* Buttons */
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

    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        background: transparent;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
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
            <i class="ri-settings-3-line"></i>
            Paramètres
        </h1>
    </div>

    <div class="row g-4">
        {{-- Configuration Card --}}
        <div class="col-lg-3 col-md-12">
            <div class="config-card">
                <div class="config-card-header">
                    <i class="ri-tools-line"></i>
                    <h5>Configuration</h5>
                </div>
                <div class="config-card-body">
                    <div class="mb-3">
                        <label class="form-label">État du service actuel</label>
                        <div>
                            <span class="status-badge {{ $status == 'ACTIF' ? 'active' : 'inactive' }}">
                                <i class="ri-{{ $status == 'ACTIF' ? 'check' : 'close' }}-circle-line"></i>
                                {{ $status }}
                            </span>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; border-color: var(--border-color);">

                    <div class="service-status {{ $status == 'ACTIF' ? 'active' : 'inactive' }}">
                        @if($status == "ACTIF")
                        <i class="ri-toggle-fill"></i>
                        @else
                        <i class="ri-toggle-line"></i>
                        @endif
                        <h5>Service {{ $status }}</h5>
                    </div>

                    {{-- Form for settings --}}
                    <form action="{{ route('update.settings') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="motif" class="form-label">Motif</label>
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Motif de la désactivation" 
                                   name="motif" 
                                   id="motif" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaires</label>
                            <textarea class="form-control" 
                                      name="commentaire" 
                                      id="commentaire" 
                                      rows="3" 
                                      required></textarea>
                        </div>

                        {{-- Hidden field for pause --}}
                        <input type="hidden" name="pause" id="pauseInput" value="{{ $last_row->pause }}">

                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="disable"
                                   {{ $last_row->pause == "1" ? 'checked' : '' }}>
                            <label class="form-check-label" for="disable">
                                @if($last_row->pause == "1")
                                <span style="color: #198754;">Activer le service</span>
                                @else
                                <span style="color: #dc3545;">Désactiver le service</span>
                                @endif
                            </label>
                        </div>

                        {{-- Modal for confirmation --}}
                        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body" id="modalMessage">
                                        <!-- Message dynamique -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Non</button>
                                        <button type="button" class="btn btn-outline-danger" id="confirmSubmit">Oui</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const checkbox = document.getElementById('disable');
                            const pauseInput = document.getElementById('pauseInput');
                            const form = checkbox.closest('form');
                            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                            const modalMessage = document.getElementById('modalMessage');
                            const confirmButton = document.getElementById('confirmSubmit');

                            let oldValue = checkbox.checked;

                            checkbox.addEventListener('change', function() {
                                const isChecked = checkbox.checked;

                                modalMessage.textContent = isChecked ?
                                    "Êtes-vous sûr de vouloir activer le service Bank to Wallet ?" :
                                    "Êtes-vous sûr de vouloir désactiver le service Bank to Wallet ?";

                                confirmModal.show();

                                confirmButton.onclick = function() {
                                    pauseInput.value = isChecked ? "0" : "1";
                                    form.submit();
                                };

                                // Si l'utilisateur clique sur NON
                                document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
                                    checkbox.checked = oldValue; // revert
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        {{-- Settings Logs --}}
        <div class="col-lg-4 col-md-12">
            <div class="logs-card">
                <div class="logs-card-header">
                    <i class="ri-file-list-line"></i>
                    <h4>Settings Logs</h4>
                </div>
                <div class="logs-card-body">
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Motif</th>
                                    <th>Action</th>
                                    <th>Initiateur</th>
                                    <th>Commentaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $data)
                                <tr>
                                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                                    <td>
                                        <a href="#" class="table-link" data-bs-toggle="modal" data-bs-target="#modal{{ $data->id }}">
                                            {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}
                                        </a>
                                    </td>
                                    <td><strong>{{ substr($data->motif, 0, 15) }}...</strong></td>
                                    <td>
                                        @if($data->pause == 1)
                                        <span class="action-badge desactivation">Désactivation</span>
                                        @else
                                        <span class="action-badge activation">Activation</span>
                                        @endif
                                    </td>
                                    <td><small>{{ substr($data->user_name, 0, 10) }}...</small></td>
                                    <td><small>{{ substr($data->commentaire, 0, 15) }}...</small></td>
                                </tr>

                                <!-- Modal pour chaque enregistrement -->
                                <div class="modal fade" id="modal{{ $data->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $data->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel{{ $data->id }}">Détails de l'enregistrement #{{ $data->id }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Date de création :</strong> {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}</p>
                                                <p><strong>Motif :</strong> {{ $data->motif ?? 'Non renseigné' }}</p>
                                                <p><strong>Action :</strong> @if($data->pause == 0) Activation @else Désactivation @endif</p>
                                                <p><strong>By :</strong> {{ $data->user_name ?? 'Non renseignée' }}</p>
                                                <p><strong>Commentaires :</strong> {{ $data->commentaire }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="6" class="empty-state">Aucune donnée disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($datas->hasPages())
                    <div class="pagination-wrapper">
                        {{ $datas->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activities Logs --}}
        <div class="col-lg-5 col-md-12">
            <div class="logs-card">
                <div class="logs-card-header">
                    <i class="ri-file-list-line"></i>
                    <h4>Activities Logs</h4>
                </div>
                <div class="logs-card-body">
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP</th>
                                    <th>Browser</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activity_logs as $log)
                                <tr>
                                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td><strong>{{ $log->user_id }}</strong></td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td><small>{{ $log->ip_address }}</small></td>
                                    <td><small>{{ substr($log->user_agent, 0, 7) }}...</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($activity_logs->hasPages())
                    <div class="pagination-wrapper">
                        {{ $activity_logs->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
