<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('acep-favicon.png') }}" type="image/x-icon">
    <!-- style CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Poppins font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- jQuery (toujours avant DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Optionnel Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    {{-- Analytics by chartjs --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<body style="font-family: 'Poppins', sans-serif;" class="body">
    {{-- navbar lists --}}
    <style>
        .body {
            background-color: #000;
            color: whitesmoke;
        }

        .nav-item .nav-link,
        .nav-item .nav-link.active,
        .nav-item .nav-link:focus {
            border-bottom: 2px solid #50c2bb;
            font-weight: 500;
        }

        i {
            color: #50c2bb;
        }

        h4 {
            color: #00564b;
            font-weight: bold;
        }

        .collapse {
            border-bottom: 2px solid transparent;
            border-bottom-right-radius: 5px;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .dropdown-item:hover {
            color: #50c2bb !important;
        }
    </style>
    @php
    $permission = "";
    foreach (session('selectedRoles') as $role) {
    if($role['name'] === 'CREATION PRET' || $role['name'] === 'CREATION CLIENT_2'){
    $permission = 'cc';
    break;
    }
    elseif ($role['name'] === 'APPROBATION 1 du PRET' || $role['name'] === 'APPROBATION 2 du PRET') {
    $permission = 'val';
    break;
    } elseif ($role['name'] === 'DIRECTEUR' || $role['name'] === 'INFORMATIQUE' || $role['name'] === 'CHEF DAGENCE' || $role['name'] === 'SUPER ADMIN') {
    $permission = 'admin';
    break;
    }
    }
    @endphp
    @if($permission === 'cc')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href=""><img src="{{ asset('/images/logos/acep-logo.png') }}" width="150" height="35" alt=""></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse pt-3 justify-content-lg-end" id="navbarSupportedContent">
                {{-- navbar lists --}}
                <ul class="navbar-nav bg-dark mb-2 mb-lg-0 text-uppercase custom-navbar">
                    <li class="nav-item d-flex align-items-center">
                        <i class="ri-home-line"></i>
                        <a class="nav-link active" aria-current="page" href="{{route('show.index')}}">Accueil</a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <i class="ri-shield-check-line"></i>
                        <a class="nav-link active" aria-current="page" href="{{route('sub.validation')}}">Validations</a>
                    </li>
                    <li class="nav-item dropdown d-flex align-items-center">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownServices"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-service-line me-2"></i> Services
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownServices">
                            <li><a class="dropdown-item" href="{{ route('show.subscribe') }}"><i class="ri-login-circle-line me-2"></i>Souscription</a></li>
                            <li><a class="dropdown-item" href="{{ route('show.unsubscribe.form') }}"><i class="ri-logout-circle-line me-2"></i></i>Résiliation</a></li>
                            <li><a class="dropdown-item" href="{{ route('show.contract')}}" ><i class="ri-file-add-line me-2"></i>Contrats</a></li>
                        </ul>
                    </li>
                </ul>
                {{-- users infos --}}
                <ul class="navbar-nav bg-dark mb-2 mb-lg-0 text-uppercase ms-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #4FC9C0;">
                            {{ session('username') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownUser">
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="ms-3">
                                    @csrf
                                    <button class="btn btn-outline-danger" type="submit">Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @elseif( $permission === 'val')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="">
                <img src="{{ asset('/images/logos/acep-logo.png') }}" width="150" height="35" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse pt-3 justify-content-lg-end" id="navbarSupportedContent">
                {{-- navbar lists --}}
                <ul class="navbar-nav bg-dark mb-2 mb-lg-0 text-uppercase custom-navbar">
                    <li class="nav-item">
                        <a class="nav-link active d-flex align-items-center gap-2" aria-current="page" href="{{route('show.index')}}">
                            <i class="ri-home-line"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active d-flex align-items-center gap-2" aria-current="page" href="{{ route('sub.validation') }}">
                            <i class="ri-shield-check-line"></i> Validations
                        </a>
                    </li>
                </ul>

                {{-- users infos --}}
                <ul class="navbar-nav bg-dark mb-2 mb-lg-0 text-uppercase ms-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: #4FC9C0;">
                            {{ session('username') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownUser">
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="ms-3">
                                    @csrf
                                    <button class="btn btn-outline-danger" type="submit">Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @elseif( $permission === 'admin')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="">
                <img src="{{ asset('/images/logos/acep-logo.png') }}" width="150" height="35" alt="">
            </a>

            <!-- Bouton responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu -->
            <div class="collapse navbar-collapse pt-3 justify-content-lg-end" id="navbarSupportedContent">

                {{-- Navbar links --}}
                <ul class="navbar-nav mb-2 mb-lg-0 text-uppercase custom-navbar">
                    <li class="nav-item">
                        <a class="nav-link active d-flex align-items-center" href="{{route('show.index')}}">
                            <i class="ri-home-line me-2"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active d-flex align-items-center" href="{{ route('sub.validation') }}">
                            <i class="ri-shield-check-line me-2"></i> Validations
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownServices"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-service-line me-2"></i> Services
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownServices">
                            <li><a class="dropdown-item" href="{{ route('show.subscribe') }}"><i class="ri-login-circle-line me-2"></i>Souscription</a></li>
                            <li><a class="dropdown-item" href="{{ route('show.unsubscribe.form') }}"><i class="ri-logout-circle-line me-2"></i></i>Résiliation</a></li>
                            <li><a class="dropdown-item" href="{{ route('show.contract')}}" ><i class="ri-file-add-line me-2"></i>Contrats</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="#">
                            <i class="ri-dashboard-2-line me-2"></i> Dashboard
                        </a>
                    </li>
                </ul>

                {{-- User menu --}}
                <ul class="navbar-nav mb-2 mb-lg-0 text-uppercase ms-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: #4FC9C0;">
                            {{ session('username') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownUser">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{route('show.settings')}}">
                                    <i class="ri-tools-fill me-2"></i> Paramètres
                                </a><hr class="dropdown-divider">
                            </li>
                            <li class="dropdown-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="btn btn-outline-danger w-100" type="submit">Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    @endif


    <main class="container-fluid mt-5 pt-4">
        {{-- main content --}}
        @yield('content')
    </main>
    <footer class="p-4 text-center text-sm text-gray-600">
        <p>&copy; 2025 DSI-ACEP / Bank To Wallet - Wallet To Bank <img src="{{asset('/images/logos/orange-logo.svg')}}"
                style="width: 125px;" height="50" alt="logo" class="pb-1"> </p>
    </footer>
</body>

</html>