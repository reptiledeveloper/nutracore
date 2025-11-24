@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;


    $delivered_time = '';
    if (!empty($order_status)) {
        foreach ($order_status as $sta) {
            if ($sta->status == 'DELIVERED') {
                $delivered_time = date('d M Y h:i A', strtotime($sta->created_at));
            }
        }
    }
    $order_items = $orders->order_items ?? '';

    $exist = DB::table('order_courier')->where("order_id", $orders->id)->where('envia_data', '!=', null)->first();
    $order_details_envia = [];
    if (!empty($exist)) {
        $order_details_envia = json_decode($exist->envia_data) ?? '';

        $order_details_envia = $order_details_envia->data[0] ?? [];

    }
    ?>

    <style>
        .order-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 auto;
            padding: 20px;
        }

        .order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .order-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .order-info {
            flex: 1;
            margin-left: 10px;
        }

        .order-info h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .order-date {
            font-size: 14px;
            color: #777;
        }

        .order-status {
            display: flex;
            align-items: center;
            font-size: 16px;
            color: #28a745;
        }

        .status-icon {
            font-size: 20px;
            margin-right: 5px;
        }

        .status-text {
            font-weight: bold;
        }

        .order-details p {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .order-details strong {
            font-weight: bold;
        }

        .item-img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .item-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .item-unit {
            font-size: 14px;
            color: #777;
            margin-bottom: 6px;
        }

        .item-price {
            font-size: 15px;
            color: #333;
        }

        .bill-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .bill-btn {
            background: #f5f5f5;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .bill-btn:hover {
            background: #e9ecef;
        }

        .bill-body {
            margin-bottom: 10px;
        }

        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .bill-footer {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
        }

        .text-success {
            color: #28a745;
            font-weight: 500;
        }

        .order-tracker {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }

        .step-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #ccc;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: #999;
            z-index: 1;
        }

        .step-text {
            margin-top: 8px;
            font-size: 13px;
            text-align: center;
            color: #777;
        }

        .step.completed .step-icon {
            border-color: #28a745;
            background: #28a745;
            color: #fff;
        }

        .step.completed .step-text {
            color: #28a745;
            font-weight: 600;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #ccc;
            margin: 0 -10px;
        }

        .step-line.completed {
            background: #28a745;
        }

    </style>





    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span>Order Details - #NC{{$id}}
                </div>
            </div>
        </div>
        <div class="page-content pt-50 p-10">
            <div class="container">
                <div class="row">
                    <div class="order-card">
                        <header class="order-header">
                            <img src="{{$orders->order_items[0]->image??''}}" alt="Order Item" class="order-img">
                            <div class="order-info">
                                <h2>{{$orders->first_product_name??''}}</h2>
                                <h2>{!! CustomHelper::getOrderStatus($orders->id) !!}</h2>
                                <p class="order-date">Ordered On {{$orders->order_date_time??''}}</p>
                            </div>
                            <div class="order-status">
                                <span class="status-icon">&#10004;</span>
                                <span class="status-text">5.0</span>
                            </div>
                        </header>

                        <div class="order-details">
                            <p><strong>Order ID:</strong> #NC{{$id}}</p>
                            <p><strong>Payment Mode:</strong> {{$orders->payment_method}}</p>
                            <p><strong>Delivered on:</strong> {{$delivered_time}}</p>
                            <p><strong>Delivered to:</strong> {{ $orders->house_no ?? '' }}
                                , {{ $orders->apartment ?? '' }}, {{ $orders->landmark ?? '' }}
                                - {{ $orders->pincode ?? '' }}
                                {{ $orders->location ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    @foreach($order_items as $items)
                        <div class="col-md-6 col-lg-4 mb-4 p-2">
                            <div class="order-card d-flex align-items-center p-3">
                                <img src="{{ $items->image ?? '' }}" alt="{{ $items->name ?? '' }}"
                                     class="item-img me-3">
                                <div class="order-info">
                                    <h4 class="item-name">{{ $items->name ?? '' }}</h4>
                                    <p class="item-unit">{{ !empty($items->unit) ? $items->unit : '' }}  {{ !empty($items->unit_value) ? $items->unit_value: '' }}</p>
                                    <p class="item-price">
                                        <strong>₹ {{ $items->net_price ?? '' }}</strong>
                                        <span class="text-muted"> • {{ $items->qty ?? '' }} pcs</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                        @if(!empty($orders->freebees_id) && $orders->freebees_id != "null")
                            @php
                                $freebees_product = \App\Models\FreeProduct::where('id',$orders->freebees_id)->first();
                                    $pro = \App\Models\Products::where('id',$freebees_product->product_id)->first();

                                    $image = \App\Helpers\CustomHelper::getImageUrl('products',$pro->image??'');
                            @endphp


                            <div class="col-md-6 col-lg-4 mb-4 p-2">
                                <div class="order-card d-flex align-items-center p-3">
                                    <img src="{{ $image ?? '' }}" alt="{{$pro->name??''}}"
                                         class="item-img me-3">
                                    <div class="order-info">
                                        <h4 class="item-name">{{$pro->name??''}}</h4>
                                        <p class="item-unit"></p>
                                        <p class="item-price">
                                            <strong>₹  {{$freebees_product->amount??''}}</strong>
                                            <span class="text-muted"> • 1 pcs</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        @endif



                        @if(!empty($orders->subscription_id) && $orders->subscription_id != "null")
                            @php
                                $subscription =\App\Models\SubscriptionPlans::where('id',$orders->subscription_id)->first();
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="order-card d-flex align-items-center p-3">
                                    <div class="order-info">
                                        <h4 class="item-name">Subscription</h4>
                                        <p class="item-price">
                                            <strong>Plan Name : {{$subscription->name??''}}</strong><br>
                                            <strong>Amount : {{$subscription->price??''}}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                </div>

                <div class="row mt-3">
                    @php
                        $statuses = ['PLACED', 'CONFIRM', 'OUT_FOR_DELIVERY', 'DELIVERED'];
                        $current_status = strtoupper($orders->status ?? 'PLACED'); // Example: "CONFIRMED"

                        // find index of current status
                        $current_index = array_search($current_status, $statuses);
                    @endphp

                    <div class="order-tracker">
                        @foreach($statuses as $index => $status)
                            <div class="step {{ $index <= $current_index ? 'completed' : '' }}">
                                <div class="step-icon">{{ $index+1 }}</div>
                                <p class="step-text">{{ $status }}</p>
                            </div>
                            @if(!$loop->last)
                                <div class="step-line {{ $index < $current_index ? 'completed' : '' }}"></div>
                            @endif
                        @endforeach
                    </div>


                </div>

                <div class="row mt-3">
                    <div class="bill-card">
                        <div class="bill-body">
                            <div class="bill-row">
                                <span>Tracking Details</span>

                            </div>
                            <div class="bill-body">
                                <div class="bill-row text-success">
                                    <span>Shipment ID</span>
                                    <span>{{ $order_details_envia->shipmentId ?? '' }}</span>
                                </div>

                                <div class="bill-row text-success">
                                    <span>Tracking Number</span>
                                    <span>{{ $order_details_envia->trackingNumber ?? '' }}</span>
                                </div>

                                <div class="bill-row text-success">
                                    <span>Track URL</span>
                                    @if(!empty($order_details_envia->trackUrl))
                                    <span>
            <a href="{{ $order_details_envia->trackUrl ?? '' }}" target="_blank">
                Click Here
            </a>
        </span>
                                    @endif
                                </div>


                            </div>

                        </div>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="bill-card">
                        <div class="bill-header">
                            <a target="_blank" href="{{route('invoice',['id'=>$orders->id])}}" class="bill-btn">
                                📄 Download Invoice
                            </a>

                        </div>

                        <div class="bill-body">
                            <div class="bill-row">
                                <span>SubTotal</span>
                                <span>₹{{$orders->order_amount??0}}</span>
                            </div>
                            <div class="bill-row text-success">
                                <span>Total Discount</span>
                                <span>₹{{$orders->total_discount??0}}</span>
                            </div>
                            <div class="bill-row text-muted">
                                <span>Coupon Discount</span>
                                <span>₹{{$orders->coupon_discount??0}}</span>
                            </div>
                            <div class="bill-row text-muted">
                                <span>Freebies Price</span>
                                <span>₹{{$orders->freebees_price??0}}</span>
                            </div>

                            <div class="bill-row text-muted">
                                <span>Delivery Fee</span>
                                <span>₹{{$orders->delivery_charges??0}}</span>
                            </div>

                            <div class="bill-row text-muted">
                                <span>NC Cash</span>
                                <span>₹{{$orders->applied_cashback??0}}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="bill-footer">
                            <strong>Total Amount</strong>
                            <strong>₹{{$orders->total_amount??0}}</strong>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </main>

@endsection
