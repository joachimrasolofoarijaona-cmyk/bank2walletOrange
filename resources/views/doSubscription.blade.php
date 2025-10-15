@extends('layouts.sidebar')

@section('title', 'Souscription')

@section('content')
@php
use Illuminate\Support\Facades\DB;
@endphp

@if(session()->has('error'))
<div class="content">
    <div class="content-header">
        <div class="container">
            <div class="container-fluid">
                <div class="card bg-dark elevation-2">
                    <div class="card-header">
                        <h3 class="m-0">Erreur de souscription</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger" role="alert">
                            {{session('error')}}
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('show.subscribe') }}" class="btn btn-outline-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@else

<div class="container-fluid">
    <div class="form-card">
        <div class="card-header d-flex align-items-center">
            <i class="ri-send-plane-line fs-4 me-2"></i>
            <h4 class="text-uppercase mb-0 fw-bold">Envoie pour validation</h4>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="form-card">
                            <div class="card-header text-center">
                                <i class="ri-file-text-line me-2"></i>
                                FORMULAIRE DE SOUSCRIPTION
                            </div>
                            <div class="card-body">
                                <style>
                                    small {
                                        color: #4FC9C0;
                                    }
                                </style>
                                <form action="{{route('send.subscription.validation')}}" method="POST" accept-charset="UTF-8">
                                    @csrf
                                    <div class="d-flex align-items-start gap-3 py-2">
                                        <div class="nom"><small>Numéro de ligne : </small> </div>
                                        <input class="form-control bg-secondary text-white" type="text" name="msisdn" value="{{ $msisdn ?? '' }}" readonly>
                                    </div>
                                    <div class="d-flex align-items-start gap-4 py-2">
                                        <div class="nom"><small>Clé d'activation :</small> </div>
                                        <input class="form-control bg-secondary text-white" type="text" name="key" value="{{ $key ?? '' }}" readonly>
                                    </div>
                                    <div class="form-group py-2">
                                        <label for="code_service"> <small>Code de service :</small> </label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="code_service" id="b2w" value="1" checked>
                                            <label class="form-check-label" for="b2w">Bank To Wallet</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="code_service" id="w2b" value="2">
                                            <label class="form-check-label" for="w2b">Wallet To Bank</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="code_service" id="all" value="3">
                                            <label class="form-check-label" for="all">Bank To Wallet / Wallet To Bank</label>
                                        </div>
                                    </div>

                                    <div class="form-group py-2">
                                        <label for="accounts"><small>Liste des comptes à lier :</small></label>

                                        @if(isset($customer_account) && count($customer_account) > 0)
                                            @foreach($customer_account as $account)
                                                @php
                                                    $account_subscribed = DB::table('subscription')
                                                        ->select('account_status')
                                                        ->where('account_no', $account['accountNo'])
                                                        ->latest('updated_at')
                                                        ->first();

                                                    // Si la ligne existe et que account_status vaut 1, on désactive
                                                    $disable = ($account_subscribed && $account_subscribed->account_status === "1") ? 'disabled' : '';
                                                @endphp

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="accounts" id="account-{{ $account['accountNo'] }}" value="{{ $account['accountNo'] }}" {{ $disable }}>

                                                    <label class="form-check-label" for="account-{{ $account['accountNo'] }}">
                                                        @if($account_subscribed && $account_subscribed->account_status === "1")
                                                            <strong><i>{{ $account['accountNo'] }}</i></strong class="pe-2"> - {{ $account['productName'] }}<span class="badge bg-warning "> Compte déjà lié</span>
                                                        @elseif($account_subscribed && $account_subscribed->account_status === "0")
                                                            <strong><i>{{ $account['accountNo'] }}</i></strong class="pe-2"> - {{ $account['productName'] }} <span class="badge bg-danger "> Compte résilié</span>
                                                        @else
                                                            <strong><i>{{ $account['accountNo'] }}</i></strong class="pe-2"> - {{ $account['productName'] }} <span class="badge bg-success "> Disponible</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Aucun compte disponible pour ce client.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-outline-primary btn-block">Suivant</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer bg-warning tex-light d-flex align-item-center">
                                <i class="fas fa-solid fa-exclamation"></i>
                                <h6 class="px-2">Il est impératif que le client dispose de ces informations pour pouvoir passer à la suite des opérations.</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-dark text-end pt-4" style="font-size: 11px;">
            Bank To Wallet / Wallet To Bank Project
            <p>Copyright &copy; Jun. 2025 - DSI ACEP Madagascar</p>
        </div>
    </div>
</div>
@endif


@endsection