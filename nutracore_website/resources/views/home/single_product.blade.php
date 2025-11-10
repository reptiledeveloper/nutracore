<?php

// echo "<pre>";
// print_r($product);
$varients = $product->varients ?? '';

//$selectedVarient = $varients[0] ?? '';
$selectedVarient = isset($varients[0]) ? (object)$varients[0] : (object)[];
$images = $selectedVarient->images ?? '';
$defaultImage = $images[0]['image'] ?? url('public/assets/images/default.png');
?>

<style>
    .product-extra-link2 .button.button-add-to-cart {
        height: 30px;
        line-height: 28px;
    }

    .product-extra-link2 a {
        height: 33px;
        line-height: 37px;
    }

    .estimate_day {
        font-size: 13px;   /* default desktop/tablet */
        word-break: break-word;
        white-space: normal;
        line-height: 1.3;
    }

    /* Small phones */
    @media (max-width: 576px) {
        .estimate_day {
            font-size: 11px;
        }
    }

    /* Extra small (like iPhone SE, 375px) */
    @media (max-width: 400px) {
        .estimate_day {
            font-size: 10px;
        }
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

        <!-- Discount badge positioned at top-right -->
        <div class="product-badges product-badges-position product-badges-mrg"
             style="position: absolute; top: 10px; right: 10px;">
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

        <div>
            <span class="font-small text-muted" style="font-size: 12px">{{ $selectedVarient->unit ?? '' }}</span>
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
            <div class="button-container mt-1">
                <div class="nutrapass-circle">
                    <img src="{{ url('public/assets/staricon.png') }}">
                </div>
                <div class="button-text " style="font-size: 13px;font-weight: 500">
                    Get @ ₹{{ $selectedVarient->subscription_price ?? 0 }}
                </div>
            </div>
        @endif

        <div class="product-extra-link2 mt-2 d-flex">
            <a aria-label="Add To Wishlist" class="action-btn hover-up"
               onclick="this.classList.toggle('filled')">
                <i class="fi-rs-heart"
                   style="color: {{ isset($selectedVarient->is_wishlist) && $selectedVarient->is_wishlist == 1 ? 'red' : '' }};"></i>
            </a>
            <button type="submit" onclick="window.location.href='{{ url('products/' . $product->slug ?? '') }}'"
                    class="button button-add-to-cart">Choose Options
            </button>
        </div>

        @if(!empty($product->estimated_day) )
            <div class="button-container1 mt-10">
                <div class="nutrapass-circle1">
                    <img src="{{ url('public/assets/shipping.png') }}">
                </div>
                <div class="button-text estimate_day">
                    {{ $product->estimated_day ?? ''}}
                </div>
            </div>
        @endif
    </div>
</div>

