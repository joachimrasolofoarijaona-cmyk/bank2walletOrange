@extends('layouts.sidebar')

@section('title', ':: Souscription ::')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <div class="card">
                <div class="card-header bg-dark d-flex align-items-left">
                    <i class="ri-links-line fs-4"></i>
                    <h4 class="text-uppercase mb-0 px-3 pt-1">Souscription</h4>
                </div>
                <div class="card-body bg-dark">
                    <div class="container pb-3">
                        <div class="row">
                            {{-- about subscriptions --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card">
                                    <div class="card-header bg-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="ri-bookmark-line"></i>
                                            <h4 class="text-uppercase mb-0 px-3">conditions de souscription</h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="">
                                            <ul class="list-group list-group-flush d-flex align-items-start">
                                                <li class="list-group-item" style="font-size: 10pt;">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <i class="ri-smartphone-line fs-1" style="color: #4FC9C0;"></i>
                                                        </div>
                                                        <div class="col-10">
                                                            Avoir un numéro Orange actif et souscrit au service Orange Money en son nom ainsi qu'un compte ouvert ACEP avec numéro Orange enregistré dans la base MUSONI
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item" style="font-size: 10pt;">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <i class="ri-key-2-line fs-1" style="color: #4FC9C0;"></i>
                                                        </div>
                                                        <div class="col-10">
                                                            Disposer d'une clé d'activation fournie par Orange après avoir fait le code <strong><span style="color: #4FC9C0;">#144*4#</span></strong>  puis <i class="ri-phone-line fs-6" style="color: #4FC9C0;"></i></i>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item" style="font-size: 10pt;">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <i class="ri-bank-line fs-1" style="color: #4FC9C0;"></i>
                                                        </div>
                                                        <div class="col-10">
                                                            Fournir ce code à l'agent bancaire pour procéder à l'étape suivante de la souscription
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- form for subscriptions --}}
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="card">
                                    <div class="card-header bg-dark">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="ri-survey-line"></i>
                                            <h4 class="text-uppercase mb-0 px-3">formulaire de souscription</h4>
                                        </div>
                                    </div>
                                    <div class="card-body text-dark">
                                        <form action="{{route('send.subscribe')}}" method="POST" accept-charset="UTF-8">
                                            @csrf
                                            <div class="form-group pt-2">
                                                <label for="msisdn">
                                                    <small>Numéro Orange : </small>
                                                </label>
                                                <input type="text" class="form-control" placeholder="Numéro de tel" value="" name="msisdn" id="msisdn" required>
                                            </div>
                                            <div class="form-group pt-2">
                                                <label for="key">
                                                    <small>Clé d'activation : </small>
                                                </label>
                                                <input type="text" class="form-control" placeholder="Clé d'activation" value="" name="key" id="key" required>
                                            </div>
                                            <hr>
                                            <div class="col-12 pt-1 pb-3">
                                                <button type="submit" class="btn btn-outline-success ">Envoyer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- error section --}}
                    @if (session('error'))
                    <div class="container pt-3">
                        <div class="alert alert-danger d-flex align-items-left">
                            <i class="ri-error-warning-line fs-4"></i>
                            <h6 class="mb-0 px-3 pt-2"> {{ session('error') }}</h6>
                        </div>
                    </div>
                    @endif
                    {{-- error section --}}
                </div>
                <div class="card-footer bg-warning tex-light d-flex align-items-center">
                    <i class="ri-error-warning-line fs-2"></i>
                    <h5 class="px-2 pt-1">
                        Il est impératif que le client dispose de ces informations pour pouvoir passer à la suite des opérations.
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection