<?php

// echo "<pre>";
// print_r($product);
$varients = $product->varients ?? '';
$selectedVarient = $varients[0] ?? '';
$images = $selectedVarient->images ??'';
$defaultImage = $images[0]['image'] ??url('public/assets/images/default.png');
?>

<div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
    <div class="product-img-action-wrap">
        <div class="product-img product-img-zoom">
            <a href='{{url('products/' . $product->slug??'')}}'>
                <img class="default-img" src="{{$defaultImage}}" alt="" />
                <img class="hover-img" src="{{$defaultImage}}" alt="" />
            </a>
        </div>
        <div class="product-badges product-badges-position product-badges-mrg">
            <span class="hot">Hot</span>
        </div>
    </div>
    <div class="product-content-wrap">
        <div class="product-rate-cover">
            <div class="product-rate d-inline-block">
                <div class="product-rating" style="width: 90%"></div>
            </div>
            <span class="font-small ml-5 text-muted"> (4.0)</span>
        </div>
        <h2><a href='{{url('products/' . $product->slug ??'')}}'>{{$product->name ?? ''}}</a></h2>

        <div>
            <span class="font-small text-muted">{{$selectedVarient->unit ?? ''}} </span>
        </div>
        <div class="product-card-bottom">
            <div class="product-price d-flex">
                <span>₹ {{$selectedVarient->selling_price ?? 0}}</span>
                <span class="old-price">₹ {{$selectedVarient->mrp ?? 0}}</span>
                <span class="stock-status in-stock" style="font-size: 12px;">{{ $selectedVarient->discount_per ?? 0 }}%
                    OFF</span>
            </div>

        </div>
        @if($selectedVarient->subscription_price > 0)
            <div class="button-container">
                <div class="nutrapass-circle">
                    <img src="{{url('public/assets/images/nutrapass.png')}}">
                </div>
                <div class="button-text">
                    Get @ ₹{{$selectedVarient->subscription_price ?? 0}} with Premium
                </div>
                <div class="arrow">
                    <img src="{{url('public/assets/images/arrow.svg')}}">
                </div>
            </div>

        @endif

        <div class="product-extra-link2 mt-2 d-flex">
            <a aria-label="Add To Wishlist" class="action-btn hover-up" 
                onclick="this.classList.toggle('filled')">
                <i class="fi-rs-heart" style="color: {{ $selectedVarient->is_wishlist == 1 ? "red" : "" }};"></i>
            </a>
            <button type="submit" onclick="window.location.href='{{url('products/' . $product->slug??'')}}'"
                class="button button-add-to-cart">Choose
                Options</button>
        </div>
    </div>
</div>