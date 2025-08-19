@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
    ?>
    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Order Detail</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-5 d-flex align-items-center justify-content-between">
                            <span>Order No : <a href="#">#{{$orders->id}}</a></span>
                            {!! \App\Helpers\CustomHelper::getOrderStatus($orders->id) !!}
                        </div>
                        <div class="row mb-5 g-4">
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Order Created at</p>
                                {{ date('d M Y h:i A',strtotime($orders->created_at)) }}
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Name</p>
                                {{ $orders->customer_name ?? '' }}
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Contact No</p>
                                {{ $orders->contact_no ?? '' }}
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Payment Status</p>
                                {{ strtoupper($orders->payment_method) }}
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Delivery Address</h5>
{{--                                            <a href="#">Edit</a>--}}
                                        </div>
                                        <div>Name: {{ $orders->customer_name ?? '' }}</div>
                                        <div>{{ $orders->house_no ?? '' }} {{ $orders->appartment ?? '' }}</div>
                                        <div>{{ $orders->landmark ?? '' }}</div>
                                        <div> {{ $orders->location ?? '' }}</div>
                                        <div>
                                            <i class="bi bi-telephone me-2"></i> {{ $orders->contact_no ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Billing Address</h5>
{{--                                            <a href="#">Edit</a>--}}
                                        </div>
                                        <div>Name: {{ $orders->customer_name ?? '' }}</div>
                                        <div>{{ $orders->house_no ?? '' }} {{ $orders->appartment ?? '' }}</div>
                                        <div>{{ $orders->landmark ?? '' }}</div>
                                        <div> {{ $orders->location ?? '' }}</div>
                                        <div>
                                            <i class="bi bi-telephone me-2"></i> {{ $orders->contact_no ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card widget">
                    <h5 class="card-header">Order Items</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                <tr>
                                    <th>IMAGE</th>
                                    <th>PRODUCT</th>
                                    <th>PRICE</th>
                                    <th>Unit/Unit Value</th>
                                    <th>QUANTITY</th>
                                    <th>SUBTOTAL</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($order_items)){
                                    $i = 1;
                                foreach ($order_items as $key => $value) {
                                    $product = \App\Helpers\CustomHelper::getProductDeatils($value->product_id??'');
                                    $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image??'');
                                    $varients = \App\Helpers\CustomHelper::getVendorProductSingleVarients($orders->vendor_id, $value->product_id, $value->variant_id);
                                    ?>
                                <tr>

                                    <td>
                                        <a href="#">
                                            <img src="{{$image}}" class="rounded" width="60"
                                                 alt="...">
                                        </a>
                                    </td>
                                    <td>{{$product->name??''}}</td>
                                    <td> ₹ {{$value->price??''}}</td>
                                    <td>{{$varients->unit??''}} {{$varients->unit_value??''}}</td>
                                    <td>{{$value->qty??''}}</td>
                                    <td class="text-right"> ₹ {{$value->net_price??''}}</td>
                                </tr>
                                <?php }
                                } ?>
        @if(!empty($orders->freebees_id) && $orders->freebees_id != "null")
                                @php
                                    $freebees_product = \App\Models\FreeProduct::where('id',$orders->freebees_id)->first();
                                        $pro = \App\Models\Products::where('id',$freebees_product->product_id)->first();

            $image = \App\Helpers\CustomHelper::getImageUrl('products',$pro->image??'');
 @endphp



                                <tr>
                                    <td>
                                        <a href="#">
                                            <img src="{{$image}}" class="rounded" width="60"
                                                 alt="...">
                                        </a>
                                    </td>
                                    <td>{{$pro->name??''}}</td>
                                    <td> ₹ {{$freebees_product->amount??''}}</td>
                                    <td></td>
                                    <td>1</td>
                                    <td class="text-right"> ₹ {{$freebees_product->amount??''}}</td>
                                </tr>
            @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Price</h6>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Sub Total :</div>
                            <div class="col-4">₹ {{$orders->order_amount??'0'}}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Delivery Charges :</div>
                            <div class="col-4">₹ {{$orders->delivery_charges??'0'}}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Tax(18%) :</div>
                            <div class="col-4">0</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Online Amount :</div>
                            <div class="col-4">₹ {{$orders->online_amount??'0'}}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">COD Amount :</div>
                            <div class="col-4">₹ {{$orders->cod_amount??'0'}}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Wallet Amount :</div>
                            <div class="col-4">₹ {{$orders->wallet??'0'}}</div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-4 text-end">
                                <strong>Total :</strong>
                            </div>
                            <div class="col-4">
                                <strong>₹ {{$orders->total_amount??'0'}}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Invoice</h6>
                        <div class="row justify-content-center mb-3">
                            <div class="col-6 text-end">Invoice No :</div>
                            <div class="col-6">
                                <a href="#">#5355619</a>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-6 text-end">Seller GST :</div>
                            <div class="col-6">{{$seller->tax_number??''}}</div>
                        </div>
                        {{--                        <div class="row justify-content-center mb-3">--}}
                        {{--                            <div class="col-6 text-end">Purchase GST :</div>--}}
                        {{--                            <div class="col-6">22HG9838964Z1</div>--}}
                        {{--                        </div>--}}
                        <div class="text-center mt-4">
                            <a target="_blank" href="{{route('orders.generateInvoicePdf',['id'=>$orders->id])}}" class="btn btn-outline-primary">Download PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
