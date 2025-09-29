@extends('layouts.sidebar')

@section('title', 'Souscription')

@section('header')
Bienvenue {{ session('firstname') }}
@endsection


@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                <div class="card-header bg-dark d-flex align-items-left">
                    <i class="ri-equalizer-line fs-5"></i>
                    <h4 class="text-uppercase mb-0 px-3">controle kyc</h4>
                </div>
                <div class="container bg-dark pt-2">
                    <div class="alert alert-warning text-center d-flex align-content-center">
                        <i class="ri-alert-line fs-2"></i>
                        <p class="pt-3 px-3"> Prière de bien vérifier les données client avant de soumettre pour validation.
                            Les données non valides ne seront pas traitées.
                        </p>
                    </div>
                </div>
                <div class="card-body bg-dark">
                    <div class="container">
                        <div class="row">
                            {{-- orange money kyc --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card">
                                    <div class="card-header bg-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="ri-smartphone-line fs-3"></i>
                                            <h4 class="text-uppercase mb-0 px-1">orange money kyc</h4>
                                        </div>
                                    </div>
                                    <div class="card-body bg-dark">
                                        @isset($data)
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Numéro : </strong> <span>{{$msisdn}}</span></li>
                                            <li class="list-group-item"><strong>Nom :</strong> {{ $om_lastname }}</li>
                                            <li class="list-group-item"><strong>Prénom :</strong> {{ $om_firtsname  }}</li>
                                            <li class="list-group-item"><strong>Date de naissance :</strong> {{ $om_birthdate }}</li>
                                            <li class="list-group-item"><strong>CIN :</strong> {{ $om_cin }}</li>
                                        </ul>
                                        @else
                                        <p>Aucune donnée Orange retournée.</p>
                                        @endisset
                                    </div>

                                    <div class="card-footer bg-dark text-center">
                                        <p>Les infomations données par Orange Money</p>
                                    </div>
                                </div>
                            </div>
                            {{-- acep kyc --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card">
                                    <div class="card-header bg-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="ri-bank-fill fs-3"></i>
                                            <h4 class="text-uppercase mb-0 px-1">ACEP kyc</h4>
                                        </div>
                                    </div>
                                    <div class="card-body bg-dark">
                                        @isset($customer_firstname)
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Numéro :</strong> {{ $customer_mobile_no }}</li>
                                            <li class="list-group-item"><strong>Nom :</strong> {{ $customer_lastname }}</li>
                                            <li class="list-group-item"><strong>Prénom :</strong> {{ $customer_firstname  }}</li>
                                            <li class="list-group-item"><strong>Date de naissance :</strong> {{ $customer_date_of_birth }}</li>
                                            <li class="list-group-item"><strong>CIN :</strong> {{ $customer_cin }}</li>
                                        </ul>
                                        @else
                                        <p>Aucune donnée ACEP retournée.</p>
                                        @endisset
                                    </div>

                                    <div class="card-footer bg-dark text-center">
                                        <p>Les infomations données par Musoni</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($verified_cin != $customer_cin)
                <div class="card-footer bg-dark">
                    <div class="alert alert-danger text-center" role="alert">
                        <div class="icon">
                            <i class="ri-alert-line"></i>
                            Les Cartes d'Identité Nationale ne correspondent pas! Merci de vérifiers
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <a href="{{ route('show.subscribe') }}" class="btn btn-outline-danger">Annuler demande</a>
                        </div>
                    </div>
                </div>
                @else
                <div class="card-footer bg-dark">
                    <div class="alert alert-success text-center" role="alert">
                        <div class="icon">
                            <i class="ri-check-line"></i>
                            Identité vérifiée, vous pouvez poursuivre la souscription
                        </div>
                    </div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-auto d-flex justify-content-center gap-3">
                                <!-- Bouton Annuler -->
                                <a href="{{ route('show.subscribe') }}" class="btn btn-outline-danger">Annuler</a>

                                <!-- Formulaire avec bouton Poursuivre -->
                                <form action="{{ route('confirm.sub') }}" method="POST">
                                    @csrf
                                    <div class="form-group d-none">
                                        <input type="text" class="form-control" name="msisdn" id="msisdn" value="{{$msisdn}}">
                                        <input type="text" class="form-control" name="key" id="key" value="{{$key}}" required>
                                        <input type="text" class="form-control" name="omCin" id="omCin" value="{{$om_cin}}" required>
                                    </div>
                                    <button type="submit" class="btn btn-outline-success">Poursuivre</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection