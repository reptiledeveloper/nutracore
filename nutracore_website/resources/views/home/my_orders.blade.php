@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    ?>

    <style>

        .order-card {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order-card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 15px;
        }

        .order-info {
            flex: 1;
        }

        .order-info h3 {
            margin: 0 0 5px;
            font-size: 16px;
        }

        .order-info p {
            margin: 2px 0;
            color: #555;
            font-size: 14px;
        }

        .order-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }

        .delivered {
            background-color: #d4f4dd;
            color: #28a745;
        }

        .pending {
            background-color: #d4f4dd;
            color: yellow;
        }
        .out_for_delivery {
            background-color: #d4f4dd;
            color: #93ef93;
        }

        .canceled {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .order-price {
            font-weight: bold;
            margin-top: 5px;
        }

        .view-detail {
            color: #555;
            text-decoration: none;
            font-size: 14px;
            margin-left: auto;
        }
    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span>My Orders
                </div>
            </div>
        </div>
        <div class="page-content pt-50 p-10">
            <div class="container">
                <div class="row">
                    @if(!empty($orders))
                        @foreach($orders as $order)
                            <div class="order-card">
                                <img src="{{$order->image??''}}" alt="">
                                <div class="order-info">
                                    <h3>{{$order->first_product_name??''}}</h3>
                                    {!! CustomHelper::getOrderStatus($order->id) !!}
                                    <p>#NC{{$order->id??''}}</p>
                                    <p>{{$order->order_date_time??''}}</p>
                                    <p class="order-price">₹ {{$order->total_amount??''}} • {{$order->count_order_items??''}} Items</p>
                                </div>
                                <a href="{{route('order_details',['id'=>$order->id])}}" class="btn btn-primary btn-sm">View Detail</a>
                            </div>
                        @endforeach
                    @else

                    @endif

                </div>
            </div>
        </div>
    </main>

@endsection
