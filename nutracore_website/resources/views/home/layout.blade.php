<?php 
use App\Helpers\CustomHelper;


$categories = \App\Models\Category::where('status', 1)
    ->orderBy('name', 'asc')
    ->get();
$user = Auth::user();
$total_qty = 0;
if (!empty($user)) {
    $total_qty = \App\Models\Cart::where('user_id', $user->id)->sum('qty');
}
$address = '';

?>

<!DOCTYPE html>
<html class="no-js" lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Nutracore</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{url('public/assets')}}/images/default.png" />
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{url('public/assets')}}/css/plugins/animate.min.css" />
    <link rel="stylesheet" href="{{url('public/assets')}}/css/main2cc5.css?v=5.6" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
    <style>
        /* Import Poppins font */

        body {
            font-family: 'Poppins', sans-serif !important;

        }

        .button-container {
            height: 53px;
            display: flex;
            align-items: center;
            background: linear-gradient(to right, #E5A527, #FFEAA9, #E7A72B);
            border-radius: 39px;
            padding: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            overflow: hidden;
            position: relative;
            /* padding-right: 30px; */
        }

        .nutrapass-circle {
            width: 48px;
            height: 46px;
            background-color: #008080;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
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
            font-size: 12px;
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
    </style>
    <style>
        .action-btn.filled .fi-rs-heart {
            color: red;
        }

        .header-action-right .search-location {
            display: block;
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

        .modal-body {
            height: 400px;
            padding: 0;
        }
    </style>

    <header class="header-area header-style-1 header-height-2">
        <div class="mobile-promotion">
            <span>Grand opening, <strong>up to 15%</strong> off all items. Only <strong>3 days</strong> left</span>
        </div>
        <div class="header-top header-top-ptb-1 d-none d-lg-block" style="background-color: #00A8A8;color: #fff;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-4">
                        <div class="header-info">
                            <ul>
                                <li>Shopping First Time, Get 5% Off: WELCOME05</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-4">
                        <div class="text-center">
                            <div id="" class="d-inline-block">
                                <ul>
                                    <li>Shopping First Time, Get 5% Off: WELCOME05 </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4">
                        <div class="header-info header-info-right">
                            <ul>
                                <li>Shopping First Time, Get 5% Off: WELCOME05 </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="header-wrap">
                    <div class="logo logo-width-1">
                        <a href='{{url('/')}}'><img src="{{url('public/assets')}}/logo.png" alt="logo" /></a>
                    </div>
                    <div class="header-right">
                        <div class="search-style-2">
                            <form action="#">
                                <select class="select-active">
                                    <option>All Categories</option>
                                    @foreach ($categories as $category)
                                        <option>{{$category->name ?? ''}}</option>

                                    @endforeach
                                </select>
                                <input type="text" placeholder="Search for items..." />
                            </form>
                        </div>
                        <div class="header-action-right">
                            <div class="header-action-2">
                                <div class="search-location">
                                    <div class="header-box" data-bs-toggle="modal" data-bs-target="#addressModal">
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
                                    <a class='mini-cart-icon' href='{{url('cart')}}'>
                                        <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg" />
                                        <span class="pro-count blue" id="cart_qty">{{ $total_qty }}</span>
                                    </a>
                                    <a href='{{url('cart')}}'><span class="lable">Cart</span></a>
                                    <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                        <ul>
                                            <li>
                                                <div class="shopping-cart-img">
                                                    <a href='shop-product-right.html'><img alt="Nest"
                                                            src="{{url('public/assets')}}/imgs/shop/thumbnail-3.jpg" /></a>
                                                </div>
                                                <div class="shopping-cart-title">
                                                    <h4><a href='shop-product-right.html'>Daisy Casual Bag</a></h4>
                                                    <h4><span>1 × </span>$800.00</h4>
                                                </div>
                                                <div class="shopping-cart-delete">
                                                    <a href="#"><i class="fi-rs-cross-small"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="shopping-cart-img">
                                                    <a href='shop-product-right.html'><img alt="Nest"
                                                            src="{{url('public/assets')}}/imgs/shop/thumbnail-2.jpg" /></a>
                                                </div>
                                                <div class="shopping-cart-title">
                                                    <h4><a href='shop-product-right.html'>Corduroy Shirts</a></h4>
                                                    <h4><span>1 × </span>$3200.00</h4>
                                                </div>
                                                <div class="shopping-cart-delete">
                                                    <a href="#"><i class="fi-rs-cross-small"></i></a>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="shopping-cart-footer">
                                            <div class="shopping-cart-total">
                                                <h4>Total <span>$4000.00</span></h4>
                                            </div>
                                            <div class="shopping-cart-button">
                                                <a class='outline' href='{{url('cart')}}'>View cart</a>
                                                <a href='shop-checkout.html'>Checkout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="header-action-icon-2">
                                    <a onclick="checkLogin()">
                                        <img class="svgInject" alt="Nest"
                                            src="{{url('public/assets')}}/imgs/theme/icons/icon-user.svg" />
                                    </a>
                                    <a onclick="checkLogin()"><span class="lable ml-0">Account</span></a>
                                    @if(!empty($user))
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                            <ul>
                                                <li>
                                                    <a href=''><i class="fi fi-rs-user mr-10"></i>My
                                                        Account</a>
                                                </li>
                                                <li>
                                                    <a href=''><i class="fi fi-rs-location-alt mr-10"></i>Order Tracking</a>
                                                </li>
                                                <li>
                                                    <a href=''><i class="fi fi-rs-label mr-10"></i>My
                                                        Voucher</a>
                                                </li>
                                                <li>
                                                    <a href='shop-wishlist.html'><i class="fi fi-rs-heart mr-10"></i>My
                                                        Wishlist</a>
                                                </li>
                                                <li>
                                                    <a href=''><i class="fi fi-rs-settings-sliders mr-10"></i>Setting</a>
                                                </li>
                                                <li>
                                                    <a href='{{url('logout')}}'><i class="fi fi-rs-sign-out mr-10"></i>Sign
                                                        out</a>
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
                        <a href='{{url('/')}}'><img src="{{url('public/assets')}}/logo.png" alt="logo" /></a>
                    </div>
                    <div class="header-nav d-none d-lg-flex">
                        <div class="main-categori-wrap d-none d-lg-block">
                            <a class="categories-button-active" href="#">
                                <span class="fi-rs-apps"></span> <span class="et">Browse</span> All Categories
                                <i class="fi-rs-angle-down"></i>
                            </a>
                            <div class="categories-dropdown-wrap categories-dropdown-active-large font-heading">
                                <div class="d-flex categori-dropdown-inner">
                                    <ul>
                                        @foreach ($categories->take(5) as $category)
                                            <li>
                                                <a href="{{ url('') }}">
                                                    <img src="{{ CustomHelper::getImageUrl('categories', $category->image ?? '') }}"
                                                        alt="" />
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <ul class="end">
                                        @foreach ($categories->skip(5)->take(5) as $category)
                                            <li>
                                                <a href="{{ url('') }}">
                                                    <img src="{{ CustomHelper::getImageUrl('categories', $category->image ?? '') }}"
                                                        alt="" />
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                            <nav>
                                <ul>
                                    <li class="hot-deals"><img
                                            src="{{url('public/assets')}}/imgs/theme/icons/icon-hot.svg"
                                            alt="hot deals" /><a href='{{ url('deals') }}'>Deals</a></li>

                                    <li class="hot-deals"><a href='{{ url('/') }}'>Home</a></li>

                                    <li>
                                        <a href='{{url('categories')}}'>All Categories</a>
                                    </li>
                                    <li>
                                        <a href='{{url('nutrapass')}}'>Nutrapass</a>
                                    </li>
                                    <li>
                                        <a href='{{url('stores')}}'>Store Locator</a>
                                    </li>

                                    <li>
                                        <a href='{{url('about')}}'>About Us</a>
                                    </li>
                                    <li>
                                        <a href='{{url('contact')}}'>Contact</a>
                                    </li>


                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="hotline d-none d-lg-flex">
                        <img src="{{url('public/assets')}}/imgs/theme/icons/icon-headphone.svg" alt="hotline" />
                        <p>+91 88850 65550<span>24/7 Support Center</span></p>
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
                                <a href='{{ url('wishlist') }}'>
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-heart.svg" />
                                    <span class="pro-count white">4</span>
                                </a>
                            </div>
                            <div class="header-action-icon-2">
                                <a class="mini-cart-icon" href="{{ url('cart') }}">
                                    <img alt="Nest" src="{{url('public/assets')}}/imgs/theme/icons/icon-cart.svg" />
                                    <span class="pro-count white">2</span>
                                </a>
                                <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                    <ul>
                                        <li>
                                            <div class="shopping-cart-img">
                                                <a href='shop-product-right.html'><img alt="Nest"
                                                        src="{{url('public/assets')}}/imgs/shop/thumbnail-3.jpg" /></a>
                                            </div>
                                            <div class="shopping-cart-title">
                                                <h4><a href='shop-product-right.html'>Plain Striola Shirts</a></h4>
                                                <h3><span>1 × </span>$800.00</h3>
                                            </div>
                                            <div class="shopping-cart-delete">
                                                <a href="#"><i class="fi-rs-cross-small"></i></a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="shopping-cart-img">
                                                <a href='shop-product-right.html'><img alt="Nest"
                                                        src="{{url('public/assets')}}/imgs/shop/thumbnail-4.jpg" /></a>
                                            </div>
                                            <div class="shopping-cart-title">
                                                <h4><a href='shop-product-right.html'>Macbook Pro 2022</a></h4>
                                                <h3><span>1 × </span>$3500.00</h3>
                                            </div>
                                            <div class="shopping-cart-delete">
                                                <a href="#"><i class="fi-rs-cross-small"></i></a>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="shopping-cart-footer">
                                        <div class="shopping-cart-total">
                                            <h4>Total <span>$383.00</span></h4>
                                        </div>
                                        <div class="shopping-cart-button">
                                            <a href='{{url('cart')}}'>View cart</a>
                                            <a href='shop-checkout.html'>Checkout</a>
                                        </div>
                                    </div>
                                </div>
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
                    <a href='{{url('/')}}'><img src="{{url('public/assets')}}/logo.png" alt="logo" /></a>
                </div>
                <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                    <button class="close-style search-close">
                        <i class="icon-top"></i>
                        <i class="icon-bottom"></i>
                    </button>
                </div>
            </div>
            <div class="mobile-header-content-area">
                <div class="mobile-search search-style-3 mobile-header-border">
                    <form action="#">
                        <input type="text" placeholder="Search for items…" />
                        <button type="submit"><i class="fi-rs-search"></i></button>
                    </form>
                </div>
                <div class="mobile-menu-wrap mobile-header-border">
                    <!-- mobile menu start -->
                    <nav>
                        <ul class="mobile-menu font-heading">
                            <li class="{{ url('deals') }}"><a href=''>Deals</a></li>

                            <li class=""><a href='{{ url('/') }}'>Home</a></li>

                            <li>
                                <a href='{{url('categories')}}'>Categories</a>
                            </li>
                            <li>
                                <a href='{{url('nutrapass')}}'>Nutrapass</a>
                            </li>
                            <li>
                                <a href='{{url('stores')}}'>Store Locator</a>
                            </li>

                            <li>
                                <a href='{{url('about')}}'>About Us</a>
                            </li>
                            <li>
                                <a href='{{url('contact')}}'>Contact</a>
                            </li>

                        </ul>
                    </nav>
                    <!-- mobile menu end -->
                </div>
                <div class="mobile-header-info-wrap">
                    <div class="single-mobile-header-info">
                        <a href='page-contact.html'><i class="fi-rs-marker"></i> Our location </a>
                    </div>
                    <div class="single-mobile-header-info">
                        <a href='page-login.html'><i class="fi-rs-user"></i>Log In / Sign Up </a>
                    </div>
                    <div class="single-mobile-header-info">
                        <a href="#"><i class="fi-rs-headphones"></i>(+01) - 2345 - 6789 </a>
                    </div>
                </div>
                <div class="mobile-social-icon mb-50">
                    <h6 class="mb-15">Follow Us</h6>
                    <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-facebook-white.svg"
                            alt="" /></a>
                    <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-twitter-white.svg"
                            alt="" /></a>
                    <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-instagram-white.svg"
                            alt="" /></a>
                    <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-pinterest-white.svg"
                            alt="" /></a>
                    <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-youtube-white.svg"
                            alt="" /></a>
                </div>
                <div class="site-copyright">Copyright 2022 © Nest. All rights reserved. Powered by AliThemes.</div>
            </div>
        </div>
    </div>
    <!--End header-->
    @yield('content')
    <footer class="main">

        <section class="section-padding footer-mid">
            <div class="container pt-15 pb-20">
                <div class="row">
                    <div class="col">
                        <div class="widget-about font-md mb-md-3 mb-lg-3 mb-xl-0 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0">
                            <div class="logo mb-30">
                                <a class='mb-15' href='{{url('/')}}'><img src="{{url('public/assets')}}/logo.png"
                                        alt="logo" /></a>
                                <p class="font-lg text-heading">Awesome grocery store website template</p>
                            </div>
                            <ul class="contact-infor">
                                <li><img src="{{url('public/assets')}}/imgs/theme/icons/icon-location.svg"
                                        alt="" /><strong>Address: </strong> <span>H. No. 2-39, First Floor, Gopanpally,
                                        Tellapur Road, Hyderabad, 500019</span></li>
                                <li><img src="{{url('public/assets')}}/imgs/theme/icons/icon-contact.svg"
                                        alt="" /><strong>Call Us:</strong><span>(+91) 88850 65550</span></li>
                                <li><img src="{{url('public/assets')}}/imgs/theme/icons/icon-email-2.svg"
                                        alt="" /><strong>Email:</strong><span>support@nutracore.in</span></li>
                                <li><img src="{{url('public/assets')}}/imgs/theme/icons/icon-clock.svg"
                                        alt="" /><strong>Hours:</strong><span>10:00 - 18:00, Mon - Sat</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".1s>
                        <h4 class=" widget-title">Company</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Delivery Information</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms &amp; Conditions</a></li>
                            <li><a href="#">Contact Us</a></li>
                            <li><a href="#">Support Center</a></li>
                            <li><a href="#">Careers</a></li>
                        </ul>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                        <h4 class="widget-title">Account</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            <li><a href="#">Sign In</a></li>
                            <li><a href="#">View Cart</a></li>
                            <li><a href="#">My Wishlist</a></li>
                            <li><a href="#">Track My Order</a></li>
                            <li><a href="#">Help Ticket</a></li>
                            <li><a href="#">Shipping Details</a></li>
                            <li><a href="#">Compare products</a></li>
                        </ul>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".3s">
                        <h4 class="widget-title">Corporate</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            <li><a href="#">Become a Vendor</a></li>
                            <li><a href="#">Affiliate Program</a></li>
                            <li><a href="#">Farm Business</a></li>
                            <li><a href="#">Farm Careers</a></li>
                            <li><a href="#">Our Suppliers</a></li>
                            <li><a href="#">Accessibility</a></li>
                            <li><a href="#">Promotions</a></li>
                        </ul>
                    </div>
                    <div class="footer-link-widget col wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                        <h4 class="widget-title">Popular</h4>
                        <ul class="footer-list mb-sm-5 mb-md-0">
                            <li><a href="#">Milk & Flavoured Milk</a></li>
                            <li><a href="#">Butter and Margarine</a></li>
                            <li><a href="#">Eggs Substitutes</a></li>
                            <li><a href="#">Marmalades</a></li>
                            <li><a href="#">Sour Cream and Dips</a></li>
                            <li><a href="#">Tea & Kombucha</a></li>
                            <li><a href="#">Cheese</a></li>
                        </ul>
                    </div>
                    <div class="footer-link-widget widget-install-app col wow animate__animated animate__fadeInUp"
                        data-wow-delay=".5s">
                        <h4 class="widget-title">Install App</h4>
                        <p class="">From App Store or Google Play</p>
                        <div class="download-app">
                            <a href="#" class="hover-up mb-sm-2 mb-lg-0"><img class="active"
                                    src="{{url('public/assets')}}/imgs/theme/app-store.jpg" alt="" /></a>
                            <a href="#" class="hover-up mb-sm-2"><img
                                    src="{{url('public/assets')}}/imgs/theme/google-play.jpg" alt="" /></a>
                        </div>
                        <p class="mb-20">Secured Payment Gateways</p>
                        <img class="" src="{{url('public/assets')}}/imgs/theme/payment-method.png" alt="" />
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
                    <div class="hotline d-lg-inline-flex mr-30">
                        <img src="{{url('public/assets')}}/imgs/theme/icons/phone-call.svg" alt="hotline" />
                        <p>88850 65550<span>Working 8:00 - 22:00</span></p>
                    </div>
                    <div class="hotline d-lg-inline-flex">
                        <img src="{{url('public/assets')}}/imgs/theme/icons/phone-call.svg" alt="hotline" />
                        <p>88850 65550<span>24/7 Support Center</span></p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 text-end d-none d-md-block">
                    <div class="mobile-social-icon">
                        <h6>Follow Us</h6>
                        <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-facebook-white.svg"
                                alt="" /></a>
                        <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-twitter-white.svg"
                                alt="" /></a>
                        <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-instagram-white.svg"
                                alt="" /></a>
                        <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-pinterest-white.svg"
                                alt="" /></a>
                        <a href="#"><img src="{{url('public/assets')}}/imgs/theme/icons/icon-youtube-white.svg"
                                alt="" /></a>
                    </div>
                    <p class="font-sm">Up to 15% discount on your first subscribe</p>
                </div>
            </div>
        </div>
    </footer>
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


<!-- Mirrored from nest-frontend.netlify.app/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 08:00:04 GMT -->

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
                                OTP</button>
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



<script>
    function checkLogin() {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
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
        var qty = $('#quantity').val();
        $.ajax({
            url: "{{ url('addToCart') }}",
            type: "POST",
            data: { product_id: product_id, variant_id: variant_id, qty: qty },
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
            cache: false,
            success: function (resp) {

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
            data: { product_id: product_id, variant_id: variant_id, qty: qty },
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
            cache: false,
            success: function (resp) {
                $('#cart_quantity' + variant_id).val(qty);
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
            data: { product_id: product_id, variant_id: variant_id },
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
            cache: false,
            success: function (resp) {
                $('#quantity').val(resp.qty);
                $('#cart_qty').html(resp.total_qty);
            }
        });
    }
    function getCartHtml() {
        var user_id = '{{ $user->id ?? '' }}';
        if (user_id == '') {
            $('#otpLoginModal').modal('show');
        }
        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ url('getCartHtml') }}",
            type: "POST",
            data: {},
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
            cache: false,
            success: function (resp) {
                $('#cart_html').html(resp.html);
            }
        });
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
        const mobile = mobileInput.value;
        if (!mobile.match(/^[6-9]\d{9}$/)) {
            alert('Enter valid 10-digit mobile number');
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
        if (!mobile.match(/^[6-9]\d{9}$/)) {
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
            data: { phone: phone },
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
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
            data: { phone: phone, otp: otp },
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': _token },
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
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>




<script>

    $(document).ready(function () {
        fetchCurrentAddress();
    });

    function fetchCurrentAddress() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async position => {
                const { latitude, longitude } = position.coords;
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`);
                const data = await response.json();
                const address = data.display_name;
                $('#address_text').html(address);
                // document.getElementById('full-address').textContent = address;
            }, error => {
                // document.getElementById('address_text').html = "Location access denied.";
            });
        } else {
            // document.getElementById('address_text').html = "Geolocation not supported.";
        }


    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCENCD7Uzd2YK0IJsUPgFI1gMNiHHPAuRA&libraries=places"></script>

<script>
    let map, marker, geocoder, autocomplete;

    function initMap() {
        const latlng = { lat: 28.6139, lng: 77.2090 };
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

                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                updateLatLngInputs(lat, lng);
            });

            // Re-focus input (helps show suggestions)
            input.focus();
        }, 300);
    }

    function reverseGeocode(lat, lng) {
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
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



</html>