@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $products = $banners[0]->products??'';
    ?>
    <style>


        .content {
            padding: 20px;
            flex-grow: 1;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .section-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .coupon-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .coupon-header {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px dashed #e0e0e0;
        }

        .coupon-header .logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
        }

        .coupon-header .logo img {
            width: 40px;
            height: 40px;
        }

        .coupon-header .details h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .coupon-header .details p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .coupon-body {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .coupon-body::before, .coupon-body::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #f0f2f5;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .coupon-body::before {
            left: -10px;
        }

        .coupon-body::after {
            right: -10px;
        }

        .coupon-body .code {
            font-size: 14px;
            font-weight: bold;
            color: #1a73e8;
            background-color: #e6f0ff;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .coupon-body .collect-button {
            background-color: #1a73e8;
            color: #ffffff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .coupon-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #f9f9f9;
            border-top: 1px solid #e0e0e0;
        }

        .coupon-footer a {
            font-size: 14px;
            color: #1a73e8;
            text-decoration: none;
            font-weight: bold;
        }

        .coupon-footer span {
            font-size: 14px;
            color: #1a73e8;
        }

        .bottom-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            border-top: 1px solid #e0e0e0;
        }

        .coupon-savings {
            font-size: 16px;
            color: #666;
        }

        .coupon-savings span {
            font-weight: bold;
            color: #333;
        }

        .view-bag-button {
            background-color: #1c1c1c;
            color: #ffffff;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <style>
        .single-hero-slider {
            width: 100%;
            height: 500px;
            background-size: 100% 100% !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
        }

        .coupon-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px; /* spacing between cards */
        }

        .coupon-card {
            flex: 0 0 33.33%;
            max-width: 33.33%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }
        @media (max-width: 992px) {
            .coupon-card {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .coupon-card {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

    </style>
    <style>
        .custom-col {
            flex: 0 0 20%;
            max-width: 20%;
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .single-hero-slider {
            width: 100%;
            height: 400px;  /* adjust */
            background-size: cover !important;
            background-position: center !important;
            border-radius: 10px;
        }
        .home-banner {
            width: 100%;
            height: 400px;
            border-radius: 20px;
            object-fit: fill; /* better than fill */
        }

        /* Tablet */
        @media (max-width: 768px) {
            .home-banner {
                height: 200px;
            }
        }

        /* Mobile */
        @media (max-width: 480px) {
            .home-banner {
                height: 200px;
            }
        }

    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> PromoCodes
                </div>
            </div>
        </div>
        <div class="page-content">
                <div class="container">
                    <img src="{{ $banners[0]->banner_img ?? '' }}"
                         class="img-fluid home-banner"
                         alt="Banner">

                    {{--   <section class="home-slider position-relative mb-30">
                           <div class="container">
                               <div class="home-slide-cover mt-30">
                                   @if(isset($banners[0]))
                                       <div class="single-hero-slider single-animation-wrap"
                                            style="background-image: url('{{ $banners[0]->banner_img }}');
                                   background-size: cover;
                                   background-position: center;
                                   height: 400px; /* adjust based on your design */
                            ">
                                       </div>
                                   @endif
                               </div>
                           </div>
                       </section>--}}

                <div class="row product-grid mt-30">
                    @foreach ($products as $product)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 custom-col">
                            @include('home.single_product', ['product' => $product])
                        </div>
                    @endforeach

                </div>



                <div class="row">


                    <div class="container">
                        <div class="content">
                            <h2 class="section-title">Collect multiple of these</h2>
                            <p class="section-subtitle">To get max discounts or offers on your bag</p>

                            @foreach($offers as $offer)
                                <div class="coupon-card">
                                    <div class="coupon-header">
                                        <div class="details">
                                            <h3>{{$offer->description??''}}</h3>
                                        </div>
                                    </div>
                                    <div class="coupon-body">
                                        <div class="code">{{$offer->offer_code??''}}</div>
                                        <button class="collect-button"
                                                onclick="copyToClipboard('{{$offer->offer_code??''}}')">Copy
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                        </div>


                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        function copyToClipboard(textToCopy) {
            navigator.clipboard.writeText(textToCopy).then(function () {
                alert("Copied to clipboard!");
            }, function (err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

@endsection
