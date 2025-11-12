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
@php
    $colorText = "#212529";
    $colorPrimary = "#00574A";
    $colorAccent = "#50c2bb";
@endphp
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex align-items-center" style="background: {{ $colorPrimary }}; color:#fff;">
            <i class="ri-send-plane-line fs-4 me-2"></i>
            <h4 class="text-uppercase mb-0 fw-bold">Formulaire de souscription</h4>
        </div>
        <div class="card-body" style="background: #f8f9fa;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body p-4">
                                <form action="{{route('send.subscription.validation')}}" method="POST" accept-charset="UTF-8">
                                    @csrf
                                    
                                    {{-- Numéro de téléphone --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: {{ $colorPrimary }};">
                                            <i class="ri-smartphone-line me-2"></i>Numéro de téléphone
                                        </label>
                                        <input class="form-control form-control-lg" type="text" name="msisdn" value="{{ $msisdn ?? '' }}" readonly style="border: 2px solid {{ $colorAccent }}; background: #fff;">
                                    </div>

                                    {{-- Clé d'activation --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold mb-2" style="color: {{ $colorPrimary }};">
                                            <i class="ri-key-line me-2"></i>Clé d'activation
                                        </label>
                                        <input class="form-control form-control-lg" type="text" name="key" value="{{ $key ?? '' }}" readonly style="border: 2px solid {{ $colorAccent }}; background: #fff;">
                                    </div>

                                    {{-- Code de service --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold mb-3" style="color: {{ $colorPrimary }};">
                                            <i class="ri-service-line me-2"></i>Code de service
                                        </label>
                                        <div class="card p-3" style="background: #fff; border: 1px solid {{ $colorAccent }};">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="code_service" id="b2w" value="1" checked style="border-color: {{ $colorPrimary }};">
                                                <label class="form-check-label fw-normal" for="b2w" style="color: {{ $colorText }};">
                                                    <i class="ri-arrow-right-line me-1" style="color: {{ $colorPrimary }};"></i>Bank To Wallet
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="code_service" id="w2b" value="2" style="border-color: {{ $colorPrimary }};">
                                                <label class="form-check-label fw-normal" for="w2b" style="color: {{ $colorText }};">
                                                    <i class="ri-arrow-left-line me-1" style="color: {{ $colorPrimary }};"></i>Wallet To Bank
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="code_service" id="all" value="3" style="border-color: {{ $colorPrimary }};">
                                                <label class="form-check-label fw-normal" for="all" style="color: {{ $colorText }};">
                                                    <i class="ri-exchange-line me-1" style="color: {{ $colorPrimary }};"></i>Bank To Wallet / Wallet To Bank
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Liste des comptes --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold mb-3" style="color: {{ $colorPrimary }};">
                                            <i class="ri-bank-line me-2"></i>Liste des comptes à lier
                                        </label>

                                        @if(isset($customer_account) && count($customer_account) > 0)
                                            <div class="card p-3" style="background: #fff; border: 1px solid {{ $colorAccent }};">
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

                                                    <div class="form-check mb-3 p-3 rounded" style="background: {{ $disable ? '#f8f9fa' : '#fff' }}; border: 1px solid {{ $disable ? '#dee2e6' : $colorAccent }}; transition: all 0.3s;">
                                                        <input class="form-check-input" type="radio" name="accounts" id="account-{{ $account['accountNo'] }}" value="{{ $account['accountNo'] }}" {{ $disable }} style="border-color: {{ $colorPrimary }};">

                                                        <label class="form-check-label w-100" for="account-{{ $account['accountNo'] }}" style="cursor: {{ $disable ? 'not-allowed' : 'pointer' }};">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <strong style="color: {{ $colorPrimary }}; font-size: 1.1em;">{{ $account['accountNo'] }}</strong>
                                                                    <span class="text-muted ms-2">- {{ $account['productName'] }}</span>
                                                                </div>
                                                                <div>
                                                                    @if($account_subscribed && $account_subscribed->account_status === "1")
                                                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                                                            <i class="ri-links-line me-1"></i>Compte déjà lié
                                                                        </span>
                                                                    @elseif($account_subscribed && $account_subscribed->account_status === "0")
                                                                        <span class="badge rounded-pill bg-danger px-3 py-2">
                                                                            <i class="ri-close-circle-line me-1"></i>Compte résilié
                                                                        </span>
                                                                    @else
                                                                        <span class="badge rounded-pill bg-success px-3 py-2">
                                                                            <i class="ri-checkbox-circle-line me-1"></i>Disponible
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning d-flex align-items-center" role="alert" style="border-left: 4px solid #ffc107;">
                                                <i class="ri-alert-line fs-4 me-3"></i>
                                                <div>
                                                    <strong>Aucun compte disponible</strong>
                                                    <p class="mb-0">Aucun compte disponible pour ce client.</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Bouton Suivant --}}
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-lg rounded-pill px-5" style="background: {{ $colorPrimary }}; color:#fff; border:none; font-weight:bold;">
                                            <i class="ri-arrow-right-line me-2"></i>Suivant
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer d-flex align-items-center" style="background: {{ $colorAccent }}; color:#fff; border:none;">
                                <i class="ri-information-line fs-5 me-3"></i>
                                <p class="mb-0 fw-normal">Il est impératif que le client dispose de ces informations pour pouvoir passer à la suite des opérations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end" style="background: #f8f9fa; border-top: 1px solid #dee2e6; font-size: 11px; color: {{ $colorText }};">
            <strong>Bank To Wallet / Wallet To Bank Project</strong>
            <p class="mb-0">Copyright &copy; Jun. 2025 - DSI ACEP Madagascar</p>
        </div>
    </div>
</div>
@endif


@endsection