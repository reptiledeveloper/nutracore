@extends('layouts.layout')
@section('content')

    <?php
        use App\Helpers\CustomHelper;
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
    $order_status_arr = config('custom.order_status_arr');
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
                            <span>Order No : <a href="#">#{{ $orders->unique_id }}</a></span>
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

                                        @if(!empty($orders->customer_name))
                                            <strong>{{$orders->customer_name??''}}</strong><br>
                                            {{$orders->contact_no??''}}
                                        @else
                                            @php
                                                $user = \App\Helpers\CustomHelper::getUserDetails($orders->userID);
                                            @endphp
                                            <strong>{{$user->name??''}}</strong><br>
                                            {{$user->phone??''}}
                                        @endif

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
                                        @if(!empty($orders->customer_name))
                                            <strong>{{$orders->customer_name??''}}</strong><br>
                                            {{$orders->contact_no??''}}
                                        @else
                                            @php
                                                $user = \App\Helpers\CustomHelper::getUserDetails($orders->userID);
                                            @endphp
                                            <strong>{{$user->name??''}}</strong><br>
                                            {{$user->phone??''}}
                                        @endif
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

            </div>

            @php
            $flatDiscountValue = round($orders->flatDiscountValue);
            @endphp
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
                            <div class="col-4 text-end">Applied NC Cash :</div>
                            <div class="col-4">₹ {{$orders->applied_cashback??'0'}}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Discount ({{$orders->flat_discount_percent??0}} %):</div>
                            <div class="col-4">₹ {{round($orders->flatDiscountValue)??'0'}}</div>
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
                            @php
                                $final_total = (int)$orders->total_amount + (int)$orders->delivery_charges - (int)$orders->applied_cashback - (int)$orders->flatDiscountValue;
                               $flatDiscountValue = round($orders->flatDiscountValue);
                               $total =  $orders->total_amount - $flatDiscountValue;
                            @endphp
                            <div class="col-4">
                                <strong>₹ {{$final_total??'0'}}</strong>
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
                                <a href="#">#{{$orders->invoice_no??''}}</a>
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

                @if(!empty($orders->subscription_id) && $orders->subscription_id != "null")
                    @php
                        $subscription =\App\Models\SubscriptionPlans::where('id',$orders->subscription_id)->first();
                    @endphp
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title mb-4">Subscription</h6>
                            <div class="row">
                                <div class="col-6 text-end">Plan Name : {{$subscription->name??''}}</div>
                                <div class="col-6 text-end">Amount : {{$subscription->price??''}}</div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <div class="row mt-3">
            <div class="card widget">
                <h5 class="card-header">Order Items</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive mt-3">
                            <table class="table table-custom mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>IMAGE</th>
                                    <th>SKU</th>
                                    <th>PRODUCT</th>
                                    <th>PRICE</th>
                                    <th>Unit/Unit Value</th>
                                    <th>QUANTITY</th>
                                    <th>MRP</th>
                                    <th>Discount</th>
                                    <th>SUBTOTAL</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order_items as $i => $value)
                                    @php
                                        $product = CustomHelper::getProductDeatils($value->product_id);
                                        $image = CustomHelper::getImageUrl('products', $product->image);
                                        $varients = CustomHelper::getAdminProductSingleVarients($value->product_id, $value->variant_id);
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td><img src="{{ $image }}" class="rounded" width="60" alt="..."></td>
                                        <td>{{ $value->varient_sku ?? $product->sku??'' }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>₹ {{ $value->price }}</td>
                                        <td>{{ $varients->unit ??'' }} {{ $varients->unit_value ??'' }}</td>

                                        <td>{{ $value->qty ??'' }}</td>
                                        <td>{{ $value->mrp ??'' }}</td>
                                        <td>{{ $value->discount ??'' }}</td>
                                        <td class="text-right">₹ {{ $value->net_price ??'' }}</td>
                                        <td>
                                            <select class="form-control"
                                                    onchange="update_order_status('{{ $value->order_items_id }}', this.value, '')">
                                                <option value="">Select Status</option>
                                                @foreach($order_status_arr as $stat => $val)
                                                    <option
                                                        value="{{ $stat }}" {{ $stat == $value->status ? 'selected' : '' }}>
                                                        {{ $val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach

                                @if(!empty($orders->freebees_id) && $orders->freebees_id != "null")
                                    @php
                                        $freebees_product = \App\Models\FreeProduct::where('id',$orders->freebees_id)->first();
                                            $pro = \App\Models\Products::where('id',$freebees_product->product_id)->first();

                                            $image = \App\Helpers\CustomHelper::getImageUrl('products',$pro->image??'');
                                    @endphp



                                    <tr>
                                        <td>{{ $i + 2 }}</td>
                                        <td>
                                            <a href="#">
                                                <img src="{{$image}}" class="rounded" width="60"
                                                     alt="...">
                                            </a>
                                        </td>
                                        <td>{{$pro->name??''}} </td>
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
        </div>
    </div>

@endsection
