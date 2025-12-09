<?php

use App\Helpers\CustomHelper;

$varients = $product->varients ?? '';
$selectedVarient = isset($varients[0]) ? (object)$varients[0] : (object)[];
$images = $selectedVarient->images ?? '';
$defaultImage = $images[0]['image'] ?? url('public/assets/images/default.png');




?>

<style>
    .product-extra-link2 .button.button-add-to-cart {
        height: 30px;
        line-height: 28px;
        font-size: 10px;
    }

    .estimate_day {
        font-size: 13px;
        word-break: break-word;
        white-space: normal;
        line-height: 1.3;
        margin-left: 10px;
    }

    @media (max-width: 576px) {
        .estimate_day {
            font-size: 11px;
        }
    }

    @media (max-width: 400px) {
        .estimate_day {
            font-size: 10px;
        }
    }

    .unit-box {
        min-height: 16px;
        display: flex;
        align-items: center;
    }

    /* Wishlist Floating Button (Top-Right Corner) */
    .wishlist-floating-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        background: #ffffff;
        border-radius: 50%;
        border: 1px solid #ddd;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9;
        cursor: pointer;
    }

    .wishlist-floating-btn i {
        font-size: 18px;
    }

    .wishlist-floating-btn.filled i {
        color: red !important;
    }

    /* Choose Button */
    .choose-btn {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        width: 100%;
    }

    .product-extra-link2 {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }

    @media (max-width: 450px) {
        .product-extra-link2 {
            flex-wrap: wrap;
            gap: 8px;
        }

        .choose-btn {
            width: 100%;
        }
    }

    .wishlist-icon {
        color: #aaa; /* default color */
        transition: 0.3s;
    }

    .wishlist-icon.active {
        color: red !important;
    }
    .product-img img {
        width: 100%;       /* Keep image responsive */
        height: 260px;     /* Default height for desktop */
        object-fit: cover; /* Ensures image covers the area without distortion */
    }

    /* For mobile screens */
    @media (max-width: 768px) {
        .product-img img {
            height: 120px; /* Reduce height on mobile */
        }
    }
    .product-cart-wrap:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.18);

    }
    .product-name {
        min-height: 42px; /* Force minimum 2 lines */
    }

    .product-name a {
        display: -webkit-box;
        -webkit-line-clamp: 2;   /* maximum 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 21px;  /* adjust to match your site */
        height: 42px;       /* 2 lines × 21px */
    }
    .deal-timer-box {
        text-align: center;
        padding: 10px;
    }

</style>
<style>
    .deal-timer-box {
        background: #DFFFF0;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        width: 100%;
        max-width: 350px;
        margin: auto;
    }

    .deal-timer-box h4 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #222;
    }

    .timer-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 20px;
    }

    .timer-item {
        background: #fff;
        padding: 15px 10px;
        border-radius: 10px;
        width: 30%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .timer-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c7a7b;
    }

    .timer-label {
        font-size: 14px;
        color: gray;
        margin-top: 5px;
    }

    .grab-btn {
        width: 100%;
        background: #18a999;
        color: #fff;
        padding: 12px 0;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .grab-btn:hover {
        background: #129183;
    }
</style>
<style>
    .timer-row {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap; /* IMPORTANT for mobile */
    }

    .timer-item {
        text-align: center;
        min-width: 70px; /* keeps size stable */
    }

    .timer-number {
        font-size: 18px;
        font-weight: bold;
        padding: 6px 10px;
        background: #fff3cd;
        border-radius: 6px;
    }

    @media (max-width: 375px) {
        .timer-item {
            min-width: 60px;
        }
        .timer-number {
            font-size: 16px;
            padding: 5px 8px;
        }
    }

</style>


<div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s"
     style="position: relative;">

    <div class="product-img-action-wrap position-relative">

        <div class="product-img product-img-zoom">
            <a href='{{ url('products/' . $product->slug ?? '') }}'>
                <img class="default-img" src="{{ $product->image??$defaultImage ??'' }}" alt="" />
                <img class="hover-img" src="{{ $product->image??$defaultImage ??'' }}" alt=""/>
            </a>
        </div>

        <!-- ⭐ Wishlist Icon at Top-Right -->
        <a class="wishlist-floating-btn"
           onclick="wishlist_save('{{$product->id}}', '{{$selectedVarient->id}}')">
            <i id="wishlist_icon_{{$selectedVarient->id}}"
               class="fi-rs-heart wishlist-icon {{ isset($selectedVarient->is_wishlist) && $selectedVarient->is_wishlist == 1 ? 'active' : '' }}">
            </i>
        </a>

        <!-- Discount badge -->
        <div class="product-badges product-badges-position product-badges-mrg"
             style="position: absolute; top: 10px; left: 10px;">
            <span class="stock-status in-stock"
                  style="font-size: 12px; background: #ff0000; color: #fff; padding: 3px 6px; border-radius: 4px;">
                {{ $selectedVarient->discount_per ?? 0 }} % OFF
            </span>
        </div>

    </div>


    <div class="product-content-wrap">

        <div class="product-rate-cover">
            <div class="product-rate d-inline-block">
                <div class="product-rating" style="width: 90%"></div>
            </div>
            <span class="font-small ml-5 text-muted"> (4.0)</span>
        </div>

        <h2 class="product-name">
            <a href="{{ url('products/' . $product->slug ?? '') }}">
                {{ $product->name ?? '' }}
            </a>
        </h2>

        <div class="unit-box">
            <span class="font-small text-muted" style="font-size:12px">
                {{ $selectedVarient->unit ?? '' }}
            </span>
        </div>

        <div class="product-card-bottom">
            <div class="product-price d-flex justify-content-start align-items-center">
                <div>
                    <span>₹ {{ $selectedVarient->selling_price ?? 0 }}</span>
                    <span class="old-price">₹ {{ $selectedVarient->mrp ?? 0 }}</span>
                </div>
            </div>
        </div>


        <div class="deal-timer-box"
             data-end-date="2025-12-30T23:59:59"

             data-product-id="{{ $product->id }}">
            <h4>Deal End in</h4>

            <div class="timer-row">

                <div class="timer-item">
                    <div id="hours{{ $product->id }}" class="timer-number">00</div>
                    <div class="timer-label">Hours</div>
                </div>

                <div class="timer-item">
                    <div id="mins{{ $product->id }}" class="timer-number">00</div>
                    <div class="timer-label">Minutes</div>
                </div>

                <div class="timer-item">
                    <div id="secs{{ $product->id }}" class="timer-number">00</div>
                    <div class="timer-label">Seconds</div>
                </div>
            </div>

            <button class="grab-btn" onclick="window.location.href='{{ url('products/' . $product->slug ?? '') }}'">Grab Now</button>
        </div>



    </div>
</div>



