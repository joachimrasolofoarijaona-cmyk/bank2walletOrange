@extends('layouts.sidebar')

@section('title', ':: Service Actif ::')

@section('content')



<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                <div class="card-header bg-dark d-flex align-items-left">
                    <i class="ri-check-double-line fs-5"></i>
                    <h4 class="text-uppercase mb-0 px-3">Service Actif</h4>
                </div>
                <div class="card-body bg-dark">
                    <div class="container">
                        
                        @if(isset($msisdn))
                        <p class="text-center text-success fs-4">Le service a été activé avec succès !</p>
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <i class="ri-check-double-line fs-1 text-success"></i>
                            <h5 class="text-uppercase text-white">Détails du service</h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Numéro de ligne : </strong> <span>{{$msisdn}}</span></li>
                            <li class="list-group-item"><strong>Alias : </strong> <span>{{$alias}}</span></li>  
                            <li class="list-group-item"><strong>Code Service : </strong> <span>{{$code_service}}</span></li>
                            <li class="list-group-item"><strong>Libellé : </strong> <span>{{$libelle}}</span></li>
                            <li class="list-group-item"><strong>Devise : </strong> <span>{{$currency}}</span></li>
                            <li class="list-group-item"><strong>Clé d'activation : </strong> <span>{{$key}}</span></li>
                            <li class="list-group-item"><strong>Client ID : </strong> <span>{{$customer_id}}</span></li>
                            <li class="list-group-item"><strong>CIN : </strong> <span>{{$customer_cin}}</span></li>
                            <li class="list-group-item"><strong>Prénom : </strong> <span>{{$customer_firtsname}}</span></li>
                            <li class="list-group-item"><strong>Nom : </strong> <span>{{$customer_lastname}}</span></li>
                            <li class="list-group-item"><strong>Date de naissance : </strong> <span>{{$customer_birthdate}}</span></li>
                        </ul>
                        <p class="text-center text-white fs-4">Merci de votre confiance.</p>
                        @else
                            <p class="text-center text-white fs-4">Aucun service actif trouvé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection