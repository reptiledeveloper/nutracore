@extends('home.layout')
@section('content')

    <main class="main pages">

        <!-- Breadcrumb -->
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ url('/') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Order Placed
                </div>
            </div>
        </div>

        <!-- Order Success Content -->
        <div class="page-content pt-50 pb-50">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="text-center">
                            <img src="{{ url('public/assets/success.png') }}" alt="Success" class="mb-4" style="width:100px;">
                            <h2 class="text-success mb-3" style="color: #00a8a8 !important;">Order Placed Successfully!</h2>
                            <p class="mb-2">Thank you for your purchase. Your order has been placed successfully.</p>
                            <p class="fw-bold">Order ID: <span class="text-primary" style="color: #00a8a8 !important;">{{ $unique_id ?? '' }}</span></p>
                            <a href="{{ url('/') }}" class="btn btn-primary mt-3">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

@endsection
