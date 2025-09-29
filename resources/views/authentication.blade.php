<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('acep-favicon.png') }}" type="image/x-icon">

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Poppins font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body style="font-family: 'Poppins', sans-serif;">
    <section class="vh-100" style="background-color: #00574A; min-height: 100vh;">
        <div class="container h-100 d-flex justify-content-center align-items-center">
            <div class="row w-100 justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="card bg-dark text-white rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="card-body  p-md-5 mx-md-4">
                                    <div class="text-center">
                                        <img src="{{asset('/images/logos/acep-logo.png')}}"
                                            style="width: 185px;" alt="logo">
                                        <h4 class="mt-4 pb-5">La réussite à portée de main</h4>
                                    </div>

                                    <form method="POST" action="{{route('login')}}" class="text-uppercase ">
                                        @csrf
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="matricule">Matricule</label>
                                            <input type="text" id="matricule" name="matricule" class="form-control" placeholder="Mxxxx" required />
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="password">Mot de passe</label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe Musoni" required />
                                        </div>

                                        <div class="text-center pt-1 pb-1">
                                            <button class="btn btn-outline-success w-100 btn-block fa-lg gradient-custom-2 mb-3 text-uppercase" type="submit">Se connecter</button>
                                        </div>
                                    </form>
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    <div class="text-center">
                                        <div class="text-content"> <small>
                                                <p>&copy; 2025 DSI-ACEP / Bank To Wallet - Wallet To Bank </p>
                                            </small>
                                        </div>

                                        <div class="logo-orange">
                                            <img src="{{asset('/images/logos/logo-orange-money-2.svg')}}" style="width: 125px;" height="50" alt="logo" class="pb-1">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>