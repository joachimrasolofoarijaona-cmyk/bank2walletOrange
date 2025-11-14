@extends('layouts.sidebar')

@section('title', ':: Mes Demandes ::')

@section('content')
@php
use Illuminate\Support\Facades\DB;

$current_date = date('Y-m-d H:i:s');
$colorText = "#212529";
$colorPrimary = "#00574A";
$colorAccent = "#50c2bb";
$tdStyle = "font-size: 9pt;";
$spanStyle = "font-size: 9pt;";
$codeStyle = "font-size: 9pt;";
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
                    <i class="ri-file-list-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Mes Demandes</h4>
                </div>

                <div class="card-body bg-light">
                    {{-- Debug info (temporaire) --}}
                    @if(request()->has('debug') || session('debug_validation'))
                    <div class="alert alert-info mb-3">
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
                    @endif

                    <div class="container-fluid table-responsive">
                        <table class="table table-hover" id="validation_table" style="font-size: 9pt;">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col"><strong>N° Demande</strong></th>
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
                            <tbody style="background-color: #f8fafb;">
                                @foreach($validations as $validation)
                                @php
                                $isSouscription = $validation->request_type === 'SOUSCRIPTION';
                                $isResiliation = $validation->request_type === 'RESILIATION';
                                $isValidationPending = $validation->status === "0";
                                $isValidated = $validation->status === "1";
                                $isRefused = $validation->status === "2";

                                $account_subscribed = DB::table('subscription')
                                    ->select('account_status')
                                    ->where('account_no', $validation->account_no)
                                    ->first();

                                $subscribed = ($account_subscribed && $account_subscribed->account_status == '1');
                                
                                // Vérifier final_status pour savoir si déjà activé/résilié
                                $final_status = $validation->final_status ?? null;
                                @endphp

                                <tr class="align-middle border-0">
                                    <td class="fw-bold" style="color: {{ $colorPrimary }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                    <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                    <td style="{{ $tdStyle }}">
                                        <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        @if($isSouscription)
                                        <span class="badge rounded-pill bg-success text-uppercase px-2 py-2" style="{{ $spanStyle }}"><i class="ri-check-double-line"></i> SOUSCRIPTION</span>
                                        @elseif($isResiliation)
                                        <span class="badge rounded-pill bg-danger text-uppercase px-2 py-2" style="{{ $spanStyle }}"><i class="ri-close-circle-line"></i> RESILIATION</span>
                                        @endif
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        <span style="color: {{ $colorAccent }}; {{ $spanStyle }}" class="fw-normal">{{ $validation->account_no }}</span>
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        <code class="fw-bold" style="color: {{ $colorPrimary }}; {{ $codeStyle }}">{{ $validation->key }}</code>
                                        @if($validation->active)
                                        <span class="badge rounded-pill ms-1" title="Déjà activée" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}"><i class="ri-flashlight-line"></i></span>
                                        @endif
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-line"></i> {{ $validation->validator ?? 'N/A' }}</span>
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        @if($isValidationPending)
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-1 fw-normal" style="{{ $spanStyle }}">En attente</span>
                                        @elseif($isRefused)
                                            <span class="badge rounded-pill bg-danger px-3 py-1 fw-bold" style="{{ $spanStyle }}">Refusé</span>
                                        @elseif($isValidated && $isSouscription && $final_status === null)
                                            {{-- SOUSCRIPTION VALIDÉE - Bouton Activer --}}
                                            <form action="{{ route('activate.service') }}" method="POST" class="mb-0 d-inline">
                                                @csrf
                                                <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                                <input type="hidden" name="mobile_no" value="{{ $validation->mobile_no }}">
                                                <input type="hidden" name="key" value="{{ $validation->key }}">
                                                <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                                <button type="submit" class="btn btn-sm rounded-pill px-4" title="Activer cette souscription" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}">
                                                    <i class="ri-flashlight-line"></i> Activer
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
                                                <button type="submit" class="btn btn-sm rounded-pill px-4" title="Résilier ce compte" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}">
                                                    <i class="ri-close-circle-line"></i> Résilier
                                                </button>
                                            </form>
                                        @elseif($isValidated && $isSouscription && $final_status === 'activated')
                                            <span class="badge rounded-pill bg-success px-3 py-1 fw-bold" style="{{ $spanStyle }}"><i class="ri-check-line"></i> Activé</span>
                                        @elseif($isValidated && $isResiliation && $final_status === 'resiliated')
                                            <span class="badge rounded-pill bg-danger px-3 py-1 fw-bold" style="{{ $spanStyle }}"><i class="ri-close-line"></i> Résilié</span>
                                        @elseif($isValidated)
                                            <span class="badge rounded-pill bg-success px-3 py-1 fw-bold" style="{{ $spanStyle }}">Validé</span>
                                        @endif
                                    </td>
                                    <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation ?? $validation->motif ?? '' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-3 bg-light">
                        {{ $validations->links() }}
                    </div>
                </div>
            </div>

            {{-- datatable scripts --}}
            <script>
                $(document).ready(function() {
                    $('#validation_table').DataTable({
                        paging: false,
                        info: false,
                        lengthChange: false,
                        searching: true,
                        order: [[1, 'desc']], // Trier par la colonne "Date demande" (index 1) en ordre décroissant
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>

@endsection

