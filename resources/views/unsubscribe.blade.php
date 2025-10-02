@extends('layouts.sidebar')

@section('title', ':: Résiliation ::')

@section('content')
<div class="container-fluid">
    <div class="container">
        {{-- Search bar to find msisdn --}}
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card  bg-lilght text-dark">
                    {{-- Card header with search icon and title --}}
                    <div class="card-header  bg-lilght  text-dark d-flex align-items-center">
                        <i class="ri-search-line fs-4"></i>
                        <h4 class="text-uppercase mb-0 px-3">Trouver un client</h4>
                    </div>
                    <div class="card-body  bg-lilght  text-dark">
                        {{-- Form to search for a customer by msisdn --}}
                        <form method="POST" action="{{ route('search.customer') }}">
                            {{-- CSRF token for form submission --}}
                            @csrf
                            <div class="mb-3">
                                <label for="msisdn" class="form-label">Numéro Orange</label>
                                <input type="text" class="form-control" id="msisdn" name="msisdn" placeholder="032**** ou 037****" required>
                            </div>
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="ri-search-line"></i> Chercher Numéro
                            </button>
                        </form>
                    </div>
                    {{-- Session error --}}
                    @if(session('error') && session('error') !== '')
                    {{-- Display error message if session has an error --}}
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong>Erreur !</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Success message --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong>Succès !</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    {{-- Display a responsive table if customer is found --}}
    @if(isset($customer) && !$customer->isEmpty())
    <div class="container mt-5">
        <div class="table-responsive  text-dark">
            <table class="table  table-hover">
                <thead class="table-light text-start text-uppercase">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Ligne</th>
                        <th scope="col">Client</th>
                        <th scope="col">Compte Rattaché</th>
                        <th scope="col">Service</th>
                        <th scope="col">Type de compte</th>
                        <th scope="col">Date de souscription</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody >
                    @foreach($customer as $cust)

                    <tr class="bg-white text-start text-dark">
                        <td>{{ $cust->id }}</td>
                        <td>{{ $cust->msisdn }}</td>
                        <td>{{ $cust->client_lastname }} {{ $cust->client_firstname }}</td>
                        <td>{{ $cust->account_no }}</td>
                        <td>
                            @if($cust->code_service === "1")
                            Bank To Wallet
                            @elseif($cust->code_service === "2")
                            Wallet To Bank
                            @elseif($cust->code_service === "3")
                            Bank To Wallet / Wallet To Bank
                            @else
                            Service Inconnu
                            @endif
                        </td>
                        <td>{{ $cust->libelle }}</td>
                        <td>{{ $cust->created_at }}</td>
                        @if($cust->account_status === "1")
                        <td>
                            <!-- Bouton ouverture modal -->
                            <button type="button"
                                class="btn btn-outline-danger btn-sm delete-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-{{ $cust->id }}">
                                <i class="ri-delete-bin-line text-danger"></i>
                            </button>

                            <style>
                                .delete-btn:hover {
                                    background-color: #dc3545 !important;
                                    /* rouge bootstrap */
                                    color: white !important;
                                    /* texte/icône deviennent blancs */
                                }

                                .delete-btn:hover i {
                                    color: white !important;
                                    /* icône en blanc */
                                }
                            </style>

                        </td>
                        @else
                        <td><strong><span class="badge bg-danger">Compte Résilié</span></strong></td>
                        @endif
                    </tr>
                    <!-- Modal pour chaque client -->
                    <div class="modal fade" id="modal-{{ $cust->id }}" tabindex="-1" aria-labelledby="ModalLabel-{{ $cust->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header  bg-lilght text-uppercase">
                                    <h6 class="modal-title" id="ModalLabel-{{ $cust->id }}">Résilier un client</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body  bg-lilght  text-dark">
                                    <form action="{{ route('send.unsubscribe.validation') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="msisdn" value="{{ $cust->msisdn }}">
                                        <input type="hidden" name="account_no" value="{{ $cust->account_no }}">
                                        <input type="hidden" name="libelle" value="{{ $cust->libelle }}">

                                        <div class="alert alert-warning d-flex align-content-center">
                                            <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                                            Prière de bien confirmer la ligne concernée !
                                        </div>

                                        <p><strong>Numéro Orange :</strong> {{ $cust->msisdn }}</p>
                                        <p><strong>Compte :</strong> {{ $cust->account_no }}</p>

                                        <div class="mb-3">
                                            <label for="origin-{{ $cust->id }}">Origine de résiliation :</label>
                                            <select name="origin" id="origin-{{ $cust->id }}" class="form-control">
                                                <option value="Orange">1- Orange</option>
                                                <option value="Bank">2- ACEP</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="motif-{{ $cust->id }}">Motif de résiliation :</label>
                                            <select name="motif" id="motif-{{ $cust->id }}" class="form-control"
                                                onchange="showDiv('autre_motif_div-{{ $cust->id }}', this)">
                                                <option value="Changement de numéro de téléphone">Changement de numéro de téléphone</option>
                                                <option value="Insatisfaction du service">Insatisfaction du service</option>
                                                <option value="Frais jugés trop élevés">Frais jugés trop élevés</option>
                                                <option value="Utilisation d'un autre service de transfert d'argent">Utilisation d'un autre service</option>
                                                <option value="Autre">Autre (À préciser)</option>
                                            </select>
                                        </div>

                                        <div id="autre_motif_div-{{ $cust->id }}" class="form-group" style="display: none;">
                                            <input type="text" class="form-control" placeholder="Autre motif" name="motif_autre">
                                        </div>

                                        <div class="form-check my-3">
                                            <input type="checkbox" class="form-check-input confirm-checkbox"
                                                id="confirm-{{ $cust->id }}"
                                                onclick="terms_changed(this, 'submit-btn-{{ $cust->id }}')">
                                            <label class="form-check-label" for="confirm-{{ $cust->id }}">Confirmer la sélection</label>
                                        </div>

                                        <button type="submit" id="submit-btn-{{ $cust->id }}" class="btn btn-danger w-100" disabled>
                                            Valider
                                        </button>
                                    </form>
                                </div>
                                <div class="modal-footer  bg-lilght">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </tbody>
                @endforeach

                <script>
                    function showDiv(divId, element) {
                        const div = document.getElementById(divId);
                        div.style.display = (element.value === 'Autre') ? 'block' : 'none';
                    }

                    function terms_changed(checkbox, buttonId) {
                        document.getElementById(buttonId).disabled = !checkbox.checked;
                    }
                </script>

            </table>
        </div>
    </div>
    @else
    <div class="container mt-5">
        <div class="alert alert-info text-center" role="alert">
            <i class="ri-information-line"></i> Aucun client trouvé avec ce numéro de téléphone.
        </div>
    </div>
    @endif
</div>
@endsection