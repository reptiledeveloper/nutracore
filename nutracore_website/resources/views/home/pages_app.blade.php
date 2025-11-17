@extends('home.layout')
@section('content')
    <style>
        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #9ee8f6, #5bd1e0);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-box svg {
            width: 22px;
            height: 22px;
            color: #fff;
        }


    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Get The NutraCore App
                </div>
            </div>
        </div>
        <div class="container">
            <div class="page-content pt-50">
                <!-- Hero Section -->
                <div class="center">
                    <img src="{{ url('public/assets/Mobile_App_1200_x_500_px.webp') }}"
                         style="width:60%; border-radius:30px;">

                    <h3>Download The NutraCore App Today</h3>
                    <span>Shop authentic supplements with confidence, track your orders, unlock rewards, and get expert<br>
                    recommendations — all in one place.<br>

                Scan a QR with your phone or use the buttons below.</span>
                </div>

                <div class="center">
                    <div class="row">
                        <div class="col-md-3 p-2">
                            <div class="feature-card p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box me-2">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6l7-3z"
                                                  stroke="currentColor" stroke-width="1.8"></path>
                                            <path d="M9 12l2 2 4-4"
                                                  stroke="#ffffff" stroke-width="2.2"
                                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>

                                    <h6 class="fw-bold mb-0">Authenticity Guaranteed</h6>
                                </div>

                                <p class="mb-0 text-muted" style="font-size: 14px;text-align: left">
                                    100% verified products from trusted brands. Trackable supply, tamper-proof
                                    packaging, and easy returns for peace of mind.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3  p-2">
                            <div class="feature-card p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box me-2">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" fill="currentColor"></path>
                                        </svg>
                                    </div>

                                    <h6 class="fw-bold mb-0">2-Hour Delivery
                                    </h6>
                                </div>

                                <p class="mb-0 text-muted" style="font-size: 14px;text-align: left">
                                    Same-day speed in select areas. Live tracking and proactive updates so you know
                                    exactly when to expect your package.


                                </p>
                            </div>
                        </div>
                        <div class="col-md-3  p-2">
                            <div class="feature-card p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box me-2">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8h16z"
                                                  stroke="currentColor" stroke-width="1.8"></path>
                                            <path d="M2 9h20v3H2z" fill="currentColor"></path>
                                            <path d="M12 4c-2 0-3 .8-3 2s1 2 3 2 3-.8 3-2-1-2-3-2z" stroke="#fff"
                                                  stroke-width="1.4"></path>
                                        </svg>
                                    </div>

                                    <h6 class="fw-bold mb-0">NutraPass Rewards
                                    </h6>
                                </div>

                                <p class="mb-0 text-muted" style="font-size: 14px;text-align: left">
                                    Earn points on every purchase, unlock exclusive deals, and redeem perks curated for
                                    your fitness goals.


                                </p>
                            </div>
                        </div>
                        <div class="col-md-3  p-2">
                            <div class="feature-card p-3 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box me-2">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M21 12a7 7 0 0 1-7 7H8l-5 3 1.5-4.5A7 7 0 0 1 3 12a7 7 0 0 1 7-7h4a7 7 0 0 1 7 7z"
                                                stroke="currentColor" stroke-width="1.8"></path>
                                            <path d="M8.5 12h7M8.5 9.5h4.5" stroke="#ffffff" stroke-width="2"
                                                  stroke-linecap="round"></path>
                                        </svg>
                                    </div>

                                    <h6 class="fw-bold mb-0">Expert Guidance
                                    </h6>
                                </div>

                                <p class="mb-0 text-muted" style="font-size: 14px;text-align: left">
                                    Personalized supplement recommendations and easy access to NC Consult so you always
                                    pick what’s right for you.


                                </p>
                            </div>
                        </div>

                    </div>


                    <div class="row g-2">
                        <div class="col-md-6" style="margin-right: -10px;">

                            <div class="container d-flex justify-content-center align-items-center"
                                 style="min-height: 100vh;">
                                <div class="card p-4 shadow-lg"
                                     style="width: 100%; max-width: 400px; border-radius: 15px; border: 1px solid #e0e0e0;">

                                    <div class="position-absolute" style="top: -15px; left: -15px;">
                                        <div class="bg-info text-white rounded p-1 shadow-sm"
                                             style="font-size: 1.25rem;background-color: white !important; font-weight: bold; width: 40px; height: 40px; display: flex; justify-content: center; align-items: center; border-radius: 10px;">
                                            <img src="{{url('public/assets/App_Icon_Final.webp')}}">
                                        </div>
                                    </div>

                                    <div class="card-body text-center d-flex flex-column align-items-center">

                                        <img
                                            src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                                            alt="Get it on Google Play" class="img-fluid mb-4"
                                            style="max-height: 40px;">

                                        <img
                                            src="https://quickchart.io/qr?text=https%3A%2F%2Fplay.google.com%2Fstore%2Fapps%2Fdetails%3Fid%3Dcom.nutracore%26hl%3Den&size=230&margin=2"
                                            alt="QR Code to download app"
                                            class="img-fluid mb-4 border border-1 p-2" style="max-width: 250px;">

                                        <a href="#" class="btn btn-lg w-100 text-white"
                                           style="background: linear-gradient(to right, #00c6ff, #0072ff); border-radius: 30px; font-weight: bold;width: 65% !important;">
                                            Open Google Play
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="container d-flex justify-content-center align-items-center"
                                 style="min-height: 100vh;">
                                <div class="card p-4 shadow-lg"
                                     style="width: 100%; max-width: 400px; border-radius: 15px; border: 1px solid #e0e0e0;">

                                    <div class="position-absolute" style="top: -15px; left: -15px;">
                                        <div class="bg-info text-white rounded p-1 shadow-sm"
                                             style="font-size: 1.25rem;background-color: white !important; font-weight: bold; width: 40px; height: 40px; display: flex; justify-content: center; align-items: center; border-radius: 10px;">
                                            <img src="{{url('public/assets/App_Icon_Final.webp')}}">
                                        </div>
                                    </div>

                                    <div class="card-body text-center d-flex flex-column align-items-center">

                                        <img
                                            src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
                                            alt="Get it on Google Play"
                                            class="img-fluid mb-4 d-block mx-auto"
                                            style="max-height: 40px;">

                                        <img
                                            src="https://quickchart.io/qr?text=https%3A%2F%2Fapps.apple.com%2Fin%2Fapp%2Fnutracore%2Fid6749866050&size=230&margin=2"
                                            alt="QR Code to download app"
                                            class="img-fluid mb-4 border border-1 p-2 d-block mx-auto"
                                            style="max-width: 250px;">

                                        <a href="#" class="btn btn-lg w-100 text-white"
                                           style="background: linear-gradient(to right, #00c6ff, #0072ff); border-radius: 30px; font-weight: bold; width: 65% !important;">
                                            Open App Store
                                        </a>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <span>Tip: Open your phone camera and scan the QR to jump to the correct store.
                        </span>
                    </div>
                </div>


            </div>
        </div>
    </main>

@endsection
