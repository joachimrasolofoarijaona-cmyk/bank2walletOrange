@extends('layouts.sidebar')

@section('title', ':: Recherche Client ::')

@section('content')
@php
$colorText = "#212529";
$colorPrimary = "#00574A";
$colorAccent = "#50c2bb";
@endphp

<div class="container-fluid pt-0">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                {{-- session error --}}
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur !</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

                {{-- session success --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                @endif

                <div class="card-header bg-gradient-primary text-white text-center d-flex align-content-center">
                    <i class="ri-search-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Recherche de Client</h4>
                </div>

                <div class="card-body bg-light">
                    {{-- Formulaire de recherche --}}
                    <form action="{{ route('client.search.post') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <label for="msisdn" class="form-label fw-bold">Numéro de ligne</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="msisdn" 
                                       name="msisdn" 
                                       placeholder="Ex: 0321234567" 
                                       value="{{ $msisdn ?? '' }}"
                                       required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn w-100" style="background: {{ $colorPrimary }}; color: white;">
                                    <i class="ri-search-line me-2"></i>Rechercher
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Tableau des résultats --}}
                    @if(isset($clients) && $clients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="clientsTable" style="font-size: 9pt;">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col"><strong>ID Client</strong></th>
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
                            <tbody style="background-color: #f8fafb;">
                                @foreach($clients as $client)
                                @php
                                $isActive = $client->account_status == "1";
                                @endphp
                                <tr class="align-middle border-0">
                                    <td class="fw-bold" style="color: {{ $colorPrimary }};">
                                        <a href="#" 
                                           class="text-decoration-none" 
                                           style="color: {{ $colorPrimary }};"
                                           onclick="showClientDetails({{ json_encode($client) }}); return false;">
                                            <i class="ri-hashtag"></i> {{ $client->client_id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($isActive)
                                        <span class="badge rounded-pill bg-success px-3 py-1 fw-bold" style="font-size: 9pt;">
                                            <i class="ri-check-line"></i> Actif
                                        </span>
                                        @else
                                        <span class="badge rounded-pill bg-danger px-3 py-1 fw-bold" style="font-size: 9pt;">
                                            <i class="ri-close-line"></i> Inactif
                                        </span>
                                        @endif
                                    </td>
                                    <td style="font-size: 9pt;">{{ $client->client_lastname ?? 'N/A' }}</td>
                                    <td style="font-size: 9pt;">{{ $client->client_firstName ?? 'N/A' }}</td>
                                    <td style="font-size: 9pt;">
                                        <span style="color: {{ $colorAccent }};" class="fw-normal">
                                            {{ $client->msisdn ?? $client->mobile_no ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td style="font-size: 9pt;">
                                        <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; font-size: 9pt;">
                                            <i class="ri-building-2-line"></i> {{ $client->officeName ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td style="font-size: 9pt;">
                                        <span style="color: {{ $colorAccent }};" class="fw-normal">{{ $client->account_no ?? 'N/A' }}</span>
                                    </td>
                                    <td style="font-size: 9pt;">{{ $client->libelle ?? 'N/A' }}</td>
                                    <td style="font-size: 9pt;">
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal pour afficher les détails complets --}}
<div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $colorPrimary }}; color: white;">
                <h5 class="modal-title" id="clientDetailsModalLabel">
                    <i class="ri-user-line me-2"></i>Détails de la Souscription
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div id="clientDetailsContent">
                    {{-- Le contenu sera injecté par JavaScript --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showClientDetails(client) {
        const modal = new bootstrap.Modal(document.getElementById('clientDetailsModal'));
        const content = document.getElementById('clientDetailsContent');
        
        const statusBadge = client.account_status == "1" 
            ? '<span class="badge bg-success"><i class="ri-check-line"></i> Actif</span>'
            : '<span class="badge bg-danger"><i class="ri-close-line"></i> Inactif</span>';
        
        const dateSub = client.date_sub 
            ? new Date(client.date_sub).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : 'N/A';
        
        const dateDob = client.client_dob 
            ? new Date(client.client_dob).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : 'N/A';

        content.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold" style="color: {{ $colorPrimary }};">Informations Client</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">ID Client:</td>
                            <td>${client.client_id || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nom:</td>
                            <td>${client.client_lastname || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Prénoms:</td>
                            <td>${client.client_firstName || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">CIN:</td>
                            <td>${client.client_cin || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date de naissance:</td>
                            <td>${dateDob}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold" style="color: {{ $colorPrimary }};">Informations Souscription</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Statut:</td>
                            <td>${statusBadge}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Numéro de ligne:</td>
                            <td>${client.msisdn || client.mobile_no || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Compte:</td>
                            <td>${client.account_no || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Libellé:</td>
                            <td>${client.libelle || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Code service:</td>
                            <td>${client.code_service || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date souscription:</td>
                            <td>${dateSub}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold" style="color: {{ $colorPrimary }};">Informations Techniques</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Alias:</td>
                            <td><code>${client.alias || 'N/A'}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Clé d'activation:</td>
                            <td><code>${client.key || 'N/A'}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Agence:</td>
                            <td>${client.officeName || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Agent:</td>
                            <td>${client.bank_agent || 'N/A'}</td>
                        </tr>
                    </table>
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
            }
        });
    });
</script>

@endsection

