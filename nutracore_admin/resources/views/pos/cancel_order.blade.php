@extends('layouts.layout')
@section('content')

    @php
        use App\Helpers\CustomHelper;
        use Illuminate\Support\Facades\DB;

        $BackUrl = CustomHelper::BackUrl();
        $routeName = CustomHelper::getAdminRouteName();
        $order_items = [];
        $user = [];
        $address = [];
        if(!empty($orders)){
             $order_items = CustomHelper::getOrderItemsWithProduct($orders->id);
              $user = \App\Helpers\CustomHelper::getUserDetails($orders->userID);
        $address = DB::table('user_address')->where('id', $orders->address_id)->first();
        }

        $order_status_arr = config('custom.order_status_arr');
        $delivery_agents = CustomHelper::getDeliveryAgents();
        $vendors = CustomHelper::getVendors();
        $products = [];
$image = \App\Helpers\CustomHelper::getImageUrl('users', $user->image ?? '');
    @endphp

    <div class="content">

        {{-- Breadcrumb --}}
        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="bi bi-globe2 small me-2"></i> Dashboard</a>
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
                            <div class="d-none d-md-flex">Cancel POS Order</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                @include('snippets.errors')
                @include('snippets.flash')




                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="" method="get" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            <div class="row">
                                <div class="form-group col-md-4 mt-3">
                                    <label for="validationCustom01" class="form-label">Invoice No</label>
                                    <input type="text" class="form-control" placeholder="Invoice No" name="invoice_no"
                                           value="{{$_GET['invoice_no'] ??''}}">
                                </div>
                            </div>
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


               @if(!empty($orders))

                    <div class="card mt-5">
                        <div class="profile-cover bg-image mb-4"
                             style="height: 0%">
                            <div
                                class="container d-flex align-items-center justify-content-center h-100 flex-column flex-md-row text-center text-md-start">
                                <div class="avatar avatar-xl me-3">
                                    <img src="{{$image}}" class="rounded-circle img-fluid" alt="...">
                                </div>
                                <div class="my-4 my-md-0">
                                    <h3 class="mb-1">{{$user->name??'Guest User'}}</h3>
                                    <h5 class="mb-1">{{$user->phone??''}}</h5>
                                    <h5 class="mb-1">Credit Balance : ₹ {{$user->credit_balance??0}}</h5>
                                    <h5 class="mb-1">NC Cash : ₹ {{$user->cashback_wallet??0}}</h5>
                                    <h5 class="mb-1">Ban : {{$user->is_ban == 1?"Yes":"No"}}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="card widget">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body d-md-flex gap-4 align-items-center">
                                        <div class="d-none d-md-flex">Order Items</div>

                                    </div>
                                </div>

                                <div class="table-responsive mt-3">
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
                                        @foreach($order_items as $i => $value)
                                            @php
                                                $product = CustomHelper::getProductDeatils($value->product_id);
                                                $image = CustomHelper::getImageUrl('products', $product->image);
                                                $varients = CustomHelper::getAdminProductSingleVarients($value->product_id, $value->variant_id);
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td><img src="{{ $image }}" class="rounded" width="60" alt="..."></td>
                                                <td>{{ $product->name }}</td>
                                                <td>₹ {{ $value->price }}</td>
                                                <td>{{ $varients->unit ??'' }} {{ $varients->unit_value ??'' }}</td>
                                                <td>{{ $value->qty ??'' }}</td>
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
                                                <td><!-- Actions --></td>
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

                   <div class="row mt-3">

                       <div class="col-md-6">
                           <div class="card mb-4">
                               <div class="card-body">
                                   <h6 class="card-title mb-4">Exchange/Return </h6>
                                  <form action="{{route('pos.cancel_order_save')}}" method="post">
                                      @csrf
                                      <input type="hidden" name="order_id" value="{{$orders->id}}">
                                      <div class="form-group mt-3">
                                          <label>Order Type:</label><br>
                                          <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="pos_cancel_type" id="exchange"
                                                     value="exchange" {{$orders->pos_cancel_type == 'exchange'?"checked":""}}>
                                              <label class="form-check-label" for="exchange">Exchange</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio" name="pos_cancel_type" id="return"
                                                     value="return" {{$orders->pos_cancel_type == 'return'?"checked":""}}>
                                              <label class="form-check-label" for="return">Return</label>
                                          </div>
                                      </div>
                                      <input type="text" class="form-control mt-3" name="pos_cancel_remarks" value="{{$orders->pos_cancel_remarks??''}}" placeholder="Enter Remarks">
                                      @if(empty($orders->pos_cancel_type))
                                          <button class="btn btn-primary mt-3">Save</button>
                                      @endif
                                  </form>

                               </div>
                           </div>
                       </div>
                       <div class="col-md-6">
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
               @endif


            </div>
        </div>

    </div>



@endsection
