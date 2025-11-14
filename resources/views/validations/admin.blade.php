@extends('layouts.sidebar')

@section('title', ':: Validations ::')

@section('content')
@php
$current_date = date('Y-m-d H:i:s');
$colorText = "#212529";
$colorPrimary = "#00574A";
$colorAccent = "#50c2bb";
$tdStyle = "font-size: 9pt;";
$spanStyle = "font-size: 9pt;";
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
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Validations - Administration</h4>
                </div>

                <div class="card-body bg-light">
                    {{-- Debug info (temporaire) --}}
                    @if(request()->has('debug') || session('debug_validation'))
                    <div class="alert alert-info mb-3">
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
                    @endif

                    <div class="container-fluid table-responsive">
                        <table class="table table-hover" id="validation_table" style="font-size: 9pt;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col"><strong>N° Demande</strong></th>
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
                                $modalId = 'modal_' . $validation->ticket;
                                $isRefused = $validation->status === "2";
                                @endphp
                                <tr>
                                    @if(\Carbon\Carbon::parse($validation->created_at)->format('Y-m-d') === $current_date)
                                    <td style="{{ $tdStyle }}">
                                        <strong>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="" class="nav-link mb-0 p-0" style="color: {{ $colorPrimary }};"> {{$validation->ticket}} </a>
                                                <span class="badge rounded-pill" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}">New</span>
                                            </div>
                                        </strong>
                                    </td>
                                    @else
                                    <td style="{{ $tdStyle }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong><a href="" class="nav-link" style="color: {{ $colorPrimary }};">{{$validation->ticket}}</a></strong>
                                        </div>
                                    </td>
                                    @endif

                                    <td style="{{ $tdStyle }}">{{$validation->created_at}}</td>
                                    <td style="{{ $tdStyle }}">
                                        @if($validation->request_type === 'SOUSCRIPTION')
                                        <span class="badge rounded-pill bg-success text-uppercase px-2 py-1" style="{{ $spanStyle }}"><i class="ri-check-double-line"></i> {{$validation->request_type}}</span>
                                        @elseif($validation->request_type === 'RESILIATION')
                                        <span class="badge rounded-pill bg-danger text-uppercase px-2 py-1" style="{{ $spanStyle }}"><i class="ri-close-circle-line"></i> {{$validation->request_type}}</span>
                                        @else
                                        <strong style="color: {{ $colorPrimary }};">{{$validation->request_type}}</strong>
                                        @endif
                                    </td>
                                    <td style="{{ $tdStyle }}"><span style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{$validation->mobile_no}}</span></td>
                                    <td style="{{ $tdStyle }}"><span style="color: {{ $colorAccent }}; {{ $spanStyle }}">{{$validation->account_no}}</span></td>
                                    <td style="{{ $tdStyle }}"><code style="color: {{ $colorPrimary }}; {{ $spanStyle }}">{{$validation->key}}</code></td>
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{$validation->office_name}}</span>
                                    </td>
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-line"></i> {{$validation->bank_agent}}</span>
                                    </td>

                                    @if($validation->status === "0")
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-1" style="{{ $spanStyle }}">En attente</span>
                                    </td>
                                    @elseif($isRefused)
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill bg-danger px-3 py-1" style="{{ $spanStyle }}">Refusé</span>
                                    </td>
                                    @else
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill bg-success px-3 py-1" style="{{ $spanStyle }}">Validé</span>
                                    </td>
                                    @endif

                                    <td style="{{ $tdStyle }}"><span style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation ?? $validation->motif ?? '' }}</span></td>
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

