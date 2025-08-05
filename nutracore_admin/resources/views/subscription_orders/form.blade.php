@extends('layouts.layout')
@section('content')
    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
    $order_status_arr = config('custom.order_status_arr');
    $delivery_agents = \App\Helpers\CustomHelper::getDeliveryAgents();
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="row">
                                <label class="form-label">Order Status :</label>
                                <select class="form-control" name="" id=""
                                        onchange="update_order_status('',this.value,'')">
                                    <option value="" selected>Select Status</option>
                                    @foreach($order_status_arr as $stat =>$val)
                                        <option
                                                value="{{$stat??''}}" {{$stat == $orders->status?"selected":""}}>{{$val??''}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row p-1">
                                <label class="form-label">Assign Delivery Boy:</label>
                                <select class="form-control" name="" id=""
                                        onchange="update_order_status('','',this.value)">
                                    <option value="" selected>Assign Delivery Boy</option>
                                    @foreach($delivery_agents as $delivery_agent)
                                        <option
                                                value="{{$delivery_agent->id??''}}" {{$delivery_agent->id == $orders->agent_id?"selected":""}}>{{$delivery_agent->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dropdown ms-auto">
                                <a href=""
                                   class="btn btn-primary"><i class="fa fa-refresh" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
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
            </div>
        </div>
        <div class="row mt-3">
            <div class="card widget">
                <h5 class="card-header">Order Items</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>IMAGE</th>
                                <th>PRODUCT</th>
                                <th>PRICE</th>
                                <th>Unit/Unit Value</th>
                                <th>QUANTITY</th>
                                <th>SUBTOTAL</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($order_items)){
                                $i = 1;
                            foreach ($order_items as $key => $value) {
                                $image = \App\Helpers\CustomHelper::getImageUrl('products', $value->image);
                                $varients = \App\Helpers\CustomHelper::getVendorProductSingleVarients($orders->vendor_id, $value->product_id, $value->varient_id);
                                ?>
                            <tr>
                                <td>{{$i++}}</td>
                                <td>
                                    <a href="#">
                                        <img src="{{$image}}" class="rounded" width="60"
                                             alt="...">
                                    </a>
                                </td>
                                <td>{{$value->product_name??''}}</td>
                                <td> ₹ {{$value->price??''}}</td>
                                <td>{{$varients->unit??''}} {{$varients->unit_value??''}}</td>
                                <td>{{$value->qty??''}}</td>
                                <td class="text-right"> ₹ {{$value->net_price??''}}</td>
                                <td>
                                    <select class="form-control" name="" id=""
                                            onchange="update_order_status('{{$value->order_items_id??''}}',this.value,'')">
                                        <option value="" selected>Select Status</option>
                                        @foreach($order_status_arr as $stat =>$val)
                                            <option
                                                    value="{{$stat??''}}" {{$stat == $value->status?"selected":""}}>{{$val??''}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            <?php }
                            } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function update_order_status(item_id, status, delivery_boy) {
            var order_id = '{{$orders->id??''}}';
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('orders.update_order_status') }}",
                type: "POST",
                data: {status: status, order_id: order_id, item_id: item_id, delivery_boy: delivery_boy},
                dataType: "HTML",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false,
                success: function (resp) {
                    alert('Updated...');
                }
            });
        }
    </script>
@endsection
