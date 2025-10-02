<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
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

    <style>
        /* Styles for dark mode, applied when the system prefers dark */
        .body {
            background-color: #f5f6fa;
            color: black;
        }

        /* Sidebar responsive */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100vh - 60px);
            width: 300px;
            background-color: #212529;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            transform: translateX(0);
        }

        /* Mobile : sidebar cachée par défaut */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Desktop : sidebar réduite quand collapsed */
        @media (min-width: 992px) {
            .sidebar.collapsed {
                width: 70px;
                overflow: visible; /* Permet d'afficher les flyouts en dehors */
            }

            /* Contraindre les éléments à la largeur réduite et centrer les icônes */
            .sidebar.collapsed .nav-link,
            .sidebar.collapsed .submenu-toggle {
                width: 70px;
                padding-left: 0;
                padding-right: 0;
                justify-content: center;
            }

            .sidebar.collapsed .nav-link i,
            .sidebar.collapsed .submenu-toggle i:first-child {
                margin-right: 0;
            }

            /* Cacher la flèche de submenu en mode collapsed pour éviter le décalage */
            .sidebar.collapsed .submenu-arrow {
                display: none !important;
            }

            /* Le fond actif/hover doit rester dans 70px */
            .sidebar.collapsed .nav-link,
            .sidebar.collapsed .submenu-toggle {
                box-sizing: border-box;
            }
        }

        .sidebar .nav-link {
            color: whitesmoke;
            padding: 15px 20px;
            border-bottom: 1px solid #343a40;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #50c2bb;
            color: #00574A;
        }

        .sidebar .nav-link i {
            color: #50c2bb;
            width: 20px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: white;
        }

        /* Styles pour les submenus */
        .nav-submenu {
            position: relative;
        }

        .submenu-toggle {
            color: whitesmoke !important;
            padding: 15px 20px;
            border-bottom: 1px solid #343a40;
            display: flex;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
        }

        .submenu-toggle:hover {
            background-color: #50c2bb;
            color: white !important;
        }

        .submenu-toggle i:first-child {
            color: #50c2bb;
            width: 20px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .submenu-toggle:hover i:first-child {
            color: white;
        }

        .submenu-arrow {
            margin-left: auto;
            transition: transform 0.3s;
        }

        .submenu-toggle.active .submenu-arrow {
            transform: rotate(180deg);
        }

        .submenu-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: #343a40;
        }

        .submenu-content.show {
            max-height: 300px;
        }

        .submenu-link {
            color: whitesmoke;
            padding: 10px 20px 10px 50px;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-bottom: 1px solid #495057;
        }

        .submenu-link:hover {
            background-color: #50c2bb;
            color: white;
        }

        .submenu-link i {
            color: #50c2bb;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .submenu-link:hover i {
            color: white;
        }

        /* Flyout des submenus quand sidebar est collapsed (desktop) */
        @media (min-width: 992px) {
            .sidebar.collapsed .submenu-content {
                position: absolute;
                left: 70px; /* largeur sidebar réduite */
                top: 0; /* ajusté en JS sur clic */
                width: 230px;
                max-height: none;
                display: none;
                border-left: 1px solid #343a40;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 1002;
            }

            .sidebar.collapsed .submenu-content.show {
                display: block;
            }
        }

        /* Contenu principal responsive */
        .main-content {
            margin-top: 60px;
            padding: 20px;
            transition: all 0.3s;

        }

        /* Desktop */
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
                padding-left: 0;
            }


            .main-content.expanded {
                margin-left: 70px;
            }
        }

        /* Mobile et tablette */
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: -65px;
                padding: 15px;
            }
        }

        .navbar-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            background-color: #212529;
            height: 60px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #50c2bb;
            font-size: 20px;
        }

        .sidebar .nav-text {
            transition: opacity 0.3s;
            white-space: nowrap;
        }

        /* Masquer le texte sur desktop collapsed */
        @media (min-width: 992px) {
            .sidebar.collapsed .nav-text {
                opacity: 0;
                display: none;
            }
        }

        i {
            color: #50c2bb;
        }

        h4 {
            color: #00564b;
            font-weight: bold;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .dropdown-item:hover {
            color: #50c2bb !important;
        }

        /* Overlay pour mobile */
        .sidebar-overlay {
            position: fixed;
            top: 60px;
            left: 0;
            width: 100%;
            height: calc(100vh - 60px);
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Footer responsive */
        footer {
            margin-left: 0;
            transition: margin-left 0.3s;
        }

        @media (min-width: 992px) {
            footer {
                margin-left: 250px;
            }

            footer.expanded {
                margin-left: 70px;
            }
        }

        /* Responsive du navbar brand */
        @media (max-width: 575.98px) {
            .navbar-brand img {
                width: 100px !important;
                height: 25px !important;
            }
        }

        /* Responsive du dropdown utilisateur */
        @media (max-width: 575.98px) {
            .navbar .dropdown .nav-link span {
                display: none;
            }
        }
    </style>
</head>

<body style="font-family: 'Poppins', sans-serif;" class="body">
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
    } elseif ( $role['name'] === 'Super user' || $role['name'] === 'DIRECTEUR' || $role['name'] === 'INFORMATIQUE' || $role['name'] === 'CHEF DAGENCE' || $role['name'] === 'SUPER ADMIN' || $role['name'] === 'Approbation Retraits (C.O)') {
    $permission = 'admin';
    break;
    }
    }
    @endphp

    <!-- Overlay pour mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Navbar supérieur pour les informations utilisateur -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-top text-uppercase fw-bold">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="ri-menu-line"></i>
                </button>
                <a class="navbar-brand ps-3 " href="">
                    <img src="{{ asset('/images/logos/acep-logo.png') }}" width="150" height="35" alt="" class="img-fluid">
                </a>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-uppercase d-flex align-items-center" href="#" id="navbarDropdownUser" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" style="color: #4FC9C0;">
                        <i class="ri-user-2-line me-2"></i>
                        <span>{{ session('username') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdownUser">
                        @if($permission === 'admin')
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{route('show.settings')}}">
                                <i class="ri-tools-fill me-2"></i> Paramètres
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @endif
                        <li class="dropdown-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-danger w-100" type="submit">Déconnexion</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Responsive -->
    <div class="sidebar" id="sidebar">
        @if($permission === 'cc')
        <nav class="nav flex-column text-uppercase fw-bold">
            <a class="nav-link active" href="{{route('show.index')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{route('sub.validation')}}">
                <i class="ri-shield-check-line"></i>
                <span class="nav-text">Validations</span>
            </a>
            <div class="nav-submenu">
                <a class="submenu-toggle" href="#" onclick="toggleSubmenu(this)">
                    <i class="ri-service-line"></i>
                    <span class="nav-text">Services</span>
                    <i class="ri-arrow-down-s-line submenu-arrow"></i>
                </a>
                <div class="submenu-content">
                    <a class="submenu-link" href="{{ route('show.subscribe') }}">
                        <i class="ri-login-circle-line"></i>
                        <span>Souscription</span>
                    </a>
                    <a class="submenu-link" href="{{ route('show.unsubscribe.form') }}">
                        <i class="ri-logout-circle-line"></i>
                        <span>Résiliation</span>
                    </a>
                    <a class="submenu-link" href="{{ route('show.contract')}}">
                        <i class="ri-file-add-line"></i>
                        <span>Contrats</span>
                    </a>
                </div>
            </div>
            <a class="nav-link" href="{{ route('documentation.user.guide') }}">
                <i class="ri-book-open-line"></i>
                <span class="nav-text">Documentation</span>
            </a>
        </nav>

        @elseif($permission === 'val')
        <nav class="nav flex-column text-uppercase fw-bold">
            <a class="nav-link active" href="{{route('show.index')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{ route('sub.validation') }}">
                <i class="ri-shield-check-line"></i>
                <span class="nav-text">Validations</span>
            </a>
            <a class="nav-link" href="{{ route('documentation.user.guide') }}">
                <i class="ri-book-open-line"></i>
                <span class="nav-text">Documentation</span>
            </a>
        </nav>

        @elseif($permission === 'admin')
        <nav class="nav flex-column text-uppercase fw-bold">
            <a class="nav-link active" href="{{route('show.index')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{ route('sub.validation') }}">
                <i class="ri-shield-check-line"></i>
                <span class="nav-text">Validations</span>
            </a>
            <div class="nav-submenu">
                <a class="submenu-toggle" href="#" onclick="toggleSubmenu(this)">
                    <i class="ri-service-line"></i>
                    <span class="nav-text">Services</span>
                    <i class="ri-arrow-down-s-line submenu-arrow"></i>
                </a>
                <div class="submenu-content">
                    <a class="submenu-link" href="{{ route('show.subscribe') }}">
                        <i class="ri-login-circle-line"></i>
                        <span>Souscription</span>
                    </a>
                    <a class="submenu-link" href="{{ route('show.unsubscribe.form') }}">
                        <i class="ri-logout-circle-line"></i>
                        <span>Résiliation</span>
                    </a>
                    <a class="submenu-link" href="{{ route('show.contract')}}">
                        <i class="ri-file-add-line"></i>
                        <span>Contrats</span>
                    </a>
                </div>
            </div>
            <a class="nav-link" href="{{ route('analytics.index') }}">
                <i class="ri-dashboard-2-line"></i>
                <span class="nav-text">Analytics</span>
            </a>
            <div class="nav-submenu">
                <a class="submenu-toggle" href="#" onclick="toggleSubmenu(this)">
                    <i class="ri-book-open-line"></i>
                    <span class="nav-text">Documentation</span>
                    <i class="ri-arrow-down-s-line submenu-arrow"></i>
                </a>
                <div class="submenu-content">
                    <a class="submenu-link" href="{{ route('documentation.user.guide') }}">
                        <i class="ri-user-book-line"></i>
                        <span>Guide utilisateur</span>
                    </a>
                    @php
                        $hasInformatiquePermission = false;
                        foreach (session('selectedRoles') as $role) {
                            if ($role['name'] === 'INFORMATIQUE' || $role['name'] === 'SUPER ADMIN') {
                                $hasInformatiquePermission = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasInformatiquePermission)
                    <a class="submenu-link" href="{{ route('documentation.technical.guide') }}">
                        <i class="ri-code-s-slash-line"></i>
                        <span>Documentation technique</span>
                    </a>
                    @endif
                </div>
            </div>
        </nav>
        @endif

        <div class="fixed-bottom">
            <hr class="dropdown-divider text-white">
            <p class="text-xs text-end pe-2 text-white py-0" style="font-size: 8px;"><small>Dev by Joachim.</small></p>
        </div>
    </div>

    <!-- Contenu principal -->
    <main class="main-content" id="mainContent" style="padding-left : 75px;">
        @yield('content')
    </main>

    <footer class="p-4 text-center text-sm text-gray-600" id="footer">
        <p class="fs-6">&copy; 2025 DSI-ACEP / Bank To Wallet - Wallet To Bank <img src="{{asset('/images/logos/orange-logo.svg')}}"
                style="width: 125px;" height="50" alt="logo" class="pb-1"> All Rights Reserved </p>
    </footer>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const footer = document.getElementById('footer');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        function isMobile() {
            return window.innerWidth < 992;
        }

        sidebarToggle.addEventListener('click', function() {
            if (isMobile()) {
                // Mode mobile/tablette
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // Mode desktop
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                footer.classList.toggle('expanded');
            }
        });

        // Fermer la sidebar en cliquant sur l'overlay (mobile)
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Gérer le redimensionnement de la fenêtre
        window.addEventListener('resize', function() {
            if (!isMobile()) {
                // En mode desktop, enlever les classes mobile
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                // En mode mobile, enlever les classes desktop
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                footer.classList.remove('expanded');
            }
        });

        function toggleSubmenu(element) {
            const submenuContent = element.nextElementSibling;
            const isActive = element.classList.contains('active');

            // Fermer tous les autres submenus
            document.querySelectorAll('.submenu-toggle').forEach(toggle => {
                if (toggle !== element) {
                    toggle.classList.remove('active');
                    toggle.nextElementSibling.classList.remove('show');
                }
            });

            // Basculer le submenu courant
            if (isActive) {
                element.classList.remove('active');
                submenuContent.classList.remove('show');
            } else {
                element.classList.add('active');
                submenuContent.classList.add('show');

                // Si la sidebar est en mode collapsed (desktop), positionner le flyout à la hauteur du toggle
                if (!isMobile() && sidebar.classList.contains('collapsed')) {
                    const toggleRect = element.getBoundingClientRect();
                    const sidebarRect = sidebar.getBoundingClientRect();
                    const topOffset = toggleRect.top - sidebarRect.top;
                    submenuContent.style.top = `${topOffset}px`;
                } else {
                    submenuContent.style.top = '';
                }
            }
        }

        // Fermer les dropdowns en cliquant en dehors
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>

</body>

</html>