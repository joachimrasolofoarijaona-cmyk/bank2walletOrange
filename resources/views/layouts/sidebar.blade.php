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
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
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
    
    <!-- Styles personnalisés -->
    @stack('styles')

    <style>
        /* Modern Design Variables */
        :root {
            --primary-color: #02564A;
            --accent-color: #4FC9C0;
            --bg-light: #F8F9FA;
            --text-primary: #212529;
            --text-secondary: #6C757D;
            --border-color: #E9ECEF;
            --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            --sidebar-bg: #02564A;
            --sidebar-hover: #033d35;
            --navbar-bg: #02564A;
        }

        /* Body Styles */
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
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #033d35 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            transform: translateX(0);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1);
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
            color: rgba(255, 255, 255, 0.9);
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 28px;
        }

        .sidebar .nav-link.active {
            background: var(--accent-color);
            color: var(--primary-color);
            border-left: 4px solid white;
        }

        .sidebar .nav-link i {
            color: var(--accent-color);
            width: 22px;
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover i {
            color: white;
            transform: scale(1.1);
        }

        .sidebar .nav-link.active i {
            color: var(--primary-color);
        }

        /* Styles pour les submenus */
        .nav-submenu {
            position: relative;
        }

        .submenu-toggle {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .submenu-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
            padding-left: 28px;
        }

        .submenu-toggle.active {
            background: rgba(255, 255, 255, 0.15);
        }

        .submenu-toggle i:first-child {
            color: var(--accent-color);
            width: 22px;
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .submenu-toggle:hover i:first-child {
            color: white;
            transform: scale(1.1);
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
            background: rgba(0, 0, 0, 0.2);
        }

        .submenu-content.show {
            max-height: 300px;
        }

        .submenu-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 24px 12px 56px;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            font-size: 13px;
            font-weight: 400;
        }

        .submenu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 60px;
        }

        .submenu-link.active {
            background: var(--accent-color);
            color: var(--primary-color);
            border-left: 3px solid white;
        }

        .submenu-link i {
            color: var(--accent-color);
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .submenu-link:hover i {
            color: white;
            transform: scale(1.1);
        }

        .submenu-link.active i {
            color: var(--primary-color);
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
                border-left: 3px solid var(--accent-color);
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                z-index: 1002;
                background: linear-gradient(180deg, var(--sidebar-bg) 0%, #033d35 100%);
                border-radius: 0 8px 8px 0;
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
            background: linear-gradient(135deg, var(--navbar-bg) 0%, #033d35 100%);
            height: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--accent-color);
            font-size: 22px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: scale(1.1);
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

        /* Navbar Brand */
        .navbar-brand {
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        /* Dropdown User */
        .navbar .dropdown .nav-link {
            color: var(--accent-color) !important;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .navbar .dropdown .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .dropdown-menu-dark {
            background: var(--sidebar-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 8px;
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.9);
            padding: 10px 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .dropdown-item i {
            color: var(--accent-color);
            font-size: 18px;
        }

        .dropdown-item:hover i {
            color: white;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .dropdown-item:hover {
            color: var(--accent-color) !important;
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

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(79, 201, 192, 0.8);
        }
    </style>
</head>

<body style="font-family: 'Poppins', sans-serif;" class="body">
    @php
    $permission = "";
    foreach (session('selectedRoles') as $role) {
    if($role['name'] === 'CREATION PRET' || $role['name'] === 'CREATION CLIENT_2' || $role['name'] === 'GESTION TENUE DE CAISSE NORMALE' ){
    $permission = 'cc';
    break;
    }
    elseif ($role['name'] === 'APPROBATION 1 du PRET' || $role['name'] === 'APPROBATION 2 du PRET' || $role['name'] === 'CHEF DAGENCE' || $role['name'] === 'DIRECTEUR DE RESEAU DAGENCES' || $role['name'] === 'APPROBATION 1 DU PRET_2') {
    $permission = 'val';
    break;
    } elseif ( $role['name'] === 'Super user' || $role['name'] === 'INFORMATIQUE' || $role['name'] === 'SUPER ADMIN' || $role['name'] === 'DIRECTEUR') {
    $permission = 'admin';
    break;
    }
    elseif ($role['name'] === 'Approbation Retraits (C.O)' || $role['name'] === 'CONTROLEUR OPERATIONNEL') {
        $permission = 'control';
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
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownUser" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--accent-color); font-weight: 500;">
                        <i class="ri-user-2-line me-2" style="font-size: 20px;"></i>
                        <span>{{ session('username') }}</span>
                        <i class="ri-arrow-down-s-line ms-2" style="font-size: 16px;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdownUser">
                        @if($permission === 'admin')
                        <li>
                            <a class="dropdown-item" href="{{route('show.settings')}}">
                                <i class="ri-settings-3-line"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" style="border-color: rgba(255, 255, 255, 0.1);">
                        </li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit" style="border: none; background: none; width: 100%; text-align: left; padding: 10px 16px;">
                                    <i class="ri-logout-box-line"></i>
                                    <span>Déconnexion</span>
                                </button>
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
            <a class="nav-link" href="{{route('client.search')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{route('show.index')}}">
                <i class="ri-dashboard-line"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link" href="{{route('validations.cc')}}">
                <i class="ri-shield-check-line"></i>
                <span class="nav-text">Mes Demandes</span>
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

        @elseif($permission === 'val' || $permission === 'control')
        <nav class="nav flex-column text-uppercase fw-bold">
            <a class="nav-link" href="{{route('client.search')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{route('show.index')}}">
                <i class="ri-dashboard-line"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link" href="{{ route('validations.validator') }}">
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
            <a class="nav-link" href="{{route('client.search')}}">
                <i class="ri-home-line"></i>
                <span class="nav-text">Accueil</span>
            </a>
            <a class="nav-link" href="{{route('show.index')}}">
                <i class="ri-dashboard-line"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link" href="{{ route('validations.admin') }}">
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

        <div class="fixed-bottom" style="background: rgba(0, 0, 0, 0.2); padding: 12px 0;">
            <hr class="dropdown-divider" style="margin: 0; border-color: rgba(255, 255, 255, 0.1);">
            <p class="text-xs text-end pe-3 text-white py-2 mb-0" style="font-size: 10px; opacity: 0.7;">
                <small>Dev by Joachim.</small>
            </p>
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

        // Gérer l'état actif des liens de navigation
        function setActiveNavLink() {
            const currentPath = window.location.pathname;
            const currentUrl = window.location.href;
            
            // Retirer la classe active de tous les liens
            document.querySelectorAll('.nav-link, .submenu-link').forEach(link => {
                link.classList.remove('active');
            });

            // Trouver le lien correspondant à l'URL actuelle
            document.querySelectorAll('.nav-link, .submenu-link').forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref && linkHref !== '#') {
                    try {
                        // Construire le chemin complet si c'est un chemin relatif
                        const linkUrl = new URL(linkHref, window.location.origin);
                        const linkPath = linkUrl.pathname;
                        
                        // Comparer les chemins exacts
                        if (currentPath === linkPath) {
                            link.classList.add('active');
                            
                            // Si c'est un lien de submenu, ouvrir le submenu parent
                            const submenuContent = link.closest('.submenu-content');
                            if (submenuContent) {
                                const submenuToggle = submenuContent.previousElementSibling;
                                if (submenuToggle && submenuToggle.classList.contains('submenu-toggle')) {
                                    submenuToggle.classList.add('active');
                                    submenuContent.classList.add('show');
                                }
                            }
                        }
                    } catch (e) {
                        // Si l'URL est invalide, ignorer
                        console.warn('Invalid URL:', linkHref);
                    }
                }
            });
        }

        // Appeler la fonction au chargement de la page
        setActiveNavLink();
    </script>


</body>

</html>