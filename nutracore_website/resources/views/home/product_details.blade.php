@extends('home.layout')
@section('content')
    <?php

    //                                                                                                             echo "<pre>";
    // print_r($product_data);
    $varients = $product_data->varients ?? '';
    $estimate_delivery = date('d M', strtotime('+2 days'));

    $selectedVarient = isset($varients[0]) ? (object)$varients[0] : (object)[];
    $options = $product_data->options ?? '';

    use App\Helpers\CustomHelper;


    ?>

    <style>
        .quantity-wrapper {
            display: flex !important;
            height: 45px !important;
            align-items: center;
            justify-content: space-between;
            width: 130px;
            padding: 6px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            gap: 8px;
            white-space: nowrap;
        }

        .quantity-btn {
            width: 100%;
            height: 32px;
            border: none;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            background: #e0e0e0;
        }

        .quantity-value {
            height: 40px !important;

            border: none;
            font-size: 18px;
            font-weight: 600;
            outline: none;
        }

        .sticky-cart-btn {
            position: fixed;
            bottom: 68px;
            left: 50%;
            transform: translateX(-50%);
            width: 93%;
            font-size: 16px;
            box-sizing: border-box;
            border-radius: 15px;
            padding: 10px !important;
            margin: 0;
            display: block;
            text-align: center !important; /* Force center */
            justify-content: center !important;
        }

        /* Desktop: Normal button */
        @media (min-width: 768px) {
            .sticky-cart-btn {
                position: static;
                transform: none;
                width: auto;
                border-radius: 8px;
            }
        }


    </style>



    <main class="main">
        <div class="container mb-30">
            <div class="row">
                <div class="col-xl-10 col-lg-12 m-auto">
                    <div class="product-detail accordion-detail">
                        <div class="row mb-50 mt-30">
                            <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
                                <div class="detail-gallery">
                                    <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                                    <!-- MAIN SLIDES -->
                                    {{--                                    <div class="product-image-slider" id="main-image-slider">--}}
                                    <div class="product-image-slider" id="main-image-slider">
                                        @foreach($selectedVarient->images ?? $product_data->images ?? [] as $img)
                                            <figure class="border-radius-10">
                                                <img src="{{ $img['image'] }}" alt="product image"/>
                                            </figure>
                                        @endforeach
                                    </div>
                                    <!-- THUMBNAILS -->
                                    <div class="slider-nav-thumbnails" id="thumbnail-slider">
                                        @foreach($selectedVarient->images ?? $product_data->images ?? [] as $img)
                                            <div><img src="{{ $img['image'] }}" alt="product image"/></div>
                                        @endforeach

                                    </div>
                                </div>
                                <!-- End Gallery -->
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="detail-info pr-30 pl-30">
                                    <span id="top-discount"
                                          class="stock-status in-stock">{{ $selectedVarient->discount_per ?? 0 }}% OFF</span>

                                    <h2 class="title-detail">{{$product_data->name ?? ''}}</h2>
                                    <span id="varient_name">{{ $selectedVarient->unit ?? '' }}</span>
                                    @if(!empty($product_data->estimated_day) )
                                        <div class="button-container1 mt-10 delivery-box">
                                            <div class="delivery-icon">
                                                <i class="fa fa-truck"></i>
                                            </div>
                                            <div class="delivery-text estimate_day">
                                                {{ $product_data->estimated_day ?? '' }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="product-detail-rating">
                                        <div class="product-rate-cover text-end">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 90%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (32 reviews)</span>
                                        </div>
                                    </div>
                                    <div class="product-price primary-color float-left">
                                        <span id="current-price" class="current-price text-brand">₹
                                            {{ $selectedVarient->selling_price ?? $product_data->product_selling_price }}</span>
                                        <span>
                                            <span id="old-price" class="old-price font-md ml-15">₹
                                                {{ $selectedVarient->mrp ?? $product_data->product_mrp }}</span>
                                        </span>
                                    </div>
                                    @foreach ($options as $optionIndex => $option)
                                        @php
                                            $option_values = $option['values'] ?? [];
                                        @endphp

                                        <div class="attr-detail attr-size mb-30">
                                            <strong class="mr-10">{{ $option['attribute'] ?? '' }}: </strong>
                                            <ul class="list-filter size-filter font-small"
                                                data-option-index="{{ $optionIndex }}">
                                                @foreach ($option_values as $valueIndex => $value)
                                                    <li class="{{$valueIndex == 0 ?"active":""}}"
                                                        id="valueIndex{{ $optionIndex }}{{ $valueIndex }}">
                                                        <a href="javascript:void(0);" class="variant-option"
                                                           data-option-index="{{ $optionIndex }}"
                                                           data-option-id="{{ $valueIndex }}"
                                                           data-option-value="{{ $value->values ?? '' }}">
                                                            {{ $value->values ?? '' }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach

                                    <div class="detail-extralink mb-50 detail-row">
                                        <!-- Quantity -->
                                        <div class="quantity-wrapper">
                                            <button class="quantity-btn quantity-decrease">−</button>
                                            <input type="text" id="quantity" class="quantity-value" value="{{ (isset($selectedVarient->qty) && $selectedVarient->qty > 0) ? $selectedVarient->qty : 1 }}" min="1">
                                            <button class="quantity-btn quantity-increase">+</button>
                                        </div>


                                        <button id="addToCartBtn"
                                                class="button button-add-to-cart addcart-btn sticky-cart-btn"
                                                onclick="addToCart()">
                                            <i class="fi-rs-shopping-cart"></i> Add to cart
                                        </button>

                                        <span id="outOfStockMsg" style="display:none; color:red; font-weight:bold;">Out of Stock</span>


                                        <!-- Wishlist -->
                                        <a aria-label="Add To Wishlist" class="action-btn hover-up wishlist-btn">
                                            <i class="fi-rs-heart"></i>
                                        </a>
                                    </div>


                                    <input type="hidden" id="selectedProductID" value="{{ $product_data->id ?? 0 }}">
                                    <input type="hidden" id="selectedVarientID" value="{{ $selectedVarient->id ?? 0 }}">


                                    <div class="d-flex gap-3 subscription-row">
                                        <div class="button-container w-50" onclick="openSubscriptionPopup()">
                                            <div class="nutrapass-circle">
                                                <img src="{{ url('public/assets/staricon.png') }}">
                                            </div>
                                            <div class="button-text" id="subscription_price">
                                                Get @ ₹ {{ $selectedVarient->subscription_price ?? 0 }}
                                            </div>
                                        </div>

                                        <div class="button-container1 w-50">
                                            <div class="nutrapass-circle1">
                                                <img src="{{ url('public/assets/staricon.png') }}">
                                            </div>
                                            <div class="button-text" id="nc_cash">
                                                Get {{ $selectedVarient->nc_cash ?? 0 }} Nc Cash
                                            </div>
                                        </div>
                                    </div>


                                    <div class="font-xs mt-10">
                                        <h3>Delivery Availability</h3>

                                        <div class="featured-card mt-3 p-3">

                                            <h6>Enter your Pin code</h6>

                                            <div class="d-flex gap-2 mt-2 flex-wrap pincode-box">
                                                <input type="text" id="pincode" class="pincode-input"
                                                       placeholder="Enter Pincode">
                                                <button class="btn pincode-btn" onclick="getEstimateDelivery()">Submit
                                                </button>
                                            </div>

                                            <div class="mt-3 delivery-list">

                                                <div class="delivery-item">
                                                    <i class="fa fa-check-circle tick-icon"></i>
                                                    <div class="delivery-item-text">
                                                        <span class="title">Free Shipping</span>
                                                        <span class="desc">
                        available on <span id="estimate_delivery">{{ $estimate_delivery }}</span>
                    </span>
                                                    </div>
                                                </div>

                                                <div class="delivery-item">
                                                    <i class="fa fa-check-circle tick-icon"></i>
                                                    <div class="delivery-item-text">
                                                        <span class="title">Pay on Delivery</span>
                                                        <span class="desc">might be available</span>
                                                    </div>
                                                </div>

                                                <div class="delivery-item">
                                                    <i class="fa fa-check-circle tick-icon"></i>
                                                    <div class="delivery-item-text">
                                                        <span class="title">Easy Return</span>
                                                        <span class="desc">on all products</span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>


                                    <div class="font-xs icons-row">
                                        <div class="row no-gutters">
                                            <div class="col-4 mt-3 text-center">
                                                <img class="info-icon"
                                                     src="{{ url('public/assets/images/genuine.png') }}">
                                            </div>

                                            <div class="col-4 mt-3 text-center">
                                                <img class="info-icon"
                                                     src="{{ url('public/assets/images/fast_delivery.png') }}">
                                            </div>

                                            <div class="col-4 mt-3 text-center">
                                                <img class="info-icon"
                                                     src="{{ url('public/assets/images/secure_payment.png') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="font-xs">
                                        <div class="row">
                                            <img src="{{ $product_data->certificate?? '' }}">
                                        </div>
                                    </div>


                                </div>
                                <!-- Detail Info -->
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="tab-style3">
                                <ul class="nav nav-tabs text-uppercase">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="Description-tab" data-bs-toggle="tab"
                                           href="#Description">Description</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="Additional-info-tab" data-bs-toggle="tab"
                                           href="#Additional-info">How To Use</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="Reviews-tab" data-bs-toggle="tab" href="#Reviews">Reviews
                                            (3)</a>
                                    </li>
                                </ul>
                                <div class="tab-content shop_info_tab entry-main-content">
                                    <div class="tab-pane fade show active" id="Description">
                                        <div>
                                            {!! $product_data->long_description??'' !!}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Additional-info">
                                        <div>
                                            {!! $product_data->short_description??'' !!}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Reviews">
                                        <!--Comments-->
                                        <div class="comments-area">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <h4 class="mb-30">Customer questions & answers</h4>
                                                    <div class="comment-list">
                                                        <div
                                                            class="single-comment justify-content-between d-flex mb-30">
                                                            <div class="user justify-content-between d-flex">
                                                                <div class="thumb text-center">
                                                                    <img src="public/assets/imgs/blog/author-2.png"
                                                                         alt=""/>
                                                                    <a href="#"
                                                                       class="font-heading text-brand">Sienna</a>
                                                                </div>
                                                                <div class="desc">
                                                                    <div class="d-flex justify-content-between mb-10">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="font-xs text-muted">December 4,
                                                                                2022 at 3:12 pm </span>
                                                                        </div>
                                                                        <div class="product-rate d-inline-block">
                                                                            <div class="product-rating"
                                                                                 style="width: 100%">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p class="mb-10">Lorem ipsum dolor sit amet,
                                                                        consectetur
                                                                        adipisicing elit. Delectus, suscipit
                                                                        exercitationem
                                                                        accusantium obcaecati quos voluptate nesciunt
                                                                        facilis itaque modi commodi dignissimos sequi
                                                                        repudiandae minus ab deleniti totam officia id
                                                                        incidunt? <a href="#" class="reply">Reply</a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="single-comment justify-content-between d-flex mb-30 ml-30">
                                                            <div class="user justify-content-between d-flex">
                                                                <div class="thumb text-center">
                                                                    <img src="public/assets/imgs/blog/author-3.png"
                                                                         alt=""/>
                                                                    <a href="#"
                                                                       class="font-heading text-brand">Brenna</a>
                                                                </div>
                                                                <div class="desc">
                                                                    <div class="d-flex justify-content-between mb-10">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="font-xs text-muted">December 4,
                                                                                2022 at 3:12 pm </span>
                                                                        </div>
                                                                        <div class="product-rate d-inline-block">
                                                                            <div class="product-rating"
                                                                                 style="width: 80%">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p class="mb-10">Lorem ipsum dolor sit amet,
                                                                        consectetur
                                                                        adipisicing elit. Delectus, suscipit
                                                                        exercitationem
                                                                        accusantium obcaecati quos voluptate nesciunt
                                                                        facilis itaque modi commodi dignissimos sequi
                                                                        repudiandae minus ab deleniti totam officia id
                                                                        incidunt? <a href="#" class="reply">Reply</a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="single-comment justify-content-between d-flex">
                                                            <div class="user justify-content-between d-flex">
                                                                <div class="thumb text-center">
                                                                    <img src="public/assets/imgs/blog/author-4.png"
                                                                         alt=""/>
                                                                    <a href="#"
                                                                       class="font-heading text-brand">Gemma</a>
                                                                </div>
                                                                <div class="desc">
                                                                    <div class="d-flex justify-content-between mb-10">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="font-xs text-muted">December 4,
                                                                                2022 at 3:12 pm </span>
                                                                        </div>
                                                                        <div class="product-rate d-inline-block">
                                                                            <div class="product-rating"
                                                                                 style="width: 80%">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p class="mb-10">Lorem ipsum dolor sit amet,
                                                                        consectetur
                                                                        adipisicing elit. Delectus, suscipit
                                                                        exercitationem
                                                                        accusantium obcaecati quos voluptate nesciunt
                                                                        facilis itaque modi commodi dignissimos sequi
                                                                        repudiandae minus ab deleniti totam officia id
                                                                        incidunt? <a href="#" class="reply">Reply</a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h4 class="mb-30">Customer reviews</h4>
                                                    <div class="d-flex mb-30">
                                                        <div class="product-rate d-inline-block mr-15">
                                                            <div class="product-rating" style="width: 90%"></div>
                                                        </div>
                                                        <h6>4.8 out of 5</h6>
                                                    </div>
                                                    <div class="progress">
                                                        <span>5 star</span>
                                                        <div class="progress-bar" role="progressbar" style="width: 50%"
                                                             aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                            50%
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <span>4 star</span>
                                                        <div class="progress-bar" role="progressbar" style="width: 25%"
                                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            25%
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <span>3 star</span>
                                                        <div class="progress-bar" role="progressbar" style="width: 45%"
                                                             aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                                            45%
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <span>2 star</span>
                                                        <div class="progress-bar" role="progressbar" style="width: 65%"
                                                             aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                                                            65%
                                                        </div>
                                                    </div>
                                                    <div class="progress mb-30">
                                                        <span>1 star</span>
                                                        <div class="progress-bar" role="progressbar" style="width: 85%"
                                                             aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                                            85%
                                                        </div>
                                                    </div>
                                                    <a href="#" class="font-xs text-muted">How are ratings
                                                        calculated?</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!--comment form-->
                                        <div class="comment-form">
                                            <h4 class="mb-15">Add a review</h4>
                                            <div class="product-rate d-inline-block mb-30"></div>
                                            <div class="row">
                                                <div class="col-lg-8 col-md-12">
                                                    <form class="form-contact comment_form" action="#" id="commentForm">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <textarea class="form-control w-100" name="comment"
                                                                              id="comment" cols="30" rows="9"
                                                                              placeholder="Write Comment"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <input class="form-control" name="name" id="name"
                                                                           type="text" placeholder="Name"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <input class="form-control" name="email" id="email"
                                                                           type="email" placeholder="Email"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <input class="form-control" name="website"
                                                                           id="website"
                                                                           type="text" placeholder="Website"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="button button-contactForm">
                                                                Submit
                                                                Review
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-60">
                            <div class="col-12">
                                <h2 class="section-title style-1 mb-30">Recommended Products</h2>
                            </div>
                            <div class="col-12">
                                <div class="row related-products">
                                    @foreach($products as $product)
                                        <div class="col-6 col-sm-6 col-md-4 col-lg-1-5">
                                            @include('home.single_product', ['product' => $product])
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
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
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>



    <script>
        function getEstimateDelivery() {
            var pincode = $('#pincode').val();
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ url('getEstimateDelivery') }}",
                type: "POST",
                data: {pincode: pincode},
                dataType: "JSON",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false,
                success: function (resp) {
                    if (resp.message != "") {
                        alert(resp.message);
                    } else {
                        $('#estimate_delivery').html(resp.delivery_data);
                    }

                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            const selectedOptions = {}; // e.g., { "0": "2 Lbs", "1": "Double Rich Chocolate" }
            const variants = @json($varients); // Laravel-passed variant array
            document.querySelectorAll('.variant-option').forEach(btn => {
                btn.addEventListener('click', function () {
                    console.log('Clicked:', this.dataset.optionValue);
                    const index = this.dataset.optionIndex;
                    const id = this.dataset.optionId;
                    const value = this.dataset.optionValue;

                    // Remove active from group
                    const ul = this.closest('ul');
                    ul.querySelectorAll('li').forEach(li => li.classList.remove('active'));

                    // Add active to clicked option
                    const elementId = `valueIndex${index}${id}`;
                    // const li = document.getElementById(elementId);
                    // if (li) li.classList.add('active');
                    $('#elementId').addClass('active');

                    // Save selection
                    selectedOptions[index] = value;

                    // When all options selected
                    const totalOptions = document.querySelectorAll('.list-filter[data-option-index]').length;

                    if (Object.keys(selectedOptions).length === totalOptions) {
                        const combinedValue = Object.keys(selectedOptions)
                            .sort((a, b) => a - b)
                            .map(key => selectedOptions[key])
                            .join(' / ');

                        console.log("Combined Value:", combinedValue);

                        // Match variant by `unit` value
                        const matchedIndex = variants.findIndex(v => v.unit === combinedValue);
                        if (matchedIndex !== -1) {
                            const matchedVariant = variants[matchedIndex];
                            console.log("Matched Variant Index:", matchedIndex);
                            console.log("Matched Variant:", matchedVariant);

                            $('#varient_name').html(matchedVariant.unit);
                            $('#selectedVarientID').val(matchedVariant.id);

                            ////////Get Cart QTY//////////
                            getCartQty();
                            updateVariantView(matchedVariant);

                            // Handle Add to Cart button based on stock
                            if (matchedVariant.is_out_of_stock == 1) {
                                $('#addToCartBtn').prop('disabled', true).hide();
                                $('#outOfStockMsg').show();
                            } else {
                                $('#addToCartBtn').prop('disabled', false).show();
                                $('#outOfStockMsg').hide();
                            }

                        } else {
                            console.warn("No matching variant found for:", combinedValue);
                            $('#addToCartBtn').prop('disabled', true).hide();
                            $('#outOfStockMsg').hide();
                        }

                    }
                });
            });


            function updateVariantView(variant) {
                // Update price
                document.getElementById('current-price').innerText = '₹' + variant.selling_price;
                document.getElementById('old-price').innerText = '₹' + variant.mrp;
                document.getElementById('top-discount').innerText = `${variant.discount_per}% OFF`;
                document.getElementById('subscription_price').innerText = ` Get @ ₹ ${variant.subscription_price}`;
                document.getElementById('nc_cash').innerText = ` Get ${variant.nc_cash} Nc Cash`;

                // Update images
                const $thumbSlider = $('#thumbnail-slider');

                // Remove all existing slides safely
                $thumbSlider.slick('slickRemove', null, null, true);

                // Add new thumbnails
                variant.images.forEach(img => {
                    $thumbSlider.slick('slickAdd', `<div><img src="${img.image}" alt="product image" /></div>`);
                });


                // const mainSlider = document.getElementById('main-image-slider');
                // mainSlider.innerHTML = '';
                //
                // variant.images.forEach(img => {
                //     mainSlider.innerHTML += `<figure class="border-radius-10"><img src="${img.image}" alt="product image" /></figure>`;
                // });


            }


            // document.addEventListener('DOMContentLoaded', function () {
            //     // Auto-click the first value of each option group
            //     document.querySelectorAll('.list-filter[data-option-index]').forEach(ul => {
            //         const firstOption = ul.querySelector('.variant-option');
            //         if (firstOption) {
            //             console.log("firstOptionfirstOption",firstOption);
            //             firstOption.click(); // triggers above listener
            //         }
            //     });
            //
            // });
            // document.addEventListener("DOMContentLoaded", function () {
            //     // Select all option lists
            //     document.querySelectorAll('.list-filter[data-option-index]').forEach(ul => {
            //         // Find the first li in each list
            //         const firstLi = ul.querySelector('li');
            //         if (firstLi) {
            //             console.log("firstLifirstLifirstLi",firstLi);
            //             // Find the link inside li and trigger click
            //             const firstOption = firstLi.querySelector('.variant-option');
            //             if (firstOption) {
            //                 firstOption.click();
            //             }
            //         }
            //     });
            // });

        });

        // $(document).ready(function () {
        //     document.querySelectorAll('.list-filter[data-option-index]').forEach(ul => {
        //         // Find the first li in each list
        //         const firstLi = ul.querySelector('li');
        //         if (firstLi) {
        //             console.log("firstLifirstLifirstLi", firstLi);
        //             // Find the link inside li and trigger click
        //             const firstOption = firstLi.querySelector('.variant-option');
        //             if (firstOption) {
        //                 // firstOption.click();
        //             }
        //         }
        //     });
        //
        // });

        document.addEventListener("DOMContentLoaded", function () {
            // Select all option groups
            document.querySelectorAll('.list-filter').forEach(ul => {
                // Find the first <li> in the group
                const firstLi = ul.querySelector('li');
                if (firstLi) {
                    const firstOption = firstLi.querySelector('.variant-option');
                    if (firstOption) {
                        // Trigger click
                        firstOption.click();

                        // Ensure active class is applied to the <li>
                        firstLi.classList.add('active');
                    }
                }
            });
        });



        document.querySelector('.quantity-decrease').onclick = function () {
            let input = document.getElementById('quantity');
            let value = parseInt(input.value);
            if (value > 1) input.value = value - 1;
        }

        document.querySelector('.quantity-increase').onclick = function () {
            let input = document.getElementById('quantity');
            let value = parseInt(input.value);
            input.value = value + 1;
        }


    </script>




@endsection
