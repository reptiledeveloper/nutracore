@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $fixed_banner_1 = [];
    $fixed_banner_2 = [];
    $fixed_banner_3 = [];
    $download_banner = [];
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
        }
    }
    $banner_types = ['', 'product', 'brand', 'category', 'link'];

    ?>
    <style>
        .single-hero-slider {
            width: 100%;
            height: 600px !important;
            background-size: 100% 100% !important; /* force stretch width & height */
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

        iframe, video {
            border: none;
            object-fit: cover;
            height: 500px; /* Reel size */
        }

        .start-30 {
            left: 35% !important;
        }
    </style>
    <main class="main">
        <section class="home-slider position-relative mb-30">
            <div class="container">
                <div class="home-slide-cover mt-30">
                    <div class="hero-slider-1 style-4">
                        @foreach($banners as $banner)
                            @if(in_array($banner->type,$banner_types))
                                <div class="single-hero-slider single-animation-wrap"
                                     style="background-image: url({{$banner->banner_img}})">
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="slider-arrow hero-slider-1-arrow"></div>
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

                        <div class="col-lg-4 col-md-6">
                            <a href="">
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
                            @foreach ($fixed_banner_1->products as $product)
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

        <style>
            .scroll-container-right {
                display: flex;
                overflow-x: auto; /* Horizontal scroll */
                justify-content: flex-end; /* Align plans to right */
                gap: 15px;
                padding: 10px;
                scroll-behavior: smooth; /* smooth scroll */
            }

            .scroll-container-right::-webkit-scrollbar {
                height: 6px; /* scrollbar height */
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
                flex-shrink: 0; /* prevent shrinking */
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
                color: white
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
                margin-top: 20px; /* or any value that aligns the text properly */
            }
        </style>

        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div class="row g-3 d-flex align-items-stretch flex-column flex-md-row">
                    <!-- Left Banner -->
                    <div class="col-12 col-md-8 d-flex flex-column d-none d-md-flex">
                        <div class="h-100">
                            <div class="position-relative banner-img">
                                <img src="{{url('public/assets/images/refer.png')}}" class="img-fluid w-100 rounded"
                                     alt="Refer Banner"/>
                                <div class="position-absolute top-50 start-30  translate-middle text-center p-3"
                                     style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                                            <span class="d-block text-white fs-5 fw-bold">
                                                Refer a friend and earn rewards!
                                            </span>
                                    <p class="text-white mt-2 mb-3">
                                        Explore the perfect supplements designed just for you! Start your journey to
                                        better
                                        health today and find what suits your needs best.
                                    </p>
                                    <a class='btn btn-primary' href=''>Join Now</a>
                                </div>
                            </div>


                            <div class="position-relative banner-img" style="position: relative;">
                                <img src="{{url('public/assets/nutrap.svg')}}" style="width: 100%; display: block;">

                                <!-- Overlay text -->
                                <div class="banner-text" style="
        position: absolute;
        top: 40%;  /* Adjust vertical position */
        left: 20px;
        color: white;
        font-weight: bold;
        max-width: 450px;
    ">
                                    <h3>Join Wellness+ Membership</h3>
                                    <p class="mt-2">🔥 10% OFF every order</p>
                                    <p class="mt-2">🚚 Free Express Delivery</p>
                                    <p class="mt-2">🎁 Monthly Freebie Box</p>
                                    <p class="mt-2">⏰ Early Access & Secret Sales</p>
                                    <button style="
            width:100%;
            border-radius:10px;
            padding: 10px 20px;
            background: white;
            border: none;
            color: black;
            cursor: pointer;
            margin-top:15px;
        ">Join Now
                                    </button>
                                </div>

                                <!-- Overlay scrollable plans -->
                                <div class="scroll-container-right" style="
        position: absolute;
        bottom: 20px;   /* Position from bottom of image */
        left: 20px;     /* Start from left */
        right: 20px;    /* Allow it to stretch horizontally */
        display: flex;
        overflow-x: auto;
        gap: 15px;
        padding-bottom: 5px;
    ">
                                    <div class="plan" onclick="selectPlan(this)">
                                        <h4>Best Value</h4>
                                        <div class="months">12</div>
                                        <div class="details">months<br>₹ 100/mo<br><strong>SAVE 20%</strong></div>
                                        <hr>
                                        <div class="price">₹ 1200</div>
                                    </div>

                                    <div class="plan" onclick="selectPlan(this)">
                                        <div class="months">6</div>
                                        <div class="details">months<br>₹ 150/mo<br><strong>SAVE 10%</strong></div>
                                        <hr>
                                        <div class="price">₹ 900</div>
                                    </div>

                                    <div class="plan" onclick="selectPlan(this)">
                                        <div class="months">3</div>
                                        <div class="details">months<br>₹ 200/mo<br><strong>SAVE 0%</strong></div>
                                        <hr>
                                        <div class="price">₹ 600</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Right Banner -->
                    <div class="col-md-4 d-flex flex-column">
                        <div class="h-100">
                            <div class="position-relative banner-img mt-3 mt-md-0" style="min-height: 350px; height: 100%;">
                                    <img src="{{ url('public/assets/images/consultation.png') }}"
                                         class="img-fluid w-100 h-100"
                                         style="object-fit: fill;"
                                         alt="Consultation Banner"/>
                                <div class="position-absolute top-50 start-50 translate-middle text-center p-3"
                                     style="border-radius: 10px; max-width: 90%;">
                                    <h5 class="text-white fw-bold">Instant Expert Guidance</h5>
                                    <p class="text-white mb-3">
                                        Get immediate access to expert advice and insights tailored just for you!
                                    </p>
                                    <a class='btn btn-primary' href=''>Connect now</a>
                                    <p class="text-white mt-2 mb-0">
                                        Get your customized nutrition and lifestyle plan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="banners mb-25">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            <img src="{{ $fixed_banner_2->banner_img ?? '' }}" alt=""/>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="banners mb-25">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            <img src="{{ $fixed_banner_3->banner_img ?? '' }}" alt=""/>
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
                min-width: 25%; /* default: 3 per row (desktop) */
            }

            /* Tablet & Mobile (≤ 768px) → show 2 per row */
            @media (max-width: 768px) {
                .slide {
                    min-width: 50%; /* 2 per row */
                }
            }

            /* Small Mobile (≤ 480px) → show 1 per row */
            @media (max-width: 480px) {
                .slide {
                    min-width: 100%; /* 1 per row */
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
                color: #00e0ff; /* teal highlight for name */
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
                        <h3>Wellness Series (Videos)</h3>
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
                                        src="https://www.youtube.com/embed/{{ $new_update->image }}?rel=0&mute=1"
                                        title="Reel Video"
                                        frameborder="0"
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
                                    <img src=" {{$testimonial->image??''}}" alt="Surya" style="height: 500px">
                                    <div class="overlay"></div>
                                    <div class="content">
                                        <h4> {{$testimonial->name??''}}</h4>
                                        <p style="color: white">
                                            {{$testimonial->description??''}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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

@endsection
