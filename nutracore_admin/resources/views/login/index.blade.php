<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Nutracore Admin</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{favicon()}}"/>

    <!-- Themify icons -->
    <link rel="stylesheet" href="{{url('public/assets/dist/icons/themify-icons/themify-icons.css')}}" type="text/css">

    <!-- Main style file -->
    <link rel="stylesheet" href="{{url('public/assets/dist/css/app.min.css')}}" type="text/css">

    <link href="https://cdn.jsdelivr.net/npm/ti-icons@0.1.2/css/themify-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{url('public/assets/css/custom.css')}}" type="text/css">

</head>
<body class="auth">

<!-- begin::preloader-->
<div class="preloader">
    <div class="preloader-icon"></div>
</div>
<!-- end::preloader -->
<style>
    .auth {
    background-color: #11AEAE;
}
</style>

<div class="form-wrapper">
    <div class="container">
        <div class="card">
            <div class="row g-0">
                <div
                    class="col d-none d-lg-flex border-start align-items-center justify-content-between flex-column text-center">
                    <div class="logo">
                        <img width="200" src="{{logo()}}" alt="logo">
                    </div>
                    <div>
                        <h3 class="fw-bold">Welcome to Nutracore!</h3>
                        <p class="lead my-5">If you don't have an account, would you like to register right now?</p>

                    </div>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="#">Privacy Policy</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#">Terms & Conditions</a>
                        </li>
                    </ul>
                </div>

                <div class="col">
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                            <div class="d-block d-lg-none text-center text-lg-start">
                                <img width="200" src="{{logo()}}" alt="logo">
                            </div>
                            <div class="my-5 text-center text-lg-start">
                                <h1 class="display-8">Sign In</h1>
                                <p class="text-muted">Sign in to Nutracore to continue</p>
                            </div>
                            @include('snippets.errors')
                            @include('snippets.flash')
                            <form action="{{ url('/login_submit') }}" method="post" class="mb-5">
                                @csrf
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Enter email" autofocus
                                           name="email">
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control" placeholder="Enter password"
                                           name="password">
                                </div>
                                <div class="text-center text-lg-start">
{{--                                    <p class="small">Can't access your account? <a href="#">Reset your password now</a>.--}}
{{--                                    </p>--}}
                                    <button class="btn btn-primary">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- Bundle scripts -->
<script src="{{url('public/assets/libs/bundle.js')}}"></script>

<!-- Main Javascript file -->
<script src="{{url('public/assets/dist/js/app.min.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
