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

</style>


<div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s"
     style="position: relative;">

    <div class="product-img-action-wrap position-relative">

        <div class="product-img product-img-zoom">
            <a href='{{ url('products/' . $product->slug ?? '') }}'>
                <img class="default-img" src="{{ $defaultImage }}" alt="" style="height: 200px"/>
                <img class="hover-img" src="{{ $defaultImage }}" alt=""/>
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

        @if(!empty($selectedVarient->subscription_price) && $selectedVarient->subscription_price > 0)
            <div class="button-container mt-1" onclick="openSubscriptionPopup()">
                <div class="nutrapass-circle">
                    <img src="{{ url('public/assets/staricon.png') }}">
                </div>
                <div class="button-text " style="font-size: 13px;font-weight: 500">
                    Get @ ₹{{ $selectedVarient->subscription_price ?? 0 }}
                </div>
            </div>
        @endif

        <!-- Choose Button Only -->
        <div class="product-extra-link2 mt-2 d-flex">
            <button type="submit"
                    onclick="window.location.href='{{ url('products/' . $product->slug ?? '') }}'"
                    class="button button-add-to-cart choose-btn">
                Choose Options
            </button>
        </div>

        @if(!empty($product->estimated_day))
            <div class="button-container1 mt-10 delivery-box">
                <div class="delivery-icon">
                    <i class="fa fa-truck"></i>
                </div>
                <div class="delivery-text estimate_day">
                    {{ $product->estimated_day ?? '' }}
                </div>
            </div>
        @endif

    </div>
</div>

