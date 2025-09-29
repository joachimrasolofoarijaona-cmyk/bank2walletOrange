@extends('layouts.sidebar')

@section('title', ':: Paramètres ::')

@section('content')
<div class="container-fluid">
    <div class="row ">

        {{-- col for settings actions --}}
        <div class="col-lg-2 col-md-12 col-xs-12 " style="font-size:11px;">
            <div class="card bg-dark shadow">
                <div class="card-header d-flex align-items-start">
                    <i class="ri-tools-line me-2 fs-3"></i>
                    <h5 class="card-title text-uppercase pt-2 f5" style="color: #00564b; font-weight: bold;">configuration</h5>
                </div>
                <div class="card-body bg-dark">
                    <h6 class="text-white">Etat du service actuel :
                        <span @if($status=="ACTIF" ) class="badge bg-success" @else class="badge bg-danger" @endif>{{$status}}</span>
                    </h6>
                    <hr>
                    <div class="d-flex align-items-start text-white">
                        @if($status == "ACTIF")
                        <i class="ri-toggle-fill me-2 fs-2 pt-1"></i>
                        <h5 class="text-uppercase pt-3">Service ACTIF</h5>
                        @else
                        <i class="ri-toggle-line me-2 fs-2 pt-1"></i>
                        <h5 class="text-uppercase pt-3">Service INACTIF</h5>
                        @endif
                    </div>

                    {{-- form for settings --}}
                    <div class="form text-white">
                        <form action="{{ route('update.settings') }}" method="POST">
                            @csrf
                            <div class="form-group pt-2">
                                <label for="motif">Motif</label>
                                <input type="text" class="form-control bg-secondary text-white"
                                    placeholder="Motif de la désactivation"
                                    name="motif" id="motif" required>
                            </div>
                            <div class="form-group pt-2">
                                <label for="commentaire">Commentaires</label>
                                <textarea class="form-control bg-secondary text-white"
                                    name="commentaire" id="commentaire" rows="3" required></textarea>
                            </div>

                            {{-- champ hidden pour pause --}}
                            <input type="hidden" name="pause" id="pauseInput" value="{{ $last_row->pause }}">

                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" id="disable"
                                    {{ $last_row->pause == "1" ? 'checked' : '' }}>
                                <label class="form-check-label" for="disable">
                                    @if($last_row->pause == "1")
                                    <span class="text-success"><strong>Activer le service</strong></span>
                                    @else
                                    <span class="text-danger"><strong>Désactiver le service</strong></span>
                                    @endif
                                </label>
                            </div>

                            {{-- modal for confirmation --}}
                            <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark">
                                            <h5 class="modal-title text-uppercase" id="confirmModalLabel">Confirmation</h5>
                                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Fermer">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body bg-secondary" id="modalMessage">
                                            <!-- Message dynamique -->
                                        </div>
                                        <div class="modal-footer bg-dark">
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

                                    // Si l’utilisateur clique sur NON
                                    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
                                        checkbox.checked = oldValue; // revert
                                    });
                                });
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>

        {{-- col for settings logs --}}
        <div class="col-lg-5 col-md-12 col-xs-12" style="font-size: 11px;">
            <div class="card bg-dark shadow border-0">
                {{-- Header --}}
                <div class="card-header bg-dark d-flex align-items-center">
                    <i class="ri-file-list-line fs-3 me-2 "></i>
                    <h4 class="card-title text-uppercase  mb-0">Settings Logs </h4>
                </div>

                {{-- Body --}}
                <div class="card-body bg-dark">
                    <div class="table-responsive">
                        <table class="table align-middle text-white mb-0">
                            <thead class="table-dark text-uppercase text-start">
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
                                    <td class="text-start">
                                        <small>
                                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#modal{{ $data->id }}">
                                                {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}
                                            </a>
                                        </small>
                                    </td>
                                    <td><strong>{{ substr($data->motif, 0, 15) }} ...</strong></td>
                                    <td class="text-start">
                                        @if($data->pause == 1)
                                        <span class="text-danger">Désactivation</span>
                                        @else
                                        <span class="text-success">Activation</span>
                                        @endif
                                    </td>
                                    <td class="text-start"><small>{{ substr($data->user_name, 0, 10) }}...</small></td>
                                    <td>{{ substr($data->commentaire, 0, 15) }}...</td>
                                </tr>

                                <!-- Fenêtre Modal (unique pour chaque enregistrement) -->
                                <div class="modal fade" id="modal{{ $data->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $data->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel{{ $data->id }}">Détails de l’enregistrement #{{ $data->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>

                                            <div class="modal-body">
                                                <p><strong>Date de création :</strong> {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}</p>
                                                <p><strong>Motif :</strong> {{ $data->motif ?? 'Non renseigné' }}</p>
                                                <p><strong>Action :</strong> @if($data->pause == 0) Activation  @else Désactivation @endif</p>
                                                <p><strong>By :</strong> {{ $data->user_name ?? 'Non renseignée' }}</p>
                                                <p><strong>Commentaires :</strong> {{ $data->commentaire }}</p>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-start text-muted">Aucune donnée disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $datas->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>

        {{-- col for general logs  --}}
        <div class="col-lg-5 col-md-12 col-xs-12 " style="font-size: 11px;">
            <div class="card shadow">
                {{-- Header --}}
                <div class="card-header bg-dark d-flex align-items-center">
                    <i class="ri-file-list-line fs-3 me-2"></i>
                    <h4 class="card-title text-uppercase mb-0">Activities Logs</h4>
                </div>

                <div class="card-body bg-dark">
                    <div class="table-responsive">
                        <table class="table align-middle text-white mb-0">
                            <thead class="table-dark text-uppercase text-start">
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
                                    <td class="text-center">
                                        <strong>{{ $loop->iteration }}</strong>
                                    </td>
                                    <td class="text-start">
                                        <small>
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td><strong>{{ $log->user_id }}</strong></td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>{{ substr($log->user_agent, 0, 7) }}...</td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $activity_logs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        @endsection