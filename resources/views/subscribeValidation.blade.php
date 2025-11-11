@extends('layouts.sidebar')

@section('title', ':: Validations ::')

@section('content')
@php

    use Illuminate\Support\Facades\DB;
    use App\Models\Subscription;

    $current_date = date('Y-m-d H:i:s');

    $validations = DB::table('validation')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Diagnostic: Pourquoi $get_zone_id renvoie constamment null 
    $office_name = session('officeName');
    $role = session('roles');
    $hierarchy = session('hierarchy');
    $parent_name = session('parent_name');
    
    // Gestion des différents formats possibles pour $office_name

    $clean_office_name = is_string($office_name) ? trim(str_replace(["\n","\r","\t"], '', $office_name)) : '';
    $validator_officename = null;

    // Cas où l'office_name contient un séparateur de zone - agence
    if (strpos($clean_office_name, ' - ') !== false) {
        $validator_officename = explode(' - ', $clean_office_name)[0];
    } elseif (strpos($clean_office_name, '-') !== false) {
        $validator_officename = explode('-', $clean_office_name)[0];
    } else {
        // Si $office_name est un nom composé comme "ANKADIKELY ILAFY", "PORT BERGER", etc.
        // On cherche d'abord directement une zone avec ce nom proprement formaté
        $zone = DB::table('zones')
            ->select('id')
            ->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?", [$clean_office_name])
            ->first();

        if ($zone && isset($zone->id)) {
            // Le nom correspond à une zone, on va chercher toutes les agences de cette zone
            $get_agences = DB::table('agences')
                ->where('zone_id', $zone->id)
                ->pluck('nom')
                ->toArray();

            $validator_officename = $get_agences; // Un array d'agences associées à la zone trouvée
        } else {
            // Pas une zone, ça doit être une agence: on cherche alors l’agence du même nom directement
            $agence = DB::table('agences')
                ->select('nom')
                ->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?", [$clean_office_name])
                ->first();

            if ($agence && isset($agence->nom)) {
                $validator_officename = $agence->nom;
            } else {
                // Si introuvable, par sécurité, on assigne la valeur brute (nom composé ou inconnu)
                $validator_officename = $clean_office_name;
            }
        }
    }


    // Récupération de toutes les clés existantes dans Subscription
    $active_keys = Subscription::pluck('key')->toArray();

    // Ajout d'une information "active" à chaque validation
    foreach ($validations as $validation) {
        $validation->active = in_array($validation->key, $active_keys);
    }

    $get_zone_id = DB::table('zones')
        ->select('id')
        ->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?", $office_name)
        ->first();

    $allowed_offices = [];

    if ($get_zone_id) {
        // Si c'est une zone, récupérer toutes les agences qui en dépendent
        $get_agences = DB::table('agences')
        ->where('zone_id', $get_zone_id->id)
        ->pluck('nom')
        ->toArray();
        
        $allowed_offices = $get_agences;
    }else {
        // Sinon c'est juste une agence
        $allowed_offices = [$validator_officename];
    }


    $current_user = session('username'); // matricule

    $user_id = session('id'); // Id user Musoni

    $user_office = session('officeName'); // __get user's office name__

    $current_user_role = "";

    $user_roles = session('selectedRoles');
    foreach($user_roles as $role){
        if($role === "SUPER ADMIN"){
            $current_user_role = "SUPER ADMIN";
        break 1;
        }
    }


    // Ajout d'une information "active" à chaque validation
    foreach ($validations as $valide) {

    }

    // __Find permission&Roles
    $access = 0;
    foreach (session('selectedRoles') as $role) {
        if ($role['name'] === 'CREATION CLIENT') {
            $access = 1;
            break;
        } elseif ($role['name'] === 'DIRECTEUR' || $role['name'] === 'INFORMATIQUE' || $role['name'] === 'SUPER ADMIN' ) {
            $access = 2;
            break;
        } elseif ($role['name'] === 'APPROBATION 1 du PRET' || $role['name'] === "CHEF DAGENCE" || $role['name'] === 'DIRECTEUR DE RESEAU DAGENCES' || $role['name'] === "CHEF D AGENCE") {
            $access = 3;
            break;
        }
    }
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

                {{-- session error --}}
                @if($access === 3)
                <div class="card-header bg-gradient-primary text-white text-center d-flex align-content-center">
                    <i class="ri-file-list-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">validations</h4>
                </div>
                @else
                <div class="card-header bg-gradient-primary text-white text-center d-flex align-content-center">
                    <i class="ri-file-list-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1 fw-bold">Mes Demandes</h4>
                </div>
                @endif
                <div class="card-body bg-light">

                    {{-- if user is VALIDATOR --}}
                    @if($access === 3)
                    @php
                        // Palette de couleurs
                        $colorText = "#212529";
                        $colorPrimary = "#00574A";
                        $colorAccent = "#50c2bb";
                        $tdStyle = "font-size: 14pt;";
                        $spanStyle = "font-size: 14pt;";
                    @endphp
                    <div class="container-fluid table-responsive">
                        <table class="table table-hover" id="validation_table" style="font-size: 14pt;">
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
                                    {{-- php for hidden --}}
                                    @php
                                        // Montrer uniquement les demandes des agences autorisées et en statut "pending"
                                        $hidden = (in_array($validation->office_name, $allowed_offices) && $validation->status === "0") ? '' : 'hidden';
                                        $modalId = 'modal_' . $validation->ticket;
                                        $isRefused = $validation->status === "2";
                                    @endphp

                                    <tr {{$hidden}}>
                                        @if(\Carbon\Carbon::parse($validation->created_at)->format('Y-m-d') === $current_date)
                                        <td style="{{ $tdStyle }}">
                                            <strong>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="" class="nav-link mb-0 p-0" style="color: {{ $colorPrimary }};"> {{$validation->ticket}}</a>
                                                    <span class="badge rounded-pill" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}">New</span>
                                                </div>
                                            </strong>
                                        </td>
                                        @else
                                        <td style="{{ $tdStyle }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <strong><a href="" class="nav-link" style="color: {{ $colorPrimary }};">{{$validation->ticket}} - test</a></strong>
                                            </div>
                                        </td>
                                        @endif

                                        <td style="{{ $tdStyle }}">{{$validation->created_at}}</td>
                                        <td style="{{ $tdStyle }}"><strong style="color: {{ $colorPrimary }};">{{$validation->request_type}}</strong></td>
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
                                        {{-- Activate modal --}}
                                        <td style="{{ $tdStyle }}">
                                            <!-- Bouton pour ouvrir le modal -->
                                            <button type="button" class="btn btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}">
                                                Détails
                                            </button>
                                            <!-- Modal -->
                                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background: {{ $colorPrimary }}; color:#fff;">
                                                            <h5 class="modal-title" id="{{ $modalId }}Label">Détails de la demande</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Fermer">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{route('do.validation')}}" method="POST">
                                                            @csrf
                                                            <div class="modal-body" style="background: #f8f9fa;">
                                                                <div class="alert rounded-pill" role="alert" style="background: {{ $colorAccent }}; color:#fff; border:none;">
                                                                    <i class="ri-alert-line"></i>
                                                                    Merci de revérifier les informations client avant toute validation
                                                                </div>
                                                                <div class="text-left">
                                                                    <small style="color: {{ $colorPrimary }}; font-weight:bold;">Demande</small>
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorPrimary }};"><strong>Type de demande : </strong> {{$validation->request_type}}</li>
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorPrimary }};"><strong>Référence : </strong>{{ $validation->ticket }}</li>
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorPrimary }};"><strong>Demandeur : </strong>{{$validation->bank_agent}}</li>
                                                                        @if($validation->request_type === 'RESILIATION')
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorPrimary }};"><strong>Motif de la demande : </strong>{{ $validation->motif }}</li>
                                                                        @endif
                                                                    </ul>
                                                                    <hr>
                                                                    <small style="color: {{ $colorPrimary }}; font-weight:bold;">Détails client</small>
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorAccent }};"><strong>N° Mobile :</strong> {{$validation->mobile_no}}</li>
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorAccent }};"><strong>Nom et prénoms : </strong>{{$validation->om_lastname}} {{$validation->om_firstname}}</li>
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorAccent }};"><strong>Date de naissance : </strong>{{$validation->client_dob}}</li>
                                                                        <li class="list-group-item" style="background: #f8f9fa; border:1px solid {{ $colorAccent }};"><strong>Carte d'identité : </strong>{{$validation->om_cin}}</li>
                                                                    </ul>
                                                                </div>
                                                                <hr>
                                                                <div class="form-group text-left">
                                                                    <label for="validation" style="color: {{ $colorText }};"><small>État de validation</small></label>
                                                                    <select name="validation" id="validation" class="form-select" required style="border:1px solid {{ $colorPrimary }};">
                                                                        <option value="1">Validé</option>
                                                                        <option value="2">Refusé</option>
                                                                    </select>
                                                                </div>
                                                                <hr>
                                                                <div class="form-group text-left">
                                                                    <label for="commentaire" style="color: {{ $colorText }};"><small>Commentaires <i><strong>(10 caractères min)</strong></i></small></label>
                                                                    <textarea class="form-control" name="commentaire" rows="3" minlength="10" required style="border:1px solid {{ $colorPrimary }};"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer" style="background: #f8f9fa;">
                                                                <button type="button" class="btn btn-sm rounded-pill px-3" data-bs-dismiss="modal" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent;">Fermer</button>
                                                                <input type="hidden" name="ticket" value="{{$validation->ticket}}">
                                                                <button type="submit" class="btn btn-sm rounded-pill px-3" style="background: {{ $colorPrimary }}; color:#fff; border:none;">Valider</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        @elseif($validation->status === "3")
                                        <td style="{{ $tdStyle }}">
                                            <span class="badge rounded-pill px-3 py-1" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}">Refusé</span>
                                        </td>
                                        @else
                                        <td style="{{ $tdStyle }}">
                                            <span class="badge rounded-pill px-3 py-1" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}">Validé</span>
                                        </td>
                                        @endif

                                        <td style="{{ $tdStyle }}"><span style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Modal générique // popup after validation --}}
                        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="@if(session('success')) background: {{ $colorPrimary }}; @elseif(session('info')) background: {{ $colorAccent }}; @else background: {{ $colorText }}; @endif">
                                        <h5 class="modal-title" id="feedbackModalLabel">
                                            @if(session('success')) Succès
                                            @elseif(session('info')) Information
                                            @else Notification
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body" style="background: #f8f9fa;">
                                        @if(session('success'))
                                        {{ session('success') }}
                                        @elseif(session('info'))
                                        {{ session('info') }}
                                        @endif
                                    </div>
                                    <div class="modal-footer" style="background: #f8f9fa;">
                                        <button type="button" class="btn btn-sm rounded-pill px-3" data-bs-dismiss="modal" style="background: {{ $colorPrimary }}; color:#fff; border:none;">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Script pour afficher le modal si une session existe --}}
                        @if(session('success') || session('info'))
                        <script>
                            window.addEventListener('DOMContentLoaded', function() {
                                var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                                feedbackModal.show();
                            });
                        </script>
                        @endif
                    </div>

                    {{-- if user is SUPER ADMIN --}}
                    @elseif($access === 2)
                    @php
                        // Palette de couleurs
                        $colorText = "#212529";
                        $colorPrimary = "#00574A";
                        $colorAccent = "#50c2bb";
                        $tdStyle = "font-size: 14pt;";
                        $spanStyle = "font-size: 14pt;";
                    @endphp
                    <div class="container-fluid table-responsive">
                        <table class="table table-hover" id="validation_table" style="font-size: 14pt;">
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
                                $hidden = $validation->office_name ? '' : 'hidden';
                                $modalId = 'modal_' . $validation->ticket;
                                $isRefused = $validation->status === "2";
                                @endphp
                                <tr {{$hidden}}>
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
                                    <td style="{{ $tdStyle }}"><strong style="color: {{ $colorPrimary }};">{{$validation->request_type}}</strong></td>
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
                                        <span class="badge rounded-pill px-3 py-1" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}">Pending</span>
                                    </td>
                                    @elseif($isRefused)
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-3 py-1" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}">Refusé</span>
                                    </td>
                                    @else
                                    <td style="{{ $tdStyle }}">
                                        <span class="badge rounded-pill px-3 py-1" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}">Validé</span>
                                    </td>
                                    @endif

                                    <td style="{{ $tdStyle }}"><span style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- if user is CC --}}
                    @else
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
                                @php
                                    // Définition thèmes couleur table (navbar): #212529 (titre/texte sombre), #00574A (colonne/ligne/bloc vert foncé), #50c2bb (colonne/ligne/accent vert clair)
                                    $tablePrimary = '#00574A';
                                    $tableAccent = '#50c2bb';
                                    $tableHeader = '#212529';

                                    $typeColors = [
                                        'SOUSCRIPTION' => ['primary', 'ri-check-double-line'],
                                        'RESILIATION'  => ['danger', 'ri-close-circle-line'],
                                    ];
                                @endphp

                                @if (!function_exists('badge'))
                                    @php
                                        // Badge conserve l'icône SAUF pour colonne Statut (managé plus bas).
                                        function badge($text, $color = 'secondary', $icon = null, $pill = true, $extra = '') {
                                            $pillClass = $pill ? 'rounded-pill' : '';
                                            $iconHtml = $icon ? '<i class="'.$icon.' me-1"></i>' : '';
                                            return '<span class="badge '.$pillClass.' bg-'.$color.' '.$extra.'">'.$iconHtml.$text.'</span>';
                                        }
                                    @endphp
                                @endif

                                @foreach($validations as $validation)
                                    @php
                                        $hidden = in_array($validation->office_name, $allowed_offices) ? '' : 'hidden';

                                        $isSouscription = $validation->request_type === 'SOUSCRIPTION';
                                        $isResiliation = $validation->request_type === 'RESILIATION';

                                        $isValidationPending = $validation->status === "0";
                                        $isValidated = $validation->status === "1";
                                        $isRefused = $validation->status === "2";
                                        $validation->active = in_array($validation->key, $active_keys);

                                        $account_subscribed = DB::table('subscription')
                                            ->select('account_status')
                                            ->where('account_no', $validation->account_no)
                                            ->first();

                                        $subscribed = true;

                                        if ($account_subscribed === null || $account_subscribed === '0') {
                                            $subscribed = false;
                                        }

                                        // Styles unifiés
                                        $tdStyle = "font-size: 9pt;";
                                        $spanStyle = "font-size: 9pt;";
                                        $codeStyle = "font-size: 9pt;";

                                        // Palette
                                        $colorText = "#212529";   // Texte / neutre
                                        $colorPrimary = "#00574A"; // Principal
                                        $colorAccent = "#50c2bb";  // Accent
                                    @endphp

                                    {{-- SOUSCRIPTION - EN ATTENTE --}}
                                    @if($isSouscription && $isValidationPending && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorPrimary }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}"><i class="ri-check-double-line"></i> SOUSCRIPTION</span>
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
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-3 py-1 fw-normal" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}">En attente</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>

                                    {{-- SOUSCRIPTION VALIDÉE non activée --}}
                                    @elseif($isSouscription && $isValidated && $subscribed === true && $validation->final_status === null && $hidden === "")
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorPrimary }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}"><i class="ri-check-double-line"></i> SOUSCRIPTION</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorPrimary }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}"><code class="fw-bold" style="color: {{ $colorPrimary }}; {{ $codeStyle }}">{{ $validation->key }}</code></td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-star-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <form action="{{ route('activate.service') }}" method="POST" class="mb-0 d-flex flex-column align-items-center">
                                                    @csrf
                                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                                    <input type="hidden" name="mobile_no" value="{{ $validation->mobile_no }}">
                                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                                    <button type="submit" class="btn btn-sm rounded-pill px-4" title="Activer cette souscription" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}">
                                                        <i class="ri-flashlight-line"></i> Activer
                                                    </button>
                                                </form>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>
                                    
                                    {{-- RESILIATION - EN ATTENTE --}}
                                    @elseif($isResiliation && $isValidationPending && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorText }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-close-circle-line"></i> RESILIATION</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}"><code class="fw-bold" style="color: {{ $colorText }}; {{ $codeStyle }}">{{ $validation->key }}</code></td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-3 py-1 fw-normal" style="background: {{ $colorAccent }}; color:#fff; {{ $spanStyle }}">En attente</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>

                                    {{-- RESILIATION - VALIDATION --}}
                                    @elseif($isResiliation && $isValidated && $validation->active && $validation->final_status === null && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorText }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-close-circle-line"></i> RESILIATION</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}"><code class="fw-bold" style="color: {{ $colorText }}; {{ $codeStyle }}">{{ $validation->key }}</code></td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}"><i class="ri-user-star-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <form action="{{route('do.unsubscribe')}}" method="POST" class="mb-0 d-flex flex-column align-items-center">
                                                    @csrf
                                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                                    <input type="hidden" name="msisdn" value="{{ $validation->mobile_no }}">
                                                    <button type="submit" class="btn btn-sm rounded-pill px-4" title="Résilier ce compte" style="border:1px solid {{ $colorText }}; color: {{ $colorText }}; background: transparent; {{ $spanStyle }}">
                                                        <i class="ri-close-circle-line"></i> Résilier
                                                    </button>
                                                </form>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>
                                    
                                    {{-- SOUSCRIPTION REFUSÉE --}}
                                    @elseif($isSouscription && $isRefused && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorText }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-close-circle-line"></i> SOUSCRIPTION</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}"><code class="fw-bold" style="color: {{ $colorText }}; {{ $codeStyle }}">{{ $validation->key }}</code></td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-user-close-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}">Refusé</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>

                                    {{-- SOUSCRIPTION ACTIVÉE --}}
                                    @elseif($isSouscription && $isValidated && $subscribed === true &&  $validation->final_status === 'activated' && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorPrimary }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}"><i class="ri-check-double-line"></i> SOUSCRIPTION</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorPrimary }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}">
                                                <code class="fw-bold" style="color: {{ $colorPrimary }}; {{ $codeStyle }}">{{ $validation->key }}</code>
                                                <span class="badge rounded-pill ms-1" title="Souscription activée" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}"><i class="ri-checkbox-circle-fill"></i></span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}"><i class="ri-user-star-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $colorPrimary }}; color:#fff; {{ $spanStyle }}">Souscrit</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorPrimary }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>

                                    {{-- RESILIATION TERMINÉE --}}
                                    @elseif($isResiliation && $isValidated && $account_subscribed->account_status == '0' &&  $validation->final_status === 'resiliated' && $hidden === '')
                                        <tr class="align-middle border-0">
                                            <td class="fw-bold" style="color: {{ $colorText }}; {{ $tdStyle }}"><i class="ri-hashtag"></i> {{ $validation->ticket }}</td>
                                            <td style="{{ $tdStyle }}">{{ \Carbon\Carbon::parse($validation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}"><i class="ri-smartphone-line opacity-75"></i> {{ $validation->mobile_no }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="badge rounded-pill px-2 py-2 text-uppercase" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-close-circle-line"></i> RESILIATION</span></td>
                                            <td style="{{ $tdStyle }}"><span class="fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->account_no }}</span></td>
                                            <td style="{{ $tdStyle }}">
                                                <code class="fw-bold" style="color: {{ $colorText }}; {{ $codeStyle }}">{{ $validation->key }}</code>
                                                <span class="badge rounded-pill ms-1" title="Résilié" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-checkbox-circle-fill"></i></span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="border:1px solid {{ $colorPrimary }}; color: {{ $colorPrimary }}; background: transparent; {{ $spanStyle }}"><i class="ri-building-2-line"></i> {{ $validation->office_name }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-2" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}"><i class="ri-user-star-line"></i> {{ $validation->validator }}</span>
                                            </td>
                                            <td style="{{ $tdStyle }}">
                                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $colorText }}; color:#fff; {{ $spanStyle }}">Résilié</span>
                                            </td>
                                            <td style="{{ $tdStyle }}"><span class="small fw-normal" style="color: {{ $colorText }}; {{ $spanStyle }}">{{ $validation->motif_validation }}</span></td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <div class="pt-3 bg-light">
                        {{ $validations->links() }}
                    </div>
                </div>
            </div>

            {{-- datatable scripts --}}
            <script>
                $(document).ready(function() {
                    $('#validation_table').DataTable({
                        paging: false, // Désactive la pagination
                        info: false, // Désactive le texte "Affichage de X à Y sur Z entrées"
                        lengthChange: false, // Désactive le select "Show X entries"
                        searching: true, // Active la barre de recherche (par défaut true)
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