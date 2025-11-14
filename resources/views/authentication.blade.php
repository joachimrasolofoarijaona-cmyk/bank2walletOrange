<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Bank To Wallet</title>
    <link rel="icon" href="{{ asset('acep-favicon.png') }}" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Poppins font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <style>
        /* Modern Design Variables */
        :root {
            --primary-color: #02564A;
            --accent-color: #4FC9C0;
            --bg-light: #F8F9FA;
            --text-primary: #212529;
            --text-secondary: #6C757D;
            --border-color: #E9ECEF;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header img {
            width: 180px;
            height: auto;
            margin-bottom: 20px;
        }

        .login-header h4 {
            font-size: 18px;
            font-weight: 500;
            margin: 0;
            opacity: 0.95;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 18px;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(2, 86, 74, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.6;
        }

        .btn-login {
            width: 100%;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            font-weight: 600;
            padding: 14px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 86, 74, 0.3);
        }

        .alert-modern {
            border-radius: 8px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-modern.alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-modern ul {
            margin: 0;
            padding-left: 20px;
        }

        .login-footer {
            padding: 30px;
            text-align: center;
            background: var(--bg-light);
            border-top: 1px solid var(--border-color);
        }

        .login-footer p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 15px;
        }

        .login-footer img {
            width: 120px;
            height: auto;
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-header img {
                width: 150px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <img src="{{asset('/images/logos/acep-logo.png')}}" alt="ACEP Logo">
                <h4>La réussite à portée de main</h4>
            </div>

            <!-- Body -->
            <div class="login-body">
                @if ($errors->any())
                <div class="alert-modern alert-danger">
                    <i class="ri-error-warning-line" style="font-size: 20px;"></i>
                    <div style="flex: 1;">
                        <strong>Erreur de connexion</strong>
                        <ul class="mt-2">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{route('login')}}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label" for="matricule">
                            <i class="ri-user-line"></i>
                            Matricule
                        </label>
                        <input type="text" 
                               id="matricule" 
                               name="matricule" 
                               class="form-control" 
                               placeholder="Mxxxx" 
                               required 
                               autocomplete="username" />
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password">
                            <i class="ri-lock-line"></i>
                            Mot de passe
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Mot de passe Musoni" 
                               required 
                               autocomplete="current-password" />
                    </div>

                    <div class="mt-4">
                        <button class="btn-login" type="submit">
                            <i class="ri-login-box-line"></i>
                            Se connecter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; 2025 DSI-ACEP / Bank To Wallet - Wallet To Bank</p>
                <img src="{{asset('/images/logos/orange-logo.png')}}" alt="Orange Money Logo">
            </div>
        </div>
    </div>
</body>

</html>