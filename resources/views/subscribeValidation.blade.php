@extends('layouts.sidebar')

@section('title', ':: Validations ::')

@section('content')
@php

use Illuminate\Support\Facades\DB;
use App\Models\Subscription;

// $current_date = date('Y-m-d H:i:s');
$current_date = '2025-05-02';

$validations = DB::table('validation')
->orderBy('created_at', 'desc')
->paginate(10);

$validator_officename = session('officeName');

// Récupération de toutes les clés existantes dans Subscription
$active_keys = Subscription::pluck('key')->toArray();


// Ajout d'une information "active" à chaque validation
foreach ($validations as $validation) {
$validation->active = in_array($validation->key, $active_keys);
}

$get_zone_id = DB::table('zones')
->select('id')
->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?", $validator_officename)
->first();

$allowed_offices = [];

if ($get_zone_id) {
// Si c'est une zone, récupérer toutes les agences qui en dépendent
$get_agences = DB::table('agences')
->where('zone_id', $get_zone_id->id)
->pluck('nom')
->toArray();

$allowed_offices = $get_agences;
} else {
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
} elseif ($role['name'] === 'DIRECTEUR' || $role['name'] === 'INFORMATIQUE' || $role['name'] === 'SUPER ADMIN') {
$access = 2;
break;
} elseif ($role['name'] === 'APPROBATION 1 du PRET' || $role['name'] === "CHEF D'AGENCE") {
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
                                {{-- php for hidden --}}
                                @php
                                $hidden = $validation->office_name ? '' : 'hidden';
                                $modalId = 'modal_' . $validation->ticket;
                                $isRefused = $validation->status === "2";
                                @endphp

                                <tr {{$hidden}}>
                                    @if(\Carbon\Carbon::parse($validation->created_at)->format('Y-m-d') === $current_date)
                                    <td>
                                        <strong>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="" class="nav-link mb-0 p-0"> {{$validation->ticket}}</a>
                                                <span class="badge bg-success">New</span>
                                            </div>
                                        </strong>
                                    </td>
                                    @else
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong><a href="" class="nav-link">{{$validation->ticket}} - test</a></strong>
                                        </div>
                                    </td>
                                    @endif

                                    <td>{{$validation->created_at}}</td>
                                    <td><strong>{{$validation->request_type}}</strong></td>
                                    <td>{{$validation->mobile_no}}</td>
                                    <td>{{$validation->account_no}}</td>
                                    <td>{{$validation->key}}</td>
                                    <td>{{$validation->office_name}}</td>
                                    <td>{{$validation->bank_agent}}</td>

                                    @if($validation->status === "0")
                                    {{-- Activate modal --}}
                                    <td>
                                        <!-- Bouton pour ouvrir le modal -->
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                            Détails
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark">
                                                        <h5 class="modal-title" id="{{ $modalId }}Label">Détails de la demande</h5>
                                                        <button type="button" class="btn-close " data-dismiss="modal" aria-label="Fermer" style="color:white;">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{route('do.validation')}}" method="POST">
                                                        @csrf
                                                        <div class="modal-body bg-dark">
                                                            <div class="alert alert-warning" role="alert">
                                                                <i class="ri-alert-line"></i>
                                                                Merci de revérifier les informations client avant toute validation
                                                            </div>
                                                            <div class="text-left">
                                                                <small>Demande</small>
                                                                <ul class="list-group">
                                                                    <li class="list-group-item list-group-item-secondary"><strong>Type de demande : </strong> {{$validation->request_type}}</li>
                                                                    <li class="list-group-item list-group-item-secondary"><strong>Référence : </strong>{{ $validation->ticket }}</li>
                                                                    <li class="list-group-item list-group-item-secondary"><strong>Demandeur : </strong>{{$validation->bank_agent}}</li>
                                                                    @if($validation->request_type === 'RESILIATION')
                                                                    <li class="list-group-item list-group-item-secondary"><strong>Motif de la demande : </strong>{{ $validation->motif }}</li>
                                                                    @endif
                                                                </ul>
                                                                <hr>
                                                                <small>Détails client</small>
                                                                <ul class="list-group">
                                                                    <li class="list-group-item list-group-item-primary"><strong>N° Mobile :</strong> {{$validation->mobile_no}}</li>
                                                                    <li class="list-group-item list-group-item-primary"><strong>Nom et prénoms : </strong>{{$validation->om_lastname}} {{$validation->om_firstname}}</li>
                                                                    <li class="list-group-item list-group-item-primary"><strong>Date de naissance : </strong>{{$validation->client_dob}}</li>
                                                                    <li class="list-group-item list-group-item-primary"><strong>Carte d'identité : </strong>{{$validation->om_cin}}</li>
                                                                </ul>
                                                            </div>
                                                            <hr>
                                                            <div class="form-group text-left">
                                                                <label for="validation"><small>État de validation</small></label>
                                                                <select name="validation" id="validation" class="form-select" required>
                                                                    <option value="1">Validé</option>
                                                                    <option value="2">Refusé</option>
                                                                </select>
                                                            </div>
                                                            <hr>
                                                            <div class="form-group text-left">
                                                                <label for="commentaire"><small>Commentaires <i><strong>(10 caractères min)</strong></i></small></label>
                                                                <textarea class="form-control" name="commentaire" rows="3" minlength="10" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-dark">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            <input type="hidden" name="ticket" value="{{$validation->ticket}}">
                                                            <button type="submit" class="btn btn-outline-danger">Valider</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @elseif($validation->status === "3")
                                    <td style="color : red;"><span class="badge bg-danger">Refusé</span></td>
                                    @else
                                    <td style="color : red;"><span class="badge bg-success">Validé</span></td>
                                    @endif

                                    <td>{{ $validation->motif_validation }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Modal générique // popup after validation --}}
                        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header 
                                                @if(session('success')) bg-success 
                                                @elseif(session('info')) bg-info 
                                                @else bg-secondary 
                                                @endif text-white">
                                        <h5 class="modal-title" id="feedbackModalLabel">
                                            @if(session('success')) Succès
                                            @elseif(session('info')) Information
                                            @else Notification
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body bg-dark">
                                        @if(session('success'))
                                        {{ session('success') }}
                                        @elseif(session('info'))
                                        {{ session('info') }}
                                        @endif
                                    </div>
                                    <div class="modal-footer bg-dark">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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
                                $hidden = $validation->office_name ? '' : 'hidden';
                                $modalId = 'modal_' . $validation->ticket;
                                $isRefused = $validation->status === "2";
                                @endphp
                                <tr {{$hidden}}>
                                    @if(\Carbon\Carbon::parse($validation->created_at)->format('Y-m-d') === $current_date)
                                    <td>
                                        <strong>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="" class="nav-link mb-0 p-0"> {{$validation->ticket}} </a>
                                                <span class="badge bg-success">New</span>
                                            </div>
                                        </strong>
                                    </td>
                                    @else
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong><a href="" class="nav-link">{{$validation->ticket}}</a></strong>
                                        </div>
                                    </td>
                                    @endif

                                    <td>{{$validation->created_at}}</td>
                                    <td><strong>{{$validation->request_type}}</strong></td>
                                    <td>{{$validation->mobile_no}}</td>
                                    <td>{{$validation->account_no}}</td>
                                    <td>{{$validation->key}}</td>
                                    <td>{{$validation->office_name}}</td>
                                    <td>{{$validation->bank_agent}}</td>

                                    @if($validation->status === "0")
                                    <td style="color : red;"><span class="badge bg-warning">Pending</span></td>
                                    </td>
                                    @elseif($isRefused)
                                    <td style="color : red;"><span class="badge bg-danger">Refusé</span></td>
                                    @else
                                    <td style="color : red;"><span class="badge bg-success">Validé</span></td>
                                    @endif

                                    <td>{{ $validation->motif_validation }}</td>
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
                                    <th scope="col">Valideur</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Commentaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validations as $validation)
                                    @php
                                        $hidden = in_array($validation->office_name, $allowed_offices) ? '' : 'hidden';

                                        $isSouscription = $validation->request_type === 'SOUSCRIPTION';
                                        $isResiliation = $validation->request_type === 'RESILIATION';

                                        $isValidationPending = $validation->status === "0"; // demande en attente de validation
                                        $isValidated = $validation->status === "1"; // demande validée
                                        $isRefused = $validation->status === "2"; // Demande refusée
                                        $validation->active = in_array($validation->key, $active_keys);

                                        $isActiveInSubscription = $validation->active;

                                        # function to check if account already subscribed
                                        $account_subscribed = DB::table('subscription')
                                            ->select('account_status')
                                            ->where('account_no', $validation->account_no)
                                            ->where('msisdn', $validation->mobile_no)
                                            ->where('account_status', '1')
                                            ->first();

                                    @endphp

                                    {{-- Cas 1 : si SOUSCRIPTION en attente de validation --}}
                                    @if($validation->request_type === 'SOUSCRIPTION' && $validation->status === "0" && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-success">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td><span class="badge bg-warning text-dark">En attente validation</span></td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 2 : si SOUSCRIPTION, VALIDEE, mais pas encore activée --}}
                                    @elseif($validation->request_type === 'SOUSCRIPTION' && $isValidated && $account_subscribed === null && $hidden === "")
                                    
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-success">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td>
                                                account is {{$account_subscribed}}
                                                <form action="{{ route('activate.service') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                                    <input type="hidden" name="mobile_no" value="{{ $validation->mobile_no }}">
                                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">

                                                    <button type="submit" class="btn btn-outline-success">Activer</button>
                                                </form>
                                            </td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 3 : RESILIATION en attente de validation --}}
                                    @elseif($isResiliation && $isValidationPending && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-danger">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td><span class="badge bg-warning text-dark">En attente validation</span></td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 4 : RESILIATION validée mais pas encore exécutée --}}
                                    @elseif($isResiliation && $isValidated && $validation->active && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-danger">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td>
                                                <form action="{{route('do.unsubscribe')}}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="ticket" value="{{ $validation->ticket }}">
                                                    <input type="hidden" name="key" value="{{ $validation->key }}">
                                                    <input type="hidden" name="account_no" value="{{ $validation->account_no }}">
                                                    <input type="hidden" name="msisdn" value="{{ $validation->mobile_no }}">
                                                    <button type="submit" class="btn btn-outline-danger">Résilier</button>
                                                </form>
                                            </td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 5 : SOUSCRIT ET REFUSE --}}
                                    @elseif($isSouscription && $isRefused && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong>{{ $validation->request_type }}</strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td style="color: red;">Refusé</td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 6 : VALIDEE et SOUSCRIT --}}
                                    @elseif($isSouscription && $isValidated && $account_subscribed && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-success">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td><span class="badge bg-success">Validé</span></td>
                                            <td>{{ $validation->motif_validation }}</td>
                                        </tr>

                                    {{-- Cas 7 : VALIDEE et RESILIEE --}}
                                    @elseif($isResiliation && $isValidated && $validation->active && $hidden === '')
                                        <tr>
                                            <td><strong>{{ $validation->ticket }}</strong></td>
                                            <td>{{ $validation->created_at }}</td>
                                            <td>{{ $validation->mobile_no }}</td>
                                            <td><strong><span class="badge bg-danger">{{ $validation->request_type }}</span></strong></td>
                                            <td>{{ $validation->account_no }}</td>
                                            <td>{{ $validation->key }}</td>
                                            <td>{{ $validation->office_name }}</td>
                                            <td>{{ $validation->validator }}</td>
                                            <td><span class="badge bg-success">Validé</span></td>
                                            <td>{{ $validation->motif_validation }}</td>
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