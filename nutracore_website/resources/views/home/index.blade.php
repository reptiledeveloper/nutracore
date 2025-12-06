@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $fixed_banner_1 = [];
    $fixed_banner_2 = [];
    $fixed_banner_3 = [];
    $download_banner = [];
    $website_main_banner = [];
    $instant_expert = [];
    $small_banners = [];
    if (!empty($banners)) {
        foreach ($banners as $banner) {
            if ($banner->type == 'Fixed_banner1') {
                $fixed_banner_1 = $banner;
            }
            if ($banner->type == 'Fixed_banner2') {
                $fixed_banner_2 = $banner;
            }
            if ($banner->type == 'Fixed_banner3') {
                $fixed_banner_3 = $banner;
            }
            if ($banner->type == 'download_banner') {
                $download_banner = $banner;
            }
            if ($banner->type == 'small_banner') {
                $small_banners[] = $banner;
            }
            if ($banner->type == 'website_main_banner') {
                $website_main_banner[] = $banner;
            }
            if ($banner->type == 'instant_expert') {
                $instant_expert = $banner;
            }
        }
    }
//    echo "<pre>";
//    print_r($small_banners);die;
    $user = Auth::user();
    $subscription = CustomHelper::subscriptionsData($user);

    $banner_types = ['', 'product', 'brand', 'category', 'link'];

    ?>
    <style>
        .single-hero-slider {
            width: 100%;
            height: 600px !important;
            background-size: 100% 100% !important;
            /* force stretch width & height */
            background-position: center center !important;
            background-repeat: no-repeat !important;
        }

        @media only screen and (max-width: 1024px) {
            .single-hero-slider {
                height: 230px !important;
            }
        }

        @media only screen and (max-width: 768px) {
            .hero-slider-1 {
                height: 200px !important;
            }
        }

        .video-slide {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background: #000;
        }

        iframe,
        video {
            border: none;
            object-fit: cover;
            height: 500px;
            /* Reel size */
        }

        .start-30 {
            left: 35% !important;
        }

    </style>
    <style>
        .banner-slide {
            width: 100%;
            height: 400px;
            border-radius: 20px;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }

        /* Tablet */
        @media (max-width: 768px) {
            .banner-slide {
                height: 200px;
                border-radius: 16px;
            }
        }

        /* Mobile */
        @media (max-width: 480px) {
            .banner-slide {
                height: 200px;
                border-radius: 12px;
            }
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
                height: 160px;
            }
        }
        .padding_set{
            padding-top: 458px;
        }

        @media (max-width: 768px) {
            .padding_set{
                padding-top: 320px;
            }
        }
        @media (max-width: 480px) {
            .padding_set{
                padding-top: 320px;
            }
        }


    </style>
    <style>
        /* General */
        * {
            font-family: "Poppins", sans-serif;
            box-sizing: border-box;
        }

        /* Main Container */
        .nutrapass-container {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            background: linear-gradient(90deg, #f8d57a, #f6c34a);
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 850px;
            margin: 50px auto;
            color: #000;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* Left Section */
        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
        }

        .left-section h3 {
            font-size: 20px;
            font-weight: 700;
        }

        .left-section p {
            font-size: 14px;
            margin: 4px 0;
        }

        .join-btn {
            margin-top: 10px;
            background: #fff;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            width: fit-content;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .join-btn:hover {
            background: #fef1c5;
        }

        /* Right Section */
        .right-section {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo h2 {
            font-size: 22px;
            font-weight: 700;
        }

        .nutra {
            color: #fff;
        }

        .pass {
            background: #fff;
            color: #f5b700;
            padding: 0 5px;
            border-radius: 4px;
        }

        .tagline {
            font-size: 10px;
            color: #333;
            font-weight: 500;
        }

        /* Plans Scroll Container */
        .scroll-container-right {
            display: flex;
            justify-content: flex-start;
            gap: 20px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            width: 100%;
            padding-bottom: 10px;
            scrollbar-width: none; /* Firefox */
        }

        .scroll-container-right::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Edge */
        }

        /* Plan Cards */
        .plan {
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            padding: 10px;
            padding-top: 0px;
            border-radius: 12px;
            width: 100px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .plan:hover {
            transform: translateY(-4px);
        }

        .plan.active {
            background: #fff;
            border: 2px solid #f5b700;
            color: #000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: scale(1.05);
        }

        /* 👇 Highlight "months" text in active plan */
        .plan.active .months {
            color: #f5b700;
        }

        .plan .plan-title {
            background: #f6c34a;
            color: white;
            font-size: 12px;
            font-weight: 600;
            /* padding: 3px 6px; */
            border-radius: 3px;
            margin-bottom: 8px;
            display: inline-block;
        }

        .plan .months {
            font-size: 26px;
            font-weight: 700;
        }

        .plan .details {
            font-size: 12px;
            line-height: 1.4;
            margin-top: 6px;
        }

        .plan .price {
            font-weight: 600;
            font-size: 13px;
        }

        .highlight {
            background: #f5b700 !important;
        }

        .save {
            color: #ff3b3b;
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nutrapass-container {
                flex-direction: column;
                text-align: center;
            }

            .left-section,
            .right-section {
                align-items: center;
            }

            .scroll-container-right {
                justify-content: center;
            }
        }
    </style>
    <style>
        .scroll-container-right {
            display: flex;
            overflow-x: auto;
            /* Horizontal scroll */
            justify-content: flex-end;
            /* Align plans to right */
            gap: 15px;
            padding: 10px;
            scroll-behavior: smooth;
            /* smooth scroll */
        }

        .scroll-container-right::-webkit-scrollbar {
            height: 6px;
            /* scrollbar height */
        }

        .scroll-container-right::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .plan {
            min-width: 150px;
            background: transparent;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            flex-shrink: 0;
            /* prevent shrinking */
        }

        .plan.selected {
            background: white;
            color: black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border: 2px solid #f5b300;
        }

        /* Recolor text inside selected plan */
        .plan.selected h4,
        .plan.selected .months,
        .plan.selected .price {
            color: #f5b300;
        }

        .plan.selected h4 {
            background: linear-gradient(90deg, #f7ce68, #f5b300);
            color: #0A6A6A
        }

        .months {
            font-size: 28px;
            font-weight: bold;
            color: white
        }

        .plan h4 {
            font-size: 18px;
            margin-bottom: 10px;
            padding: 5px 10px;
            display: inline-block;
            border-radius: 5px;
            background: transparent;
            color: white;
        }

        .plan:not(:has(h4)) .details {
            margin-top: 20px;
            /* or any value that aligns the text properly */
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <main class="main">

        @include('home.countdown')



        <section class="home-slider position-relative mb-30">
            <div class="container">
                <div class="home-slide-cover mt-30">

                    <div class="swiper myBannerSwiper">
                        <div class="swiper-wrapper">

                            @foreach($website_main_banner as $banner)
                                @php
                                    $slug = '';

                                    if (!empty($banner->category_id)) {
                                        $slug = \App\Models\Category::find($banner->category_id)->slug ?? '';
                                    }

                                    if (!empty($banner->brand_id)) {
                                        $slug = \App\Models\Brand::find($banner->brand_id)->slug ?? '';
                                    }

                                    $url = url('collections/' . $slug);
                                @endphp

                                @if(!empty($banner->banner_img))
                                    <div class="swiper-slide">
                                        <a href="{{ $url }}">
                                            <img src="{{ $banner->banner_img }}"
                                                 class="img-fluid home-banner"
                                                 alt="Banner">
                                        </a>
                                    </div>
                                @endif
                            @endforeach

                        </div>

                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                </div>
            </div>
        </section>



        <!--End hero slider-->
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Categories</h3>
                    </div>
                    <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow"
                         id="carausel-10-columns-arrows"></div>
                </div>
                <div class="carausel-10-columns-cover position-relative">
                    <div class="carausel-10-columns" id="carausel-10-columns">
                        @foreach($categories as $category)
                            <div class="col-md-2">
                                <div class="border-1 text-center " style="background: #DEFFFF">
                                    <figure>
                                        <a href="{{ url('collections/' . $category->slug) }}">
                                            <img src="{{$category->image ?? ''}}" alt="" style="height:100px;"/>
                                        </a>
                                    </figure>
                                    <h4>
                                        <a href='{{ url('collections/' . $category->slug) }}'>{{$category->name ?? ''}}</a>
                                    </h4>
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </section>


        <!--End category slider-->


        <section class="banners mb-25 d-none d-lg-block">
            <div class="container">
                <div class="row">
                    @foreach($small_banners as $banner)
                       @php
                        $slug = '';
                        @endphp
                        @if(!empty($banner->category_id))
                            @php
                                $slug = \App\Models\Category::find($banner->category_id)->slug??'';
                            @endphp
                        @endif
                        @if(!empty($banner->brand_id))
                            @php
                                $slug = \App\Models\Brand::find($banner->brand_id)->slug??'';
                            @endphp
                        @endif



                    @php
                        $url =  url('collections/' . $slug);
                    @endphp
                        <div class="col-lg-4 col-md-6 p-2">
                            <a href="{{$url}}">
                                <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                                    <img src="{{$banner->banner_img}}" alt=""/>
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!--End banners-->
        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div
                    class="section-title style-2 wow animate__animated animate__fadeIn d-flex justify-content-between align-items-center">
                    <h3>Best Sellers</h3>
                    <a href="{{route('best_sellers')}}" class="btn btn-sm btn-primary">See All</a>
                </div>
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @foreach ($best_sellers as $product)
                                @if ($loop->index < 10)
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-1-5">
                                        @include('home.single_product', ['product' => $product])
                                    </div>
                                @endif
                            @endforeach
                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>
        <!--Products Tabs-->

        <section class="banners mb-25">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            <img src="{{ $fixed_banner_1->banner_img ?? '' }}" alt=""/>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @if(!empty($fixed_banner_1->products))
                                @foreach ($fixed_banner_1->products as $product)
                                    @if ($loop->index < 10)
                                        <div class="col-6 col-sm-6 col-md-4 col-lg-1-5">
                                            @include('home.single_product', ['product' => $product])
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>


        <section class="popular-categories section-padding">
            <div class="container">
                <div class="section-title">
                    <div class="title">
                        <h3>Shop by Brands</h3>
                    </div>
                    <div class="slider-arrow slider-arrow-2 flex-right carausel-8-columns-arrow"
                         id="carausel-8-columns-arrows"></div>
                </div>
                <div class="carausel-8-columns-cover position-relative">
                    <div class="carausel-8-columns" id="carausel-8-columns">
                        @foreach ($brands as $brand)
                            <div class="col-md-2">
                                <div class="border-1 text-center">
                                    <figure>
                                        <a href="{{ url('collections/' . $brand->slug) }}">
                                            <img src="{{$brand->brand_img}}" alt="" style="height:100px;"/>
                                        </a>
                                    </figure>
                                    <h4>{{ $brand->brand_name ?? '' }}</h4>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
            </div>
        </section>


        <section class="section-padding pb-5">
            <div class="container">
                <div class="section-title wow animate__animated animate__fadeIn" data-wow-delay="0">
                    <img src="{{ $download_banner->banner_img ?? '' }}" alt=""/>
                </div>
            </div>
        </section>
        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div
                    class="section-title style-2 wow animate__animated animate__fadeIn d-flex justify-content-between align-items-center">
                    <h3>Best Deals</h3>
                    <a href="{{route('best_deals')}}" class="btn btn-sm btn-primary">See All</a>
                </div>
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @foreach ($best_deals as $product)
                                @if ($loop->index < 10)
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-1-5">
                                        @include('home.single_product', ['product' => $product])
                                    </div>
                                @endif
                            @endforeach
                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Shop By Goal</h3>
                    </div>
                    <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow"
                         id="carausel-10-columns-arrows"></div>
                </div>
                <div class="carausel-10-columns-cover position-relative">
                    <div class="carausel-10-columns" id="carausel-10-columns1">
                        @foreach($goalcategories as $category)
                            <div class="col-md-2">
                                <div class="border-1 text-center " style="background: #DEFFFF">
                                    <figure>
                                        <a href="{{ url('collections/' . $category->slug) }}">
                                            <img src="{{$category->image ?? ''}}" alt="" style="height:100px;"/>
                                        </a>
                                    </figure>
                                    <h4>
                                        <a href='{{ url('collections/' . $category->slug) }}'>{{$category->name ?? ''}}</a>
                                    </h4>
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </section>


        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div class="row g-3 d-flex align-items-stretch flex-column flex-md-row">
                    <!-- Left Banner -->
                    <div class="col-12 col-md-8 d-flex flex-column d-none d-md-flex">
                        <div class="h-100">
{{--                            <div class="position-relative banner-img">--}}
{{--                                <img src="{{url('public/assets/images/refer.png')}}" class="img-fluid w-100 rounded"--}}
{{--                                     alt="Refer Banner"/>--}}
{{--                                <div class="position-absolute top-50 translate-middle text-left"--}}
{{--                                     style="left: 30%;">--}}
{{--                                    <span class="d-block text-white fs-5 fw-bold">--}}
{{--                                        Refer a friend and earn rewards!--}}
{{--                                    </span>--}}
{{--                                    <p class="text-white mt-2 mb-3"--}}
{{--                                       style="font-size: 12px; font-family: 'Lato', sans-serif;">--}}
{{--                                        Explore the perfect supplements designed just for you! Start <br> your journey--}}
{{--                                        to--}}
{{--                                        better--}}
{{--                                        health today and find what suits your needs best.--}}
{{--                                    </p>--}}
{{--                                    <a class='btn btn-primary' onclick="checkLoginRedirect('{{route('refer_earn')}}')">Join Now</a>--}}
{{--                                </div>--}}
{{--                            </div>--}}


                            <div class="position-relative banner-img" style="position: relative;">
                                <img src="{{url('public/assets/nutrap.svg')}}"
                                     style="width: 100%; display: block; border-radius: 12px;">

                                <!-- Overlay text -->
                                <div class="banner-text" style="
      position: absolute;
      top: 52%;
      left: 30px;
      color: #333;
      font-weight: bold;
      max-width: 450px;
      transform: translateY(-50%);
  ">
                                    <h3 style="font-weight: 700; font-size: 25px;">Join Wellness+ Membership</h3>
                                    <p id="subscription_html1">

                                    </p>
                                    <button class="btn" onclick="subscribeNow()"  style="
        width: 80%;
        border-radius: 8px;
        padding: 10px 20px;
        background: #fff;
        border: none;
        color: #000;
        font-weight: 600;
        cursor: pointer;
        margin-top: 15px;
    ">Join Now
                                    </button>
                                </div>

                                <!-- Overlay scrollable plans -->
                                <div class="scroll-container-right" style="
  position: absolute;
  bottom: 25px;
  left: 73%;
  transform: translateX(-50%);
  display: flex;
  justify-content: flex-start;
  gap: 20px;
  padding-bottom: 5px;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  width: 500px; /* optional - container width control */
  scrollbar-color: #ccc transparent;
">
                                    @foreach($subscription['subscription_plans'] as $key =>  $plan)
                                        @php
                                            $permonth = (int)$plan->price / (int)$plan->duration;
                                            $permonth = round($permonth);
                                             $standardMonthlyPrice = (int)$plan->price;
        $totalStandardPrice = $standardMonthlyPrice * $plan->duration;
                                             $savePercent = 0;
            if ($totalStandardPrice > 0) {
                $savePercent = round((($totalStandardPrice - $plan->price) / $totalStandardPrice) * 100);
            }
                                        @endphp

                                        <div class="plan {{$key == 0 ? 'selected' : ''}}"  onclick="selectPlan('{{ $plan->id }}','{{ $plan->duration }}','{!! $plan->terms  !!}')"   id="plan{{ $plan->duration }}"
                                             data-duration="{{ $plan->duration }}"
                                             data-id="{{ $plan->id }}"
                                             data-terms="{{ htmlentities($plan->terms) }}"
                                             data-price="{{ $plan->price }}">

                                            @if($plan->is_best_value == 1)
                                                <h4 class="plan-title">Best Value</h4>
                                            @else
                                                <p>&nbsp;</p>
                                            @endif
                                            <div class="months">{{$plan->duration}}</div>
                                            <div class="details">months<br>₹ {{$permonth}}
                                                /mo<br><strong>SAVE {{$savePercent}}%</strong></div>
                                            <hr>
                                            <div class="price">₹ {{$plan->price ?? ''}}</div>
                                        </div>
                                    @endforeach
                                </div>



                            </div>



                        </div>
                    </div>
                    <!-- Right Banner -->
                    <div class="col-md-4 d-flex flex-column">
                        <div class="h-100">
                            <div class="position-relative banner-img mt-3 mt-md-0"
                                 style="min-height: 350px; ">
                                <img src="{{ $instant_expert->banner_img??url('public/assets/images/consultation.png') }}"
                                     class="img-fluid w-100 h-100"
                                     style="object-fit: fill;" alt="Consultation Banner"/>
                                <div class="position-absolute top-50 translate-middle text-center p-3"
                                     style="border-radius: 10px; left:50%; width:100%;  display:flex; justify-content: space-between; flex-direction: column;">
                                    <div class="padding_set">
{{--                                        <h5 class="text-white fw-bold">Instant Expert Guidance</h5>--}}
{{--                                        <p class="text-white mb-3">--}}
{{--                                            Get immediate access to expert advice and insights tailored just for you!--}}
{{--                                        </p>--}}
                                    </div>
                                    <div style="padding-bottom: 20px">
                                        <a class='btn btn-primary' href='{{url('nc_consult')}}'>Connect now</a>
                                        <p class="text-white mt-2 mb-0">
                                            Get your customized nutrition and lifestyle plan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

{{--        <section class="banners mb-25">--}}
{{--            <div class="container">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-lg-12">--}}
{{--                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">--}}
{{--                            <img src="{{ $fixed_banner_2->banner_img ?? '' }}" alt="" style="width: 100%"/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </section>--}}

        <section class="banners mb-25">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            <img src="{{ $fixed_banner_3->banner_img ?? '' }}" alt="" style="width: 100%"/>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div
                    class="section-title style-2 wow animate__animated animate__fadeIn d-flex justify-content-between align-items-center">
                    <h3>New Arrival</h3>
                    <a href="{{route('new_arrivals')}}" class="btn btn-sm btn-primary">See All</a>
                </div>
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @foreach ($newArrival as $product)
                                @if ($loop->index < 10)
                                    <div class="col-6 col-sm-6 col-md-4 col-lg-1-5">
                                        @include('home.single_product', ['product' => $product])
                                    </div>
                                @endif
                            @endforeach
                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>
        <style>
            .slider-wrapper {
                position: relative;
                overflow: hidden;
                width: 100%;
            }

            .slider-track {
                display: flex;
                transition: transform 0.5s ease;
            }

            .slide {
                box-sizing: border-box;
                padding: 0 10px;
            }

            .video-slide iframe {
                width: 100%;
                height: 500px;
                border-radius: 12px;
                display: block;
            }

            .slider-arrows button {
                background: #00a8a8;
                color: #fff;
                border: none;
                padding: 8px 14px;
                border-radius: 50%;
                cursor: pointer;
                margin-left: 5px;
                transition: background 0.3s;
            }

            .slider-arrows button:hover {
                background: #007575;
            }

            .slide {
                min-width: 25%;
                /* default: 3 per row (desktop) */
            }

            /* Tablet & Mobile (≤ 768px) → show 2 per row */
            @media (max-width: 768px) {
                .slide {
                    min-width: 50%;
                    /* 2 per row */
                }
            }

            /* Small Mobile (≤ 480px) → show 1 per row */
            @media (max-width: 480px) {
                .slide {
                    min-width: 100%;
                    /* 1 per row */
                }
            }

            .testimonial-card {
                position: relative;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                font-family: Arial, sans-serif;
                color: #fff;
            }

            .testimonial-card img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .testimonial-card .overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2) 20%, rgba(0, 0, 0, 0.7) 100%);
            }

            .testimonial-card .content {
                position: absolute;
                bottom: 0;
                padding: 15px;
            }

            .testimonial-card h4 {
                margin: 0 0 10px;
                font-size: 18px;
                font-weight: bold;
                color: #00e0ff;
                /* teal highlight for name */
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
            }

            .testimonial-card p {
                margin: 0;
                font-size: 14px;
                line-height: 1.4;
            }
        </style>
        <section class="popular-categories section-padding">
            <div class="container">
                <div class="section-title d-flex justify-content-between align-items-center">
                    <div class="title">
                        <h3>Wellness Series</h3>
                    </div>
                    <div class="slider-arrows">
                        <button class="prev-btn" data-target="videoSlider">⟨</button>
                        <button class="next-btn" data-target="videoSlider">⟩</button>
                    </div>
                </div>

                <div class="slider-wrapper">
                    <div class="slider-track" id="videoSlider">
                        @foreach ($new_updates as $new_update)
                            <div class="slide">
                                <div class="video-slide">
                                    <iframe
                                        src="https://www.youtube-nocookie.com/embed/{{ $new_update->image }}?rel=0&mute=1"
                                        title="Reel Video" frameborder="0"
                                        allow="autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>


        <section class="popular-categories section-padding">
            <div class="container">
                <div class="section-title d-flex justify-content-between align-items-center">
                    <div class="title">
                        <h3>Happy Customers</h3>
                    </div>
                    <div class="slider-arrows">
                        <button class="prev-btn" data-target="testimonialSlider">⟨</button>
                        <button class="next-btn" data-target="testimonialSlider">⟩</button>
                    </div>
                </div>

                <div class="slider-wrapper">
                    <div class="slider-track" id="testimonialSlider">
                        @foreach ($testimonials as $testimonial)
                            <div class="slide">
                                <div class="testimonial-card">
                                    <img src=" {{$testimonial->image ?? ''}}" alt="Surya" style="height: 500px">
                                    <div class="overlay"></div>
                                    <div class="content">
                                        <h4> {{$testimonial->name ?? ''}}</h4>
                                        <p style="color: white">
                                            {{$testimonial->description ?? ''}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <style>
            /* Main Banner */
            .health-banner {
                width: 100%;
                background: white;
                border-radius: 30px;
                display: flex;
                overflow: hidden;
                box-shadow: 0px 6px 30px rgba(0, 0, 0, 0.15);
                max-width: 82%;
                margin: 20px auto;
            }

            /* Left side */
            .banner-left {
                flex: 1;
                background: #00A8A870;
                padding: 60px 50px;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .banner-left h2 {
                font-size: 38px;
                font-weight: 700;
                color: white;
                line-height: 1.3;
            }

            .banner-left p {
                margin-top: 10px;
                font-size: 16px;
                opacity: 0.9;
            }

            /* Email Input */
            .email-box {
                margin-top: 35px;
                background: white;
                border-radius: 40px;
                display: flex;
                align-items: center;
                padding: 5px;
                width: 80%;
            }

            .email-box input {
                border: none;
                outline: none;
                flex: 1;
                padding: 12px 15px;
                font-size: 15px;
                border-radius: 40px;
            }

            .email-box button {
                background: #00a8a5;
                color: white;
                padding: 12px 25px;
                border-radius: 40px;
                border: none;
                cursor: pointer;
                font-weight: 600;
            }

            .email-icon {
                font-size: 18px;
                margin-left: 15px;
            }

            /* Right side */
            .banner-right {
                flex: 1.2;
            }

            .banner-right img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }


            /* Responsive */
            @media (max-width: 768px) {
                .health-banner {
                    flex-direction: column;
                }

                .banner-left {
                    padding: 40px 30px;
                    text-align: center;
                }

                .email-box {
                    width: 100%;
                }

                .banner-right img {
                    height: 300px;
                }
            }
        </style>
        <div class="health-banner">

            <!-- Left Section -->
            <div class="banner-left">
                <h2>Stay home & get your health<br>needs from our shop</h2>
                <p>Start Your Health Shopping with NutraCore</p>

                <div class="email-box">
                    <span class="email-icon">✉</span>
                    <input type="email" placeholder="Your email address">
                    <button>Subscribe</button>
                </div>
            </div>

            <!-- Right Section -->
            <div class="banner-right">
                <img src="{{url('public/assets/newsletter.png')}}" alt="">
            </div>

        </div>


        <section class="featured section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6 mb-md-4 mb-xl-0">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay="0">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-1.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Best prices & offers</h3>
                                <p>Orders $50 or more</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".1s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-2.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Free delivery</h3>
                                <p>24/7 amazing services</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".2s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-3.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Great daily deal</h3>
                                <p>When you sign up</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".3s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-4.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Wide assortment</h3>
                                <p>Mega Discounts</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".4s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-5.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Easy returns</h3>
                                <p>Within 30 days</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6 d-xl-none">
                        <div
                            class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".5s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-6.svg" alt=""/>
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Safe delivery</h3>
                                <p>Within 30 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function initSlider(trackId) {
            const track = document.getElementById(trackId);
            const slides = track.querySelectorAll('.slide');
            let index = 0;

            function showSlide(i) {
                index = (i + slides.length) % slides.length;
                track.style.transform = `translateX(-${index * 100}%)`;
            }

            // attach events to buttons with matching data-target
            document.querySelectorAll(`[data-target="${trackId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.classList.contains('prev-btn')) {
                        showSlide(index - 1);
                    } else {
                        showSlide(index + 1);
                    }
                });
            });
        }

        // Init sliders
        initSlider('videoSlider');
        initSlider('testimonialSlider');
    </script>

    <script>
        function selectPlan(selected) {
            // Remove active class from all
            document.querySelectorAll('.plan').forEach(plan => {
                plan.classList.remove('selected');
            });

            // Add active class to clicked plan
            selected.classList.add('selected');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".myBannerSwiper", {
            loop: true,
            autoplay: {
                delay: 3000,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            speed: 600,
            spaceBetween: 20,
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const plans = document.querySelectorAll(".plan");

            // ✅ Default first plan active
            if (plans.length > 0) {
                plans[0].classList.add("active");
                plans[0].querySelector(".months").style.color = "#db6001";
            }

            // ✅ Click event for each plan
            plans.forEach(plan => {
                plan.addEventListener("click", function () {
                    // sab plans se active class hatao
                    plans.forEach(p => {
                        p.classList.remove("active");
                        p.querySelector(".months").style.color = "#000"; // reset color
                    });

                    // clicked plan ko active karo
                    this.classList.add("active");
                    this.querySelector(".months").style.color = "#db6001";
                });
            });
        });
    </script>

@endsection
