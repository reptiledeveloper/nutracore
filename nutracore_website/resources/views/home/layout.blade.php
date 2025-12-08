<?php

use App\Helpers\CustomHelper;


$user = Auth::user();
$total_qty = 0;
if (!empty($user)) {
    $total_qty = \App\Models\Cart::where('user_id', $user->id)->sum('qty');
}

$address = session('address');
$latitude = session('latitude');
$longitude = session('longitude');
if (!empty($user)) {
    $user_address = \App\Models\UserAddress::where('id', $user->addressID)->first();
    $address = $user_address->flat_no ?? '';
    $address .= $user_address->building_name ?? '';
    $address .= $user_address->landmark ?? '';
    $address .= $user_address->location ?? '';
}

$categories = \App\Models\Category::select('id', 'name', 'image', 'priority', 'slug')
    ->where(['status' => 1, 'parent_id' => 0, 'is_goal' => 0, 'is_delete' => 0])
    ->orderBy('priority')
    ->get()->map(fn($cat) => tap($cat, fn($c) => $c->image = CustomHelper::getImageUrl('categories', $c->image)));

$allcategories = $categories = \App\Models\Category::select('id', 'name', 'image', 'priority', 'slug', 'is_popular')
    ->where(['status' => 1, 'parent_id' => 0, 'is_delete' => 0])->get();

$goal_category = \App\Models\Category::select('id', 'name', 'image', 'priority', 'slug')
    ->where(['status' => 1, 'parent_id' => 0, 'is_goal' => 1, 'is_delete' => 0])
    ->orderBy('priority')
    ->get()
    ->map(fn($cat) => tap($cat, fn($c) => $c->image = CustomHelper::getImageUrl('categories', $c->image)));

$brands = \App\Models\Brand::select('id', 'brand_img', 'brand_name', 'certificate', 'priority', 'slug', 'is_popular')
    ->where(['status' => 1, 'is_delete' => 0])
    ->orderBy('priority')
    ->get()
    ->map(fn($brand) => tap($brand, function ($b) {
        $b->brand_img = CustomHelper::getImageUrl('brands', $b->brand_img);
        $b->brand_icon = $b->brand_img;
        $b->certificate = CustomHelper::getImageUrl('brands', $b->certificate);
    }));
?>

    <!DOCTYPE html>
<html class="no-js" lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>

<head>
    <meta charset="utf-8"/>
    <title>Nutracore</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content=""/>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta property="og:title" content=""/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{url('public/assets')}}/images/default.png"/>
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{url('public/assets')}}/css/plugins/animate.min.css"/>
    <link rel="stylesheet" href="{{url('public/assets')}}/css/main2cc5.css?v=5.6"/>
    <link rel="stylesheet" href="{{url('public/assets')}}/css/responsive.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{--    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>--}}

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"
    />
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"
    />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GT-K55F9G2F"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'GT-K55F9G2F');
    </script>

</head>
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K9FG2LRP"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="toast" class="toast"></div>
<body class="bg-gray-100 min-h-screen flex flex-col justify-between">


<style>
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #00A8A8;
        color: #fff;
        padding: 12px 18px;
        border-radius: 6px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease, transform 0.4s ease;
        transform: translateY(-20px);
        z-index: 9999;
    }

    .toast.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    :root {
        --font-main: "Poppins", sans-serif;
    }

    body {
        font-family: var(--font-main);
    }

    .action-btn.filled .fi-rs-heart {
        color: red;
    }

    .header-action-right .search-location {
        display: block;
    }

    .pac-container {
        z-index: 999999999 !important;
    }

    .main-menu > nav > ul > li > a {

        font-size: 12px !important;
    }

    .header-box {
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 12px 16px;
        border-radius: 8px;
        max-width: 400px;
        margin-left: 30px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .address-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex-grow: 1;
    }

    .down-arrow {
        flex-shrink: 0;
        font-size: 14px;
        color: #666;
    }

    /*.modal-body {*/
    /*    height: 400px;*/
    /*    padding: 0;*/
    /*}*/
    .mega-children img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .mega-children img:hover {
        transform: scale(1.1);
    }

    .menu-link {
        font-size: 16px; /* Same size for text */
        display: flex; /* Align icon and text */
        align-items: center; /* Vertical center */
        text-decoration: none;
        color: inherit;
    }

    .menu-link i {
        font-size: 16px !important; /* Match text size */
        margin-right: 5px; /* Spacing between icon and text */
    }


</style>
<style>
    .membership-card {
        display: flex;
        align-items: center;
        background-color: #fff7e6;
        border-radius: 12px;
        padding: 16px;
        max-width: 500px;
        margin: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-section {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 0 12px;
    }

    .icon img {
        width: 40px;
        height: 40px;
    }

    .text {
        margin-left: 12px;
    }

    .text .title {
        font-weight: bold;
        color: #d4a200;
        font-size: 16px;
    }

    .text .subtitle {
        color: #8c6d1f;
        font-size: 12px;
    }

    .divider {
        width: 1px;
        height: 50px;
        background-color: #e0d4b8;
    }

    .arrow {
        margin-left: auto;
        font-size: 20px;
        color: #d4a200;
    }

</style>
<style>
    /* Hide by default */
    .mobile-bottom-nav {
        display: none;
    }

    /* Show only on mobile */
    @media (max-width: 768px) {
        .mobile-bottom-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
            padding: 6px 0;
            z-index: 9999;
        }

        .mobile-bottom-nav .nav-item {
            flex: 1;
            text-align: center;
            color: #555;
            font-size: 11px;
            font-weight: 500;
        }

        .mobile-bottom-nav .nav-item img {
            height: 24px;
            margin: 0 auto 3px;
            display: block;
        }

        .mobile-bottom-nav .nav-item.active,
        .mobile-bottom-nav .nav-item:hover {
            color: #00A8A8;
        }
    }
</style>
<style>
    /* Import Poppins font */
    .button-container1 {
        height: 24px;
        display: flex;
        align-items: center;
        background: linear-gradient(to right, #a5f6f6, #a5f6f6, #a5f6f6);
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        overflow: hidden;
        position: relative;
        /* padding-right: 30px; */
    }

    .nutrapass-circle1 {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        margin-left: 10px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        position: relative;
        left: -6px;
        border: 0px solid white;
        flex-shrink: 0;
    }

    .button-container {
        height: 20px;
        display: flex;
        align-items: center;
        background: linear-gradient(to right, #E5A527, #FFEAA9, #eed7b4);
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        overflow: hidden;
        position: relative;
        /* padding-right: 30px; */
    }

    .nutrapass-circle {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        margin-left: 10px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        position: relative;
        left: -6px;
        border: 0px solid white;
        flex-shrink: 0;
    }

    .nutrapass-circle span {
        line-height: 1.2;
    }

    .button-text {
        color: #333;
        font-size: 9px;
        font-weight: 500;
        margin-left: -3px;
        /* white-space: nowrap; */
    }

    .arrow {
        font-size: 16px;
        color: #333;
        margin-left: 20px;
        position: absolute;
        right: 10px;
    }

    .card-2 figure img {
        height: 80px;
    }

    .product-cart-wrap .product-card-bottom {
        margin-top: 1px !important;
    }

    .product-name {
        font-size: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<style>
    .border-1 {
        border: 1px solid #ccc;
        padding: 10px;
        margin: 10px;
        border-radius: 8px;
        display: flex;
        justify-content: center; /* horizontally center */
        align-items: center; /* vertically center */
        height: 150px; /* or any fixed height you want */
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .border-1:hover {
        border-color: #007bff; /* change border color on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .border-1 img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .border-1 {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 10px;
    }

    .border-1 figure {
        margin: 0;
    }

    .border-1 h4 {
        margin-top: 10px;
        font-size: 14px;
    }


</style>
<style>

    .profile-card {
        display: flex;
        align-items: center;
        background: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
    }

    .avatar {
        width: 70px; /* Set the desired width */
        height: 70px; /* Set the desired height */
        overflow: hidden; /* Hide overflow so image doesn't spill out */
        border-radius: 50%; /* Makes it a circle */
        display: inline-block;
    }

    .avatar img {
        width: 100%; /* Make image cover the container */
        height: 100%; /* Make image cover the container */
        object-fit: cover; /* Ensures the image scales correctly and fills circle */
    }

    .profile-info {
        flex: 1;
        padding: 10px;
    }

    .profile-info h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .profile-info p {
        margin: 2px 0 0;
        font-size: 14px;
        color: #555;
    }

    .edit-icon {
        cursor: pointer;
        font-size: 18px;
        color: #555;
    }
</style>
<style>

    .menu {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        overflow: hidden;
    }

    .menu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.2s;
    }

    .menu-item:last-child {
        border-bottom: none;
    }

    .menu-item:hover {
        background-color: #f0f0f0;
    }

    .menu-item span {
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .menu-item i {
        margin-right: 12px;
        font-size: 18px;
    }

    .arrow {
        color: #999;
    }

    @media only screen and (max-width: 768px) {
        .logo.logo-width-1 {
            margin-right: 0;
            position: absolute;
            top: -12px;
            font-size: 12px;
            left: 39%;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
            font-weight: 700;
        }
    }

    .address_phone {
        font-size: 13px;
        font-weight: 400;
        line-height: 16px;
        margin-bottom: 5px;
        color: #7E7E7E;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* limit to 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media only screen and (max-width: 1024px) {
        .hero-slider-1 .single-hero-slider {
            height: 230px;
        }
    }

    @media only screen and (max-width: 768px) {
        .home-slider .hero-slider-1 {
            height: 200px;
        }
    }

    .slick-dots {
        display: none !important;
    }

    /* -------------------------------------- */
    /* Correct Mobile Alignment for Home + Address */
    /* -------------------------------------- */
    @media only screen and (max-width: 768px) {

        .logo.logo-width-1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -12px;
            text-align: left; /* Left align inside */
            z-index: 10;
            width: 80%; /* Increase width so text has space to align left */

        }

        .logo.logo-width-1 > div {

        }

        .logo.logo-width-1 span {
            font-size: 15px;
            font-weight: 600;
            color: #00A8A8 !important;
            line-height: 18px;
        }

        .address_phone {
            font-size: 12px;
            line-height: 14px;
        }

        /* Move burger + cart to right */
        .header-action-right,
        .burger-icon {
            position: relative;
            z-index: 20;
        }
    }

    .address-box {
        width: 100%;
    }


    .address-icon {
        color: #00A8A8;
        margin-left: 6px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .address_phone {
            max-width: 70%;

        }
    }

</style>
<style>
    .modal-bottom .modal-dialog {
        position: fixed;
        bottom: 0;
        margin: 0;
        width: 100%;
        max-height: 90%;
    }

    .modal-bottom .modal-content {
        border-radius: 15px 15px 0 0;
    }

    .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }


</style>
<style>
    .otp-box {
        width: 50px;
        height: 45px;
        font-size: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .otp-box:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
    }
</style>
<style>
    .ticker {
        position: relative;
        width: 100%;
        overflow: hidden;
        height: 20px; /* adjust height */
        display: flex;
        align-items: center;
    }

    .ticker-wrap {
        display: inline-block;
        white-space: nowrap;
        animation: ticker 15s linear infinite;
    }

    .ticker-item {
        display: inline-block;
        padding: 0 50px; /* space between items */
        font-size: 14px;
        color: #fff;
    }

    @keyframes ticker {
        0% {
            transform: translateX(100%);
        }
        100% {
            transform: translateX(-100%);
        }
    }

    .categories-dropdown-active-large {
        min-width: 400px !important;
    }

</style>

<style>
    /* Explore button style */
    .categories-button-active {
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #333;
        background: red;
        padding: 10px 20px;
        border-radius: 5px;
    }

    /* Parent wrapper must not clip dropdown */
    .main-categori-wrap {
        position: static !important;
    }

    /* Mega dropdown */
    .mega-dropdown {
        position: absolute;
        top: 100%; /* parent ke niche aligned */
        left: 50%; /* horizontal center */
        transform: translateX(-50%); /* center properly */
        width: 100%; /* container ke width ke hisaab se */
        background: #fff;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        padding: 20px;
        display: none; /* show on hover */
        z-index: 9999;
        transition: all 0.3s ease;
        border-radius: 5px;
    }

    /* Show dropdown on hover */
    .main-categori-wrap:hover .mega-dropdown {
        display: block;
    }

    /* Left + right structure */
    .mega-parents .nav-link {
        color: #333;
        font-weight: 500;
        padding: 10px 15px;
        transition: all 0.3s;
        border-radius: 4px;
    }

    .mega-parents .nav-link:hover,
    .mega-parents .nav-link.active {
        background: #f7f7f7;
        color: #ff6600;
    }

    .mega-children .child-panel {
        display: none;
        animation: fadeIn 0.3s ease forwards;
    }

    .mega-children .child-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mega-children h5 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .mega-children ul {
        list-style: none;
        padding: 0;
    }

    .mega-children ul li {
        padding: 5px 0;
        color: #555;
    }

    .mega-children ul li:hover {
        color: #ff6600;
        cursor: pointer;
    }
</style>


<header class="header-area header-style-1 header-height-2">
    {{--    <div class="mobile-promotion">--}}
    {{--        <span>Grand opening, <strong>up to 15%</strong> off all items. Only <strong>3 days</strong> left</span>--}}
    {{--    </div>--}}
    <div class="header-top header-top-ptb-1 d-none d-lg-block"
         style="background-color: #00A8A8;color: #fff; overflow:hidden;">
        <div class="container">
            <div class="ticker">
                <div class="ticker-wrap">
                    <div class="ticker-item">Shopping First Time, Get 5% Off: WELCOME05</div>
                    <div class="ticker-item">Free Shipping on Orders Above ₹999</div>
                    <div class="ticker-item">24/7 Customer Support Available</div>
                    <div class="ticker-item">Sign Up & Get Extra Rewards</div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addressSearchModal" tabindex="-1" aria-labelledby="locationModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Select your Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input id="locationInput" type="text" class="form-control mb-2"
                           placeholder="Search delivery location">

                    <button class="btn btn-light w-100 mb-2" onclick="getCurrentLocation()"><i
                            class="fi-rs-crosshairs"></i> Use current location
                    </button>
                    <div class="saved-address">
                        @if(!empty($user))
                            @foreach($user->addresses as $add)

                                @php
                                    $fulladdress = $add->flat_no . ', ' .
                                                   $add->building_name . ', ' .
                                                   $add->landmark . ', ' .
                                                   $add->location;
                                @endphp



                                <div class="address-card address-item mb-2 p-2 border rounded"
                                     data-id="{{ $add->id }}"
                                     data-fulladdress="{{ $fulladdress }}"
                                     data-lat="{{ $add->latitude }}"
                                     data-lng="{{ $add->longitude }}"
                                     data-pincode="{{ $add->pincode }}"
                                     style="cursor:pointer;">

                                    <strong>{{ $add->address_type }}</strong>
                                    <p class="mb-0">
                                        {{ $add->flat_no }}, {{ $add->building_name }},
                                        {{ $add->landmark }}, {{ $add->location }}
                                    </p>

                                </div>

                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
        <div class="container">
            <div class="header-wrap">
                <div class="logo logo-width-1">
                    <a href='{{url('/')}}'><img src="{{url('public/assets/nc_partner/NutraCoreLogoMain.svg')}}" alt="logo"/></a>
                </div>
                <div class="header-right">
                    <div class="search-style-2">
                        <form action="{{route('search')}}">
                            <select class="select-active" id="categorySelect">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name ?? '' }}</option>
                                @endforeach
                            </select>
                            <input type="text" id="productSearch" name="search" placeholder="Search for items..."/>
                            <div id="suggestionBox" class="list-group position-absolute"
                                 style="z-index: 1000; width: 100%; display:none;margin-top: 51px;"></div>
                        </form>

                    </div>
                    <div class="header-action-right">
                        <div class="header-action-2">
                            <div class="search-location">
                                <div class="header-box" data-bs-toggle="modal" data-bs-target="#addressSearchModal">
                                        <span class="address-text" id="address_text">
                                            {{ $address }}
                                        </span>
                                    <span class="down-arrow">▼</span>
                                </div>
                            </div>

                            <!-- <div class="header-action-icon-2">
                                    <a href='shop-wishlist.html'>
                                        <img class="svgInject" alt="Nest"
                                            src="{{url('public/assets')}}/imgs/theme/icons/icon-heart.svg" />
                                        <span class="pro-count blue">0</span>
                                    </a>
                                    <a href='shop-wishlist.html'><span class="lable">Wishlist</span></a>
                                </div> -->
                            <div class="header-action-icon-2">
                                @if(!empty($user))
                                    <a class='mini-cart-icon' href='{{url('cart')}}'>
                                        <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg"/>
                                        <span class="pro-count blue" id="cart_qty">{{ $total_qty }}</span>
                                    </a>
                                    <a href='{{url('cart')}}'><span class="lable">Cart</span></a>
                                @else
                                    <a class='mini-cart-icon' onclick="checkLogin()">
                                        <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg"/>
                                        <span class="pro-count blue" id="cart_qty">{{ $total_qty }}</span>
                                    </a>
                                    <a onclick="checkLogin()"><span class="lable">Cart</span></a>
                                @endif
                            </div>
                            <div class="header-action-icon-2">
                                <a onclick="checkLogin()">
                                    <img class="svgInject" alt="Nest"
                                         src="{{url('public/assets')}}/imgs/theme/icons/icon-user.svg"/>
                                </a>
                                <a onclick="checkLogin()"><span class="lable ml-0">Account</span></a>
                                @if(!empty($user))
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                        <ul>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{url('profile')}}')"><i
                                                        class="fi fi-rs-user mr-10"></i>My
                                                    Account</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('wishlist')}}')"><i
                                                        class="fi fi-rs-heart mr-10"></i>Wishlist</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('address')}}')"><i
                                                        class="fi fi-rs-location-alt mr-10"></i>Address Details</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('my_orders')}}')"><i
                                                        class="fi fi-rs-label mr-10"></i>My
                                                    Orders</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('suppliment_recommendation')}}')"><i
                                                        class="fi fi-rs-heart mr-10"></i>My
                                                    Supplement Recommendation</a>
                                            </li>
                                            <li>
                                                <a href="{{url('nc_consult')}}"><i
                                                        class="fi fi-rs-heart mr-10"></i>NC Consult</a>
                                            </li>
                                            <li>
                                                <a href='{{route('nutrapass')}}'><i
                                                        class="fi fi-rs-settings-sliders mr-10"></i>NutraPass
                                                    Membership</a>
                                            </li>
                                            <li>
                                                <a href='{{route('nc_cash')}}'><i
                                                        class="fi fi-rs-settings-sliders mr-10"></i>NC Cash</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('giftcard')}}')"><i
                                                        class="fi fi-rs-settings-sliders mr-10"></i>GiftCard</a>
                                            </li>
                                            <li>
                                                <a onclick="checkLoginRedirect('{{route('refer_earn')}}')"><i
                                                        class="fi fi-rs-settings-sliders mr-10"></i>Refer & Earn</a>
                                            </li>
                                            <li>
                                                <a href='{{route('coupons')}}'><i class="fi fi-rs-sign-out mr-10"></i>PromoCodes</a>
                                            </li>
                                            <li>
                                                <a href='https://api.whatsapp.com/send?phone=919959503035'><i
                                                        class="fi fi-rs-sign-out mr-10"></i>Need Help</a>
                                            </li>
                                            <li>
                                                <a href='{{url('logout')}}'><i class="fi fi-rs-sign-out mr-10"></i>Logout</a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom header-bottom-bg-color sticky-bar">
        <div class="container">
            <div class="header-wrap header-space-between position-relative">
                <div class="logo logo-width-1 d-block d-lg-none">

                    <div class="d-flex align-items-center address-box"
                         data-bs-toggle="modal" data-bs-target="#addressSearchModal">

    <span class="address_phone" id="address_phone">
        {{$address ?? ''}}
    </span>

                        <i class="fi-rs-angle-down address-icon"></i>
                    </div>


                </div>
                <div class="header-nav d-none d-lg-flex">
                    <div class="main-categori-wrap d-none d-lg-block">
                        <!-- Navbar Section -->
                        <div class="header-nav d-none d-lg-flex">
                            <div class="main-categori-wrap d-none d-lg-block">
                                <!-- Explore button -->
                                <a class="categories-button-active" href="#">
                                    <span class="fi-rs-apps"></span> <span class="et">Explore</span>
                                    <i class="fi-rs-angle-down"></i>
                                </a>

                                <div style="display: flex; justify-content: center; align-items: flex-start; ">
                                    <!-- Mega Dropdown -->
                                    <div class="mega-dropdown container" id="megaDropdown" style:background:red;>
                                        <div class="row">
                                            <!-- Left: parent list -->
                                            <div class="col-lg-3 mega-parents">
                                                <nav class="nav flex-column">
                                                    <a class="nav-link active" href="#" data-target="panel-electronics">Category</a>
                                                    <a class="nav-link" href="#" data-target="panel-fashion">Brands</a>
                                                    <a class="nav-link" href="#" data-target="panel-home">By Goals</a>
                                                </nav>
                                            </div>

                                            <!-- Right: dynamic child content -->
                                            <div class="col-lg-9 mega-children"
                                                 style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
                                                <div id="panel-electronics" class="child-panel active">
                                                    <h5>Category</h5>
                                                    <div class="row">
                                                        @foreach($categories as $category)
                                                            <div class="col-md-2 mb-4 me-3">
                                                                <!-- mb-4 = bottom margin, me-3 = right margin -->
                                                                <div class="card text-center"
                                                                     style="background: #DEFFFF; border: 1px solid #ccc;">
                                                                    <a href="{{ url('collections/' . $category->slug) }}"
                                                                       style="padding: 10px; padding-bottom:0px;">
                                                                        <img
                                                                            src="{{ CustomHelper::getImageUrl("categories",$category->image) ?? '' }}"
                                                                            alt="{{ $category->name ?? '' }}"
                                                                            class="card-img-top"
                                                                            style="height: 100%; object-fit: cover; width: 100%;">
                                                                    </a>
                                                                    <div class="card-body p-2">
                                                                        <h5 class="card-title mb-0"
                                                                            style="font-size:15px;">
                                                                            <a href="{{ url('collections/' . $category->slug) }}"
                                                                               class="text-dark text-decoration-none">
                                                                                {{ $category->name ?? '' }}
                                                                            </a>
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div id="panel-fashion" class="child-panel">
                                                    <h5>Brands</h5>
                                                    <div class="row">
                                                        @foreach($brands as $brand)
                                                            <div class="col-md-2">
                                                                <div class="border-1 text-center">
                                                                    <figure>
                                                                        <a href="{{ url('collections/' . $brand->slug) }}">
                                                                            <img src="{{ $brand->brand_img }}" alt=""
                                                                                 style="height:100px;"/>
                                                                        </a>
                                                                    </figure>
                                                                    <h4>{{ $brand->brand_name ?? '' }}</h4>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div id="panel-home" class="child-panel">
                                                    <h5>By Goals</h5>
                                                    <div class="row">
                                                        @foreach($goal_category as $category)
                                                            <div class="col-md-2 mb-3">
                                                                <div class="border text-center p-0"
                                                                     style="background: #DEFFFF;overflow:hidden;">
                                                                    <a href="{{ url('collections/' . $category->slug) }}">
                                                                        <img src="{{ $category->image ?? '' }}"
                                                                             alt="{{ $category->name ?? '' }}"
                                                                             style="width: 100%; height: 100%; object-fit: cover; display: block;"/>
                                                                    </a>
                                                                    <h4>
                                                                        <a href="{{ url('collections/' . $category->slug) }}"
                                                                           style="font-size:15px;colar:black;">{{ $category->name ?? '' }}</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Mega Dropdown -->
                                </div>
                            </div>
                        </div>


                        <!-- JS to handle hover child panels -->
                        <script>
                            const parentLinks = document.querySelectorAll(".mega-parents .nav-link");
                            const childPanels = document.querySelectorAll(".child-panel");

                            parentLinks.forEach(link => {
                                link.addEventListener("mouseenter", () => {
                                    // Remove active class from all
                                    parentLinks.forEach(l => l.classList.remove("active"));
                                    childPanels.forEach(p => p.classList.remove("active"));

                                    // Activate the current one
                                    link.classList.add("active");
                                    const target = link.getAttribute("data-target");
                                    document.getElementById(target).classList.add("active");
                                });
                            });
                        </script>


                    </div>
                    <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                        <nav>


                            <ul>
                                <li>
                                    <a href="{{ url('/') }}" class="menu-link">
                                        <i class="fa fa-home"></i> Home
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('brands') }}" class="menu-link">
                                        <i class="fa fa-tags"></i> Brands
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('coupons') }}" class="menu-link">
                                        <i class="fa fa-gift"></i> Offers
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('nutrapass') }}" class="menu-link">
                                        <i class="fa fa-id-card"></i> Nutrapass
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('nc_cash') }}" class="menu-link">
                                        <i class="	fa fa-credit-card"></i> NC Cash
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('stores') }}" class="menu-link">
                                        <i class="fa fa-shopping-bag"></i> Store Locator
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ url('nc_consult') }}" class="menu-link">
                                        <i class="fa fa-info-circle"></i> NC Consult
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('contact') }}" class="menu-link">
                                        <i class="fa fa-envelope"></i> Contact
                                    </a>
                                </li>


                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="hotline d-none d-lg-flex">
                    <img src="{{url('public/assets')}}/imgs/theme/icons/icon-headphone.svg" alt="hotline"/>
                    <p>+91 88850 65550<span>10:00 - 18:00, Mon - Sat</span></p>
                </div>
                <div class="header-action-icon-2 d-block d-lg-none">
                    <div class="burger-icon burger-icon-white">
                        <span class="burger-icon-top"></span>
                        <span class="burger-icon-mid"></span>
                        <span class="burger-icon-bottom"></span>
                    </div>
                </div>
                <div class="header-action-right d-block d-lg-none">
                    <div class="header-action-2">
                        <div class="header-action-icon-2">
                            @if(!empty($user))
                                <a href='{{ url('wishlist') }}'>
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-heart.svg"/>
                                    <span class="pro-count white">0</span>
                                </a>
                            @else
                                <a onclick="checkLogin()">
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-heart.svg"/>
                                    <span class="pro-count white"></span>
                                </a>
                            @endif
                        </div>
                        <div class="header-action-icon-2">
                            @if(!empty($user))
                                <a class="mini-cart-icon" href="{{ url('cart') }}">
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg"/>
                                    <span class="pro-count white" id="cart_qty_phone2">{{ $total_qty }}</span>
                                </a>
                            @else
                                <a class="mini-cart-icon" onclick="checkLogin()">
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg"/>
                                    <span class="pro-count white" id="cart_qty_phone1">{{ $total_qty }}</span>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href='{{url('/')}}'><img src="{{url('public/assets/nc_partner/NutraCoreLogoMain.svg')}}" alt="logo"/></a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">

            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    <ul class="mobile-menu font-heading">
                        @if(!empty($user))
                            <div class="row">
                                <div class="profile-card">
                                    <div class="avatar"><img src="{{CustomHelper::getImageUrl('users',$user->image)}}">
                                    </div>
                                    <div class="profile-info">
                                        <h3>{{$user->name??''}}</h3>
                                        <p>{{$user->email??''}}</p>
                                    </div>
                                    <a href="{{route('profile')}}">
                                        <div class="edit-icon">✏️</div>
                                    </a>
                                </div>

                            </div>
                            <div class="row mt-5">
                                <div class="membership-card">
                                    <a href="">
                                        <div class="card-section">
                                            <div class="icon">
                                                <img src="{{url('public/assets/member.svg')}}" alt="Gold Icon">
                                            </div>
                                            <div class="text">
                                                <div class="title">{{$active_loyality->title??''}}</div>
                                                <div class="subtitle">Membership <br>
                                                    till {{date('d M Y',strtotime($user->subscription_end??''))}}</div>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="divider"></div>
                                    <a href="{{route('nc_cash')}}">
                                        <div class="card-section">
                                            <div class="icon">
                                                <img src="{{url('public/assets/coin.svg')}}" alt="Points Icon">
                                            </div>
                                            <div class="text">
                                                <div class="title">{{$user->cashback_wallet??0}}</div>
                                                <div class="subtitle">NC Points</div>
                                            </div>

                                        </div>
                                    </a>
                                </div>


                            </div>
                        @else
                            <div class="row">
                                <button onclick="checkLogin()" class="btn btn-primary">Login</button>

                            </div>
                        @endif

                        <div class="menu mt-10">
                            <div class="menu-item">
                                <a onclick="checkLoginRedirect('{{route('address')}}')"><span>Address Detail</span></a>
                            </div>
                            <div class="menu-item">
                                <a onclick="checkLoginRedirect('{{route('wishlist')}}')"><span>Wishlist</span></a>
                            </div>
                            <div class="menu-item">
                                <a onclick="checkLoginRedirect('{{route('my_orders')}}')"><span> My Orders</span></a>

                            </div>
                            <div class="menu-item">
                                <a onclick="checkLoginRedirect('{{route('suppliment_recommendation')}}')"><span> My Supplement Recommendation</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{url('nc_consult')}}"><span> NC Consult</span></a>

                            </div>
                        </div>

                        <div class="menu mt-10">
                            <div class="menu-item">
                                <a href="{{route('nutrapass')}}"><span>NutraPass Membership</span></a>
                            </div>
                            <div class="menu-item">
                                <a href="{{route('nc_cash')}}"><span> NC Cash</span></a>

                            </div>
                            <div class="menu-item">
                                <a onclick="checkLoginRedirect('{{route('giftcard')}}')"><span> GiftCard</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{route('refer_earn')}}"> <span> Refer & Earn</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{route('coupons')}}"> <span>PromoCodes</span></a>
                            </div>
                        </div>

                        <div class="menu mt-10">
                            <div class="menu-item">
                                <a href="https://api.whatsapp.com/send?phone=919959503035"><span>Need Help? Let's Chat</span></a>
                            </div>
                            <div class="menu-item">
                                <a href="{{route('privacy_policy')}}"><span>Privacy Policy</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{route('terms')}}"><span> Terms of Service</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{route('return_policy')}}"> <span>Return & Refund Policy</span></a>

                            </div>
                            <div class="menu-item">
                                <a href="{{route('shipping_policy')}}"> <span>Shipping & Delivery Policy</span></a>

                            </div>
                        </div>
                        <div class="menu mt-10">
                            <div class="menu-item">
                                <a href="{{url('logout')}}"><span>Logout</span></a>
                            </div>
                        </div>

                    </ul>
                </nav>
                <!-- mobile menu end -->
            </div>

            <div class="site-copyright">Copyright {{date('Y')}} © Nutracore. All rights reserved. Powered by
                Nutracore.
            </div>
        </div>
    </div>
</div>
<!--End header-->
@yield('content')
<nav class="mobile-bottom-nav">
    <a href="{{url('/')}}" class="nav-item">
        <img src="{{url('public/assets/home/home.png')}}" alt="Home">
        <span>Home</span>
    </a>
    <a href="{{url('/explore')}}" class="nav-item">
        <img src="{{url('public/assets/home/explore.png')}}" alt="Explore">
        <span>Explore</span>
    </a>
    <a href="{{url('/coupons')}}" class="nav-item">
        <img src="{{url('public/assets/home/offers.png')}}" alt="Offers">
        <span>Offers</span>
    </a>
    <a href="{{url('/stores')}}" class="nav-item">
        <img src="{{url('public/assets/home/store.png')}}" alt="Stores">
        <span>Stores</span>
    </a>
    <a onclick="checkLoginRedirect('{{url('profile')}}')" class="nav-item">
        <img src="{{url('public/assets/home/profile.png')}}" alt="Profile">
        <span>Profile</span>
    </a>
</nav>
<style>
    footer.main * {
        color: white !important;
    }
</style>
@php
    $currentRoute = Route::currentRouteName();
@endphp
@if($currentRoute != 'cart')
    <footer class="main" style="background-color: #0f5759;color: white">

        <section class="section-padding footer-mid">
            <div class="container pt-15 pb-20">
                <div class="row">
                    <div class="col">
                        <div
                            class="widget-about font-md mb-md-3 mb-lg-3 mb-xl-0 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0">
                            <div class="logo" style="margin:0px;width: 85%;">
                                <img src="{{url('public/assets/nc_partner/NutraCoreLogoFooter.svg')}}"
                                     alt="logo"/>

                            </div>
                            <ul class="contact-infor ">

                                <li class="d-flex"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-contact.svg"
                                                        alt=""/><span>(+91) 88850 65550</span></li>
                                <li class="d-flex"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-email-2.svg"
                                                        alt=""/><span>support@nutracore.in</span></li>
                                <li class="d-flex"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-clock.svg"
                                                        alt=""/><span>10:00 - 18:00, Mon - Sat</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                        <h4 class=" widget-title
                ">Company</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            <li><a href="{{url('about')}}">About Us</a></li>
                            <li><a href="{{url('privacy_policy')}}">Privacy Policy</a></li>
                            <li><a href="{{url('terms')}}">Terms &amp; Conditions</a></li>
                            <li><a href="{{url('contact')}}">Contact Us</a></li>
                            <li><a href="{{url('return_policy')}}"> Refund & Cancellation policy</a></li>
                            <li><a href="{{url('nc_partner')}}">Becone a NC Partner </a></li>
                        </ul>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                        <h4 class="widget-title">Popular</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            @foreach ($allcategories->where('is_popular',1)->take(5) as $category)
                                <li><a href="{{ url('collections/' . $category->slug) }}">{{$category->name??''}}</a>
                                </li>
                            @endforeach

                        </ul>
                    </div>

                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                        <h4 class="widget-title">Brands</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            @foreach ($brands->where('is_popular',1)->take(5) as $brand)
                                <li><a href="{{ url('collections/' . $brand->slug) }}">{{$brand->brand_name??''}}</a>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                    <div class="footer-link-widget widget-install-app col wow animate__animated animate__fadeInUp"
                         data-wow-delay=".5s">
                        <h4 class="widget-title">Install App</h4>
                        <p class="">From App Store or Google Play</p>
                        <div class="download-app">
                            <a target="_blank" href="https://apps.apple.com/in/app/nutracore/id6749866050"
                               class="hover-up mb-sm-2 mb-lg-0"><img class="active"
                                                                     src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
                                                                     alt=""/></a>
                            <a target="_blank"
                               href="https://play.google.com/store/apps/details?id=com.nutracore&hl=en_IN"
                               class="hover-up mb-sm-2"><img
                                    src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                                    alt=""/></a>
                        </div>
                        <p class="mb-20">Secured Payment Gateways</p>
                        <img class="" src="{{url('public/assets')}}/imgs/theme/payment-method.png" alt=""/>
                    </div>
                </div>
            </div>
        </section>
        <div class="container pb-30 wow animate__animated animate__fadeInUp" data-wow-delay="0">
            <div class="row align-items-center">
                <div class="col-12 mb-30">
                    <div class="footer-bottom"></div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <p class="font-sm mb-0">&copy; {{date('Y')}}, <strong class="text-brand">Nutracore</strong>All
                        rights reserved</p>
                </div>
                <div class="col-xl-4 col-lg-6 text-center d-none d-xl-block">

                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 text-end d-none d-md-block">
                    <div class="mobile-social-icon">
                        <h6>Follow Us</h6>
                        <a href="https://www.facebook.com/nutracore.in" target="_blank"><img
                                src="{{url('public/assets')}}/imgs/theme/icons/icon-facebook-white.svg"
                                alt=""/></a>

                        <a href="https://www.instagram.com/nutracore.in/" target="_blank"><img
                                src="{{url('public/assets')}}/imgs/theme/icons/icon-instagram-white.svg"
                                alt=""/></a>
                        <a href="https://www.youtube.com/@NutraCoreOfficial" target="_blank"><img
                                src="{{url('public/assets')}}/imgs/theme/icons/icon-youtube-white.svg"
                                alt=""/></a>
                    </div>
                    {{--                <p class="font-sm">Up to 15% discount on your first subscribe</p>--}}
                </div>
            </div>
        </div>
    </footer>
@endif
@php
    $user = Auth::user();
    $subscription = CustomHelper::subscriptionsData($user);

@endphp
<div class="popup-overlay" id="membershipPopup" style="display: none">
    <div class="popup-box">

        <div class="popup-header">
            <span class="close-btn" onclick="closePopup()">✕</span>
            <h3 style="color: white;font-size: 20px">Join NutraPass Membership</h3>
        </div>

        <img src="{{url('public/assets/images/nutrapasslogo.svg')}}" class="logo"/>

        <div class="benefits-card">
            <ul id="subscription_html">

            </ul>
        </div>

        <div class="plans">
            @foreach($subscription['subscription_plans'] as $plan)
                @php
                    $permonth = round((int)$plan->price / (int)$plan->duration);
                    $totalStandardPrice = (int)$plan->price * (int)$plan->duration;
                    $savePercent = $totalStandardPrice > 0
                        ? round((($totalStandardPrice - $plan->price) / $totalStandardPrice) * 100)
                        : 0;
                @endphp

                <div onclick="selectPlan('{{ $plan->id }}','{{ $plan->duration }}','{!! $plan->terms  !!}')"
                     class="plan-item"
                     id="plan{{ $plan->duration }}"
                     data-duration="{{ $plan->duration }}"
                     data-id="{{ $plan->id }}"
                     data-terms="{{ htmlentities($plan->terms) }}"
                     data-price="{{ $plan->price }}">
                    @if($plan->is_best_value == 1)
                        <div class="best-value-tag">Best Value</div>
                    @endif

                    <h2>{{ $plan->duration }}</h2>
                    <p>months</p>
                    <span class="price">₹{{ $permonth }}/mo</span>
                    <small>SAVE {{ $savePercent }}%</small>
                    <h4 class="total">₹{{ $plan->price }}</h4>
                </div>
            @endforeach
        </div>


        <button class="subscribe-btn" onclick="subscribeNow()">Subscribe</button>

    </div>
</div>
<style>
    .popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popup-box {
        width: 90%;
        max-width: 380px;
        background: #17b4ad;
        border-radius: 20px;
        padding: 20px;
        color: white;
        text-align: center;
    }

    .close-btn {
        font-size: 22px;
        cursor: pointer;
        float: left;
    }

    .logo {
        width: 160px;
        margin: 20px auto;
    }

    .benefits-card {
        background: #ffcc5c;
        padding: 12px;
        border-radius: 15px;
        color: #000;
    }

    .benefits-card ul {
        padding: 0;
        list-style: none;
    }

    .benefits-card li {
        font-size: 15px;
        margin: 5px 0;
    }

    .plans {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }

    .plan-item {
        background: white;
        width: 30%;
        color: #444;
        padding: 15px 8px;
        border-radius: 15px;
        cursor: pointer;
        transition: 0.3s;
        position: relative;
    }

    .plan-item.active {
        border: 3px solid #ffca28;
        transform: scale(1.05);
    }

    .plan-item h2 {
        margin: 0;
        font-size: 28px;
        color: #333;
    }

    .plan-item .price {
        display: block;
        margin-top: 5px;
        font-weight: 600;
        color: #000;
    }

    .plan-item small {
        color: green;
    }

    .plan-item .total {
        margin-top: 8px;
        font-weight: bold;
    }

    .best-value {
        background: #ffc033;
    }

    .best-value-tag {
        position: absolute;
        top: -10px;
        right: 0;
        background: #07a0f5;
        color: white;
        padding: 2px 10px;
        border-radius: 5px;
        font-size: 12px;
    }

    .subscribe-btn {
        width: 100%;
        background: white;
        padding: 15px;
        border-radius: 25px;
        color: #17b4ad;
        font-weight: 600;
        font-size: 18px;
        margin-top: 25px;
        border: none;
        cursor: pointer;
    }
</style>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>

    @php
        $lastPlan = end($subscription['subscription_plans']);

    @endphp
    function openSubscriptionPopup() {
        document.getElementById("membershipPopup").style.display = "flex";
    }

    function closePopup() {
        document.getElementById("membershipPopup").style.display = "none";
    }

    let selectedPlanId = null;

    $(document).ready(function () {

        // pick last plan-item element in DOM
        var $last = $('.plan-item').last();

        if ($last.length) {
            // read attributes
            var months = $last.data('duration');
            var id = $last.data('id');
            // decode terms html if needed
            var terms = $last.data('terms') || '';
            // If terms was HTML-encoded by htmlentities, decode it:
            terms = $('<textarea/>').html(terms).text();

            selectPlan(id, months, terms);
        }
    });

    function selectPlan(id, months, terms) {
        selectedPlan = months;
        selectedPlanId = id;
        if (terms) {
            $('#subscription_html').html(terms);
            $('#subscription_html1').html(terms);
        }

        $(".plan-item").removeClass("active");
        $("#plan" + months).addClass("active");
    }

    function subscribeNow() {
        if (!selectedPlanId) {
            alert("Please select a plan first");
            return;
        }
        var user_id = '{{$user->id??''}}';
        if (user_id == "") {
            checkLogin();
            return;
        }
        $.ajax({
            url: "{{ url('take_subscription') }}",
            type: "POST",
            data: {
                subscription_id: selectedPlanId,
                _token: "{{ csrf_token() }}"
            },
            success: function (res) {

                if (!res.result) {
                    alert(res.message);
                    return;
                }

                // ---- OPEN RAZORPAY POPUP ----
                var options = {
                    "key": res.keys.key,
                    "currency": "INR",
                    "order_id": res.order_id,
                    "handler": function (response) {
                        alert('Payment Sucessfull');
                    },

                    "prefill": {
                        "name": "{{ Auth::user()->name??'' }}",
                        "email": "{{ Auth::user()->email ??''}}",
                        "contact": "{{ Auth::user()->phone ??''}}"
                    },
                };

                var rzp = new Razorpay(options);
                rzp.open();
            },

            error: function (err) {
                console.log(err);
                alert("Something went wrong!");
            }
        });
    }

</script>


<!-- Preloader Start -->
<!-- <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="text-center">
                    <img src="{{url('public/assets')}}/imgs/theme/loading.gif" alt="" />
                </div>
            </div>
        </div>
    </div> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<!-- Vendor JS-->
<script src="{{url('public/assets')}}/js/vendor/modernizr-3.6.0.min.js"></script>
<script src="{{url('public/assets')}}/js/vendor/jquery-3.6.0.min.js"></script>
<script src="{{url('public/assets')}}/js/vendor/jquery-migrate-3.3.0.min.js"></script>
<script src="{{url('public/assets')}}/js/vendor/bootstrap.bundle.min.js"></script>
<script src="{{url('public/assets')}}/js/plugins/slick.js"></script>
<script src="{{url('public/assets')}}/js/plugins/jquery.syotimer.min.js"></script>
<script src="{{url('public/assets')}}/js/plugins/waypoints.js"></script>
<script src="{{url('public/assets')}}/js/plugins/wow.js"></script>
<script src="{{url('public/assets')}}/js/plugins/perfect-scrollbar.js"></script>
<script src="{{url('public/assets')}}/js/plugins/magnific-popup.js"></script>
<script src="{{url('public/assets')}}/js/plugins/select2.min.js"></script>
<script src="{{url('public/assets')}}/js/plugins/counterup.js"></script>
<script src="{{url('public/assets')}}/js/plugins/jquery.countdown.min.js"></script>
<script src="{{url('public/assets')}}/js/plugins/images-loaded.js"></script>
<script src="{{url('public/assets')}}/js/plugins/isotope.js"></script>
<script src="{{url('public/assets')}}/js/plugins/scrollup.js"></script>
<script src="{{url('public/assets')}}/js/plugins/jquery.vticker-min.js"></script>
<script src="{{url('public/assets')}}/js/plugins/jquery.theia.sticky.js"></script>
<script src="{{url('public/assets')}}/js/plugins/jquery.elevatezoom.js"></script>
<!-- Template  JS -->
<script src="{{url('public/assets')}}/js/main2cc5.js?v=5.6"></script>
<script src="{{url('public/assets')}}/js/shop2cc5.js?v=5.6"></script>
</body>


<!-- Mirrored from nest-frontend.netlify.app/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 08:00:04 GMT -->
<style>
    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }
</style>

<div class="modal fade" id="otpLoginModal" tabindex="-1" aria-labelledby="otpLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login with OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="otpLoginForm">
                    @csrf
                    <!-- Mobile Number -->
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile no"
                               required>
                    </div>
                    <div class="mb-3">
                        <label style="font-size:14px;">
                            <input type="checkbox" id="termsCheckbox" style="margin-right:6px;" checked="checked">
                            By continuing, I agree to the <a href="{{url('terms')}}" target="_blank">Terms of use</a> &
                            <a href="{{url('privacy_policy')}}" target="_blank">Privacy Policy</a>
                        </label>
                    </div>


                    <!-- Send OTP Button -->
                    <div class="d-grid mb-3">
                        <button type="button" id="sendOtpBtn" class="btn btn-primary">Send OTP</button>
                    </div>
                    <input type="hidden" id="otp" name="otp">

                    <!-- OTP Field (Box Style) -->
                    <div id="otpSection" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Enter OTP</label>
                            <div class="d-flex gap-2 ">
                                @for ($i = 1; $i <= 4; $i++)
                                    <input type="text" maxlength="1" class="form-control text-center otp-box"
                                           id="otp-{{ $i }}" inputmode="numeric" autocomplete="one-time-code">
                                @endfor
                            </div>
                        </div>

                        <!-- Timer and Resend -->
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <small id="timer">01:00</small>
                            <button type="button" id="resendOtpBtn" class="btn btn-link p-0" disabled>Resend
                                OTP
                            </button>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script>
    function checkLogin() {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            closePopup();
            $('.mobile-header-active').removeClass('sidebar-visible');
            $('body').removeClass('mobile-menu-active');

            $('#otpLoginModal').modal('show');

        }
    }


    function addToCart() {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
        }
        var _token = '{{ csrf_token() }}';
        var product_id = $('#selectedProductID').val();
        var variant_id = $('#selectedVarientID').val();
        if (variant_id == '') {
            variant_id = 0;
        }
        var qty = $('#quantity').val();
        $.ajax({
            url: "{{ url('addToCart') }}",
            type: "POST",
            data: {product_id: product_id, variant_id: variant_id, qty: qty},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#cart_qty').html(resp.total_qty);
                $('#cart_qty_phone1').html(resp.total_qty);
                $('#cart_qty_phone2').html(resp.total_qty);
                showToast(resp.message);
            }
        });
    }

    function wishlist_save(product_id, variant_id, remove_cart = false) {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
        }
        var _token = '{{ csrf_token() }}';
        if (variant_id == '') {
            variant_id = 0;
        }
        var qty = $('#quantity').val();
        $.ajax({
            url: "{{ url('wishlist_save') }}",
            type: "POST",
            data: {product_id: product_id, variant_id: variant_id},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                let icon = $("#wishlist_icon_" + variant_id);

                if (remove_cart == true || remove_cart == "true") {
                    DeleteCart(product_id, variant_id);
                }
                // If added to wishlist
                if (resp.status == "added") {
                    icon.addClass("active");
                }
                // If removed from wishlist
                else if (resp.status == "removed") {
                    icon.removeClass("active");
                }

                var currentRoute = @json(Route::currentRouteName()); // e.g. "wishlist"

                if (currentRoute === 'wishlist') {
                    location.reload();
                }
            }


        });
    }

    function DeleteCart(product_id, variant_id) {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
        }
        var _token = '{{ csrf_token() }}';
        if (variant_id == '') {
            variant_id = 0;
        }
        var qty = 0;
        $.ajax({
            url: "{{ url('addToCart') }}",
            type: "POST",
            data: {product_id: product_id, variant_id: variant_id, qty: qty},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#cart_qty').html(resp.total_qty);
                $('#cart_qty_phone1').html(resp.total_qty);
                $('#cart_qty_phone2').html(resp.total_qty);
                showToast(resp.message);
                getCartHtml();
            }
        });
    }


    function updateCart(product_id, variant_id, type) {

        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
        }
        var _token = '{{ csrf_token() }}';
        var qty = $('#cart_quantity' + variant_id).val();
        if (isNaN(qty)) {
            qty = document.getElementById(`quantity-${product_id}-${variant_id}`).value;
        }
        if (type == 'minus') {
            qty = parseInt(qty) - 1;
        } else {
            qty = parseInt(qty) + 1;
        }
        if (qty <= 0) {
            qty = 1;
        }
        $.ajax({
            url: "{{ url('addToCart') }}",
            type: "POST",
            data: {product_id: product_id, variant_id: variant_id, qty: qty},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#cart_quantity' + variant_id).val(qty);
                $('#cart_qty').html(resp.total_qty ?? 0);
                $('#cart_qty_phone1').html(resp.total_qty ?? 0);
                $('#cart_qty_phone2').html(resp.total_qty ?? 0);
                showToast(resp.message);
                getCartHtml();
            }
        });
    }

    function getCartQty() {
        var _token = '{{ csrf_token() }}';
        var product_id = $('#selectedProductID').val();
        var variant_id = $('#selectedVarientID').val();
        $.ajax({
            url: "{{ url('getCartQty') }}",
            type: "POST",
            data: {product_id: product_id, variant_id: variant_id},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#quantity').val(resp.qty);
                $('#cart_qty').html(resp.total_qty);
            }
        });
    }

    {{--async function getCartHtml() {--}}

    {{--    var user_id = '{{ $user->id ?? "" }}';--}}
    {{--    if (user_id == '') {--}}
    {{--        $('#otpLoginModal').modal('show');--}}
    {{--        return null;--}}
    {{--    }--}}

    {{--    var _token = '{{ csrf_token() }}';--}}

    {{--    // ✅ return AJAX so async/await receives the response--}}
    {{--    return $.ajax({--}}
    {{--        url: "{{ url('getCartHtml') }}",--}}
    {{--        type: "POST",--}}
    {{--        data: $("#cartSubmitForm").serialize(),--}}
    {{--        dataType: "JSON",--}}
    {{--        headers: {'X-CSRF-TOKEN': _token},--}}
    {{--        cache: false--}}
    {{--    }).done(function (resp) {--}}
    {{--        $('#cart_html').html(resp.html);--}}
    {{--        $('#applied_cashback').val(resp.applied_cashback);--}}
    {{--        selectFreebees();--}}
    {{--        selectCoupon_code();--}}
    {{--        selectNCCash();--}}
    {{--        setSubscription();--}}
    {{--    });--}}
    {{--}--}}

    async function getCartHtml() {
        var user_id = '{{ $user->id ?? "" }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
            return null;
        }

        var _token = '{{ csrf_token() }}';

        // Show loader
        $('#cartLoader').show();

        try {
            const resp = await $.ajax({
                url: "{{ url('getCartHtml') }}",
                type: "POST",
                data: $("#cartSubmitForm").serialize(),
                dataType: "JSON",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false
            });

            // Populate cart
            $('#cart_html').html(resp.html);
            $('#applied_cashback').val(resp.applied_cashback);
            selectFreebees();
            selectCoupon_code();
            selectNCCash();
            setSubscription();

            return resp;
        } catch (err) {
            console.error(err);
            alert('Something went wrong while fetching the cart!');
            return null;
        } finally {
            // Hide loader
            $('#cartLoader').hide();
        }
    }


</script>

<script>
    let countdown;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const timerEl = document.getElementById('timer');
    const otpSection = document.getElementById('otpSection');
    const mobileInput = document.getElementById('mobile');
    const otpInput = document.getElementById('otp');
    const otpLoginModal = document.getElementById('otpLoginModal');

    function startTimer(duration) {
        let time = duration;
        timerEl.textContent = `01:00`;
        resendOtpBtn.disabled = true;

        countdown = setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            time--;

            if (time < 0) {
                clearInterval(countdown);
                timerEl.textContent = "00:00";
                resendOtpBtn.disabled = false;
            }
        }, 1000);
    }

    sendOtpBtn.addEventListener('click', function () {
        var mobile = mobileInput.value;
        var termsCheckbox = document.getElementById('termsCheckbox');
        if (mobile.length !== 10 || isNaN(mobile)) {
            alert('Enter valid 10-digit mobile numbersdfsdfsdf');
            return;
        }
        if (!termsCheckbox.checked) {
            alert('Please agree to Terms of Use & Privacy Policy');
            return;
        }
        ///////////////////////SEND OTP API////////////////////////
        var success = sendOTP(mobile);
        if (success) {
            otpSection.classList.remove('d-none');
            sendOtpBtn.classList.add('d-none');
            mobileInput.disabled = true;
            startTimer(60);
        }

    });

    resendOtpBtn.addEventListener('click', function () {
        const mobile = mobileInput.value;
        if (mobile.length !== 10 || isNaN(mobile)) {
            alert('Enter valid 10-digit mobile number');
            return;
        }

        sendOTP(mobile);
        startTimer(60);
    });

    document.getElementById('otpLoginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Get mobile number
        const mobile = document.getElementById('mobile').value;

        // Get OTP from boxes
        let otp = '';
        document.querySelectorAll('.otp-box').forEach(input => otp += input.value);

        // Basic validation
        if (otp.length < 4) {
            alert('Please enter a valid 4-digit OTP');
            return;
        }

        // Optional: assign to hidden input if backend expects it
        document.getElementById('otp').value = otp;

        // ✅ Use the values as needed

        login(mobile, otp);

        // AJAX call or further processing can go here
    });


    // 🧹 Reset modal on close
    otpLoginModal.addEventListener('hidden.bs.modal', function () {
        // Stop timer if running
        clearInterval(countdown);

        // Reset fields and visibility
        mobileInput.value = '';
        otpInput.value = '';
        otpSection.classList.add('d-none');
        resendOtpBtn.disabled = true;
        mobileInput.disabled = false;
        timerEl.textContent = '01:00';
    });


    function sendOTP(phone) {
        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ url('sendOTP') }}",
            type: "POST",
            data: {phone: phone},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {

            }
        });
        return true;
    }

    function login(phone, otp) {
        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ url('login') }}",
            type: "POST",
            data: {phone: phone, otp: otp},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                if (resp.result) {
                    location.reload();
                }

            }
        });
        return true;
    }
</script>

<script>
    document.querySelectorAll('.otp-box').forEach((input, index, inputs) => {
        input.addEventListener('input', function () {
            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });


</script>

<style>
    #map {
        height: 300px;
        width: 100%;
        margin-top: 15px;
    }

    #address_search {
    }
</style>

{{--new codingf vika kumar--}}

<style>
    .border-1 {
        border: 1px solid #ccc;
        /* padding: 10px; */
        margin: 5px;
        border-radius: 8px;
        display: flex;
        justify-content: center; /* horizontally center */
        align-items: center; /* vertically center */
        height: 150px; /* or any fixed height you want */
        transition: transform 0.3s, box-shadow 0.3s;
    }

</style>


<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Addresses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="text" class="form-control mb-2" id="address_search" placeholder="Search address">
                <input type="hidden" id="latitude">
                <input type="hidden" id="longitude">
                <input type="hidden" id="pincode">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCENCD7Uzd2YK0IJsUPgFI1gMNiHHPAuRA&libraries=places"></script>

<script>
    let map, marker, geocoder, autocomplete;

    function initMap() {
        const latlng = {lat: 28.6139, lng: 77.2090};
        geocoder = new google.maps.Geocoder();

        map = new google.maps.Map(document.getElementById("map"), {
            center: latlng,
            zoom: 14,
        });

        marker = new google.maps.Marker({
            position: latlng,
            map,
            draggable: true,
        });

        marker.addListener("dragend", () => {
            const pos = marker.getPosition();
            reverseGeocode(pos.lat(), pos.lng());
            updateLatLngInputs(pos.lat(), pos.lng());
        });

        // Autocomplete
        setTimeout(() => {
            const input = document.getElementById("address_search");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    return alert("No details available for input: '" + place.name + "'");
                }

                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                document.getElementById("latitude").value = place.geometry.location.lat();
                document.getElementById("longitude").value = place.geometry.location.lng();
                document.getElementById("address_phone").innerHTML = place.formatted_address;
                document.getElementById("address_text").innerHTML = place.formatted_address;

                let pincode = "";

                // ✅ Extract PIN code from address_components
                results[0].address_components.forEach(component => {
                    if (component.types.includes("postal_code")) {
                        pincode = component.long_name;
                    }
                });
                storeLocation(place.geometry.location.lat(), place.geometry.location.lng(), place.formatted_address, pincode);

                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                updateLatLngInputs(lat, lng);
            });

            // Re-focus input (helps show suggestions)
            input.focus();
        }, 300);
    }

    function reverseGeocode(lat, lng) {
        geocoder.geocode({location: {lat, lng}}, (results, status) => {
            if (status === "OK" && results[0]) {
                document.getElementById("address_search").value = results[0].formatted_address;
            }
        });
    }

    function updateLatLngInputs(lat, lng) {
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;

    }

    // Call initMap only when modal is shown
    document.getElementById("addressModal").addEventListener("shown.bs.modal", () => {
        setTimeout(() => initMap(), 200);
    });

</script>

<script>
    function initAutocomplete() {
        console.log("AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA");
        const input = document.getElementById('locationInput');
        const options = {
            // types: ['geocode'], // or 'address' to restrict results
            componentRestrictions: {country: "in"} // Restrict to India
        };
        const autocomplete = new google.maps.places.Autocomplete(input, options);

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                console.log("No details available for input: '" + place.name + "'");
                return;
            }
            document.getElementById("latitude").value = place.geometry.location.lat();
            document.getElementById("longitude").value = place.geometry.location.lng();
            document.getElementById("address_phone").innerHTML = place.formatted_address;
            document.getElementById("address_text").innerHTML = place.formatted_address;
            let pincode = "";

            // ✅ Extract PIN code from address_components
            place.address_components.forEach(component => {
                if (component.types.includes("postal_code")) {
                    pincode = component.long_name;
                }
            });
            storeLocation(place.geometry.location.lat(), place.geometry.location.lng(), place.formatted_address, pincode);
            console.log("Selected place:", place.formatted_address);
            console.log("Latitude:", place.geometry.location.lat());
            console.log("Longitude:", place.geometry.location.lng());

            // You can now store or use the place information as needed
        });
    }

    // Initialize autocomplete when the page loads
    window.onload = initAutocomplete;


    async function storeLocation(latitude, longitude, address, pincode) {
        $.ajax({
            url: '{{url('store_location')}}', // your route
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                latitude: latitude,
                longitude: longitude,
                address: address,
                pincode: pincode,
            },
            success: function (res) {
                console.log('Location stored in session');

                $('#addressSearchModal').hide();
            },
            error: function (err) {
                console.error('Error storing location', err);
            }
        });
    }
</script>


<script>
    $(document).ready(function () {
        var address = '{{$address ??''}}';
        if (address === '') {
            getCurrentLocation();
        }
    });

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success, error);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function success(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        console.log("Latitude:", latitude, "Longitude:", longitude);

        // Now reverse geocode the coordinates
        const geocoder = new google.maps.Geocoder();
        const latlng = {lat: latitude, lng: longitude};

        geocoder.geocode({location: latlng}, (results, status) => {
            if (status === "OK") {
                if (results[0]) {
                    console.log("Address:", results[0].formatted_address);
                    document.getElementById('locationInput').value = results[0].formatted_address;
                    var pincode = "";

                    // ✅ Extract PIN code from address_components
                    results[0].address_components.forEach(component => {
                        if (component.types.includes("postal_code")) {
                            console.log("component.typescomponent.types", component.types);
                            console.log("component.typescomponent.types", component.long_name);
                            pincode = component.long_name;
                        }
                    });


                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;
                    document.getElementById("address_phone").innerHTML = results[0].formatted_address;
                    document.getElementById("address_text").innerHTML = results[0].formatted_address;
                    document.getElementById("pincode").value = pincode;
                    storeLocation(latitude, longitude, results[0].formatted_address, pincode);


                } else {
                    alert("No results found");
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });
    }

    function error(err) {
        console.warn(`ERROR(${err.code}): ${err.message}`);
    }

    function checkLoginRedirect(url) {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('.mobile-header-active').removeClass('sidebar-visible');
            $('body').removeClass('mobile-menu-active');
            $('#otpLoginModal').modal('show');
        } else {
            window.location.href = url; // ✅ redirect to given url
        }
    }

</script>

<script>
    $(document).ready(function () {
        $('#productSearch').on('keyup', function () {
            let query = $(this).val();
            let category_id = $('#categorySelect').val();
            let _token = '{{ csrf_token() }}';

            if (query.length > 0) {
                $.ajax({
                    url: "{{ url('search-products') }}",
                    type: "POST",
                    data: {query: query, category_id: category_id, _token: _token},
                    success: function (data) {
                        let suggestionBox = $('#suggestionBox');
                        suggestionBox.empty();

                        if (data.length > 0) {
                            data.forEach(item => {
                                suggestionBox.append(
                                    '<a href="{{ url('products') }}/' + item.slug + '" class="list-group-item list-group-item-action">'
                                    + item.name +
                                    '</a>'
                                );
                            });
                            suggestionBox.show();
                        } else {
                            suggestionBox.hide();
                        }
                    }
                });
            } else {
                $('#suggestionBox').hide();
            }
        });

        // Hide suggestion box when clicking outside
        $(document).click(function (e) {
            if (!$(e.target).closest('#productSearch').length) {
                $('#suggestionBox').hide();
            }
        });
    });

</script>

<script>
    // (function (w, d, s, c, r, a, m) {
    //     w['KiwiObject'] = r;
    //     w[r] = w[r] || function () {
    //         (w[r].q = w[r].q || []).push(arguments)
    //     };
    //     w[r].l = 1 * new Date();
    //     a = d.createElement(s);
    //     m = d.getElementsByTagName(s)[0];
    //     a.async = 1;
    //     a.src = c;
    //     m.parentNode.insertBefore(a, m)
    // })(window, document, 'script', "https://app.interakt.ai/kiwi-sdk/kiwi-sdk-17-prod-min.js?v=" + new Date().getTime(), 'kiwi');
    // window.addEventListener("load", function () {
    //     kiwi.init('', '9rrBdQVIAwVNoYbWU9KwpQjrvmXn44fF', {});
    // });


    document.addEventListener('DOMContentLoaded', function () {

        const addressCards = document.querySelectorAll('.address-card');

        addressCards.forEach(card => {
            card.addEventListener('click', function () {

                const addressID = this.dataset.id;
                const fullAddress = this.dataset.fulladdress;
                const latitude = this.dataset.lat;
                const longitude = this.dataset.lng;
                const pincode = this.dataset.pincode;
                const token = '{{ csrf_token() }}';

                // Highlight selected
                document.querySelectorAll('.address-card')
                    .forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // 🔥 SET FULL ADDRESS INTO YOUR INPUTS
                document.getElementById('locationInput').value = fullAddress;
                document.getElementById('address_text').innerHTML = fullAddress;
                document.getElementById('address_phone').innerHTML = fullAddress;
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
                document.getElementById('pincode').value = pincode;

                // Call your store function if needed
                storeLocation(latitude, longitude, fullAddress, pincode);

                // AJAX request
                $.ajax({
                    url: "{{ url('update_selected_address') }}",
                    type: "POST",
                    data: {addressID: addressID},
                    dataType: "JSON",
                    headers: {'X-CSRF-TOKEN': token},
                    cache: false,
                    success: function (resp) {
                        const addr = resp.address;
                        const fullAddress =
                            (addr.flat_no ? addr.flat_no + ' ' : '') +
                            (addr.building_name ? addr.building_name + ', ' : '') +
                            (addr.landmark ? addr.landmark + '<br>' : '') +
                            (addr.location ? addr.location + '<br>' : '') +
                            (addr.city ? addr.city + ', ' : '') +
                            (addr.state ? addr.state + ', ' : '') +
                            (addr.pincode ? addr.pincode : '');
                        document.getElementById("latitude").value = addr.latitude;
                        document.getElementById("longitude").value = addr.longitude;
                        document.getElementById("address_phone").innerHTML = fullAddress;
                        document.getElementById("address_text").innerHTML = fullAddress;
                        $('#addressSearchModal').modal('hide');
                    }
                });
            });
        });

    });

    function showToast(message) {
        const toast = document.getElementById("toast");
        toast.innerText = message;
        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000); // hide after 3 seconds
    }

</script>


</html>
