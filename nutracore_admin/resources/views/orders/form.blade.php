@extends('layouts.layout')
@section('content')

    @php
        use App\Helpers\CustomHelper;
        use Illuminate\Support\Facades\DB;

        $BackUrl = CustomHelper::BackUrl();
        $routeName = CustomHelper::getAdminRouteName();
        $order_items = CustomHelper::getOrderItemsWithProduct($orders->id);
        $order_status_arr = config('custom.order_status_arr');
        $delivery_agents = CustomHelper::getDeliveryAgents();
        $vendors = CustomHelper::getVendors();
        $products = [];
         $user = \App\Helpers\CustomHelper::getUserDetails($orders->userID);
        $address = DB::table('user_address')->where('id', $orders->address_id)->first();
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

        {{-- Order Status & Logistics --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex align-items-center">
                            <div class="row w-75">

                                {{-- Order Status --}}
                                <div class="col-md-6">
                                    <label class="form-label">Order Status :</label>
                                    <select class="form-control" onchange="update_order_status('', this.value, '', '')">
                                        <option value="">Select Status</option>
                                        @foreach($order_status_arr as $stat => $val)
                                            <option
                                                value="{{ $stat }}" {{ $stat == $orders->status ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Vendor (Pickup) --}}
                                <div class="col-md-6">
                                    <label class="form-label">Select Pickup Location :</label>
                                    <select class="form-control" onchange="update_order_status('', '', '', this.value)">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                            <option
                                                value="{{ $vendor->id }}" {{ $vendor->id == $orders->vendor_id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Logistics --}}
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Select Logistics :</label>
                                    <select class="form-control" onchange="update_logistics(this.value)">
                                        <option value="">Select Logistics</option>
                                        <option
                                            value="shiprocket" {{ $orders->logistics == 'shiprocket' ? 'selected' : '' }}>
                                            Shiprocket
                                        </option>
                                        <option
                                            value="delhivery" {{ $orders->logistics == 'delhivery' ? 'selected' : '' }}>
                                            Delhivery
                                        </option>
                                        <option value="porter" {{ $orders->logistics == 'porter' ? 'selected' : '' }}>
                                            Porter
                                        </option>
                                        <option value="envia" {{ $orders->logistics == 'envia' ? 'selected' : '' }}>
                                            Envia
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <div class="dropdown ms-auto">
                                <a data-bs-toggle="modal"
                                   data-bs-target="#updateDimensionModal" class="btn btn-primary">Update Dimension</a>
                            </div>
                            <div class="dropdown ms-auto">
                                <a href="" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logistics Partial --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if($orders->logistics == 'shiprocket')
                            @include('orders.shiprocket')
                        @elseif($orders->logistics == 'porter')
                            @include('orders.porter')
                        @elseif($orders->logistics == 'envia')
                            @include('orders.envia')
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Order Details --}}
        <div class="row mt-3">
            {{-- Left - Order & Address Info --}}
            <div class="col-lg-8 col-md-12">
                <div class="card mb-4">
                    <div class="card-body">

                        {{-- Header --}}
                        <div class="mb-5 d-flex align-items-center justify-content-between">
                            <span>Order No : <a href="#">#{{ $orders->id }}</a></span>
                            {!! CustomHelper::getOrderStatus($orders->id) !!}
                        </div>

                        {{-- Order Info --}}
                        <div class="row mb-5 g-4">
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Order Created at</p>
                                {{ date('d M Y h:i A', strtotime($orders->created_at)) }}
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Name</p> @if(!empty($orders->customer_name))
                                    {{ $orders->customer_name }}
                                @else
                                    {{$user->name??''}}

                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Contact
                                    No</p>{{ !empty($orders->contact_no) ? $orders->contact_no : $user->phone ??''}}
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <p class="fw-bold">Payment Status</p>{{ strtoupper($orders->payment_method) }}
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="row g-4">
                            {{-- Delivery Address --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Delivery Address</h5>
                                            <a href="javascript:;" data-bs-toggle="modal"
                                               data-bs-target="#updateAddressModal">Edit</a>
                                        </div>
                                        <div>Name:
                                            {{ !empty($orders->customer_name) ? $orders->customer_name : $user->name ??''}}</div>
                                        <div>{{ $orders->house_no }} {{ $orders->apartment }}</div>
                                        <div>{{ $orders->landmark }}</div>
                                        <div>{{ $orders->location }}</div>
                                        <div>{{ $orders->pincode }}</div>
                                        <div>
                                            <i class="bi bi-telephone me-2"></i> {{ !empty($orders->contact_no) ? $orders->contact_no : $user->phone ??''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Billing Address --}}
                            <div class="col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0">Billing Address</h5>
                                        </div>
                                        <div>Name:
                                            {{ !empty($orders->customer_name) ? $orders->customer_name : $user->name ??''}}</div>
                                        <div>{{ $orders->house_no }} {{ $orders->apartment }}</div>
                                        <div>{{ $orders->landmark }}</div>
                                        <div>{{ $orders->location }}</div>
                                        <div>
                                            <i class="bi bi-telephone me-2"></i> {{ !empty($orders->contact_no) ? $orders->contact_no : $user->phone ??''}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right - Pricing --}}



            <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Price</h6>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Sub Total :</div>
                            <div class="col-4">₹ {{ $orders->order_amount ?? 0 }}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Delivery Charges :</div>
                            <div class="col-4">₹ {{ $orders->delivery_charges ?? 0 }}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Tax(18%) :</div>
                            <div class="col-4">0</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Online Amount :</div>
                            <div class="col-4">₹ {{ $orders->online_amount ?? 0 }}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">COD Amount :</div>
                            <div class="col-4">₹ {{ $orders->cod_amount ?? 0 }}</div>
                        </div>
                        <div class="row justify-content-center mb-3">
                            <div class="col-4 text-end">Wallet Amount :</div>
                            <div class="col-4">₹ {{ $orders->wallet ?? 0 }}</div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-4 text-end"><strong>Total :</strong></div>
                            <div class="col-4"><strong>₹ {{ $orders->total_amount ?? 0 }}</strong></div>
                        </div>
                    </div>
                </div>
                @if(!empty($orders->subscription_id) && $orders->subscription_id != "null")
                    @php
                    $subscription =\App\Models\SubscriptionPlans::where('id',$orders->subscription_id)->first();
                    @endphp
                <div class="card">
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
        <div class="row mt-3" id="update_order_form" style="display: none;">
            <div class="card ">
                <div class="card-body">

                    <form action="{{ route('orders.update_order') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$orders->id}}">
                        <div class="row">
                            <div class="form-group col-md-3 mt-3" id="product_show">
                                <label for="inputEmail4" class="form-label">Choose Product</label>
                                <select class="form-control select2product" name="product_id"
                                        onchange="get_varients(this.value)">
                                    <option value="" selected>Choose Product</option>

                                </select>
                            </div>
                            <div class="form-group col-md-3 mt-3" id="product_show">
                                <label for="inputEmail4" class="form-label">Choose Varient</label>
                                <select class="form-control" name="varient_id" id="varient_id"
                                        onchange="get_varient_detail()">
                                    <option value="" selected>Choose Varient</option>

                                </select>
                            </div>
                            <div class="form-group col-md-3 mt-3" id="product_show">
                                <label for="inputEmail4" class="form-label">Qty</label>
                                <input type="text" name="qty" value="1" id="qty" onkeyup="get_varient_detail()"
                                       class="form-control">
                            </div>
                            <div class="form-group col-md-3 mt-3" id="product_show">
                                <label for="inputEmail4" class="form-label">Price</label>
                                <input type="text" name="price" readonly value="" id="price" class="form-control">
                            </div>
                            <div class="form-group col-md-3 mt-3" id="product_show">
                                <label for="inputEmail4" class="form-label">Total</label>
                                <input type="text" readonly value="" id="total" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mt-3">
                                <button class="btn btn-primary" type="submit">Save</button>
                                <button class="btn btn-danger" type="button" onclick="openAddProductForm()">Cancel
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>


        {{-- Order Items --}}
        <div class="row mt-3">
            <div class="card widget">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Order Items</div>
                            <div class="dropdown ms-auto">
                                <a onclick="openAddProductForm()" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                            </div>
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
                            @forelse ($order_items as $i => $value)
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
                            @empty

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
                                        <td>{{$pro->name??''}} </td>
                                        <td> ₹ {{$freebees_product->amount??''}}</td>
                                        <td></td>
                                        <td>1</td>
                                        <td class="text-right"> ₹ {{$freebees_product->amount??''}}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td colspan="9">No items found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <div class="modal fade" id="updateDimensionModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Book Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('orders.update_dimension',['id'=>$orders->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$orders->id}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mt-3">
                                Name
                            </div>
                            <div class="col-md-2 mt-3">
                                Weight (in KG)
                            </div>
                            <div class="col-md-2 mt-3">
                               Length (In CM)
                            </div>
                            <div class="col-md-2 mt-3">
                                Width (In CM)
                            </div>
                            <div class="col-md-2 mt-3">
                                Height (In CM)
                            </div>
                            @foreach($order_items as $i => $value)
                                @php
                                    $product = CustomHelper::getProductDeatils($value->product_id);
                                    $image = CustomHelper::getImageUrl('products', $product->image);
                                    $varients = CustomHelper::getAdminProductSingleVarients($value->product_id, $value->variant_id);
                                @endphp
                            <input type="hidden" name="item_ids[]" value="{{$value->id??''}}">
                                <div class="col-md-4 mt-3">
{{--                                    <label class="form-label">{{ $product->name }} - {{ $varients->unit ??'' }} {{ $varients->unit_value ??'' }}</label>--}}
                                    <label class="form-label">Box - {{$i+1}}</label>
                                </div>
                                <div class="col-md-2 mt-3">
                                  <input type="text" class="form-control" name="weight[]" value="{{$value->weight??''}}">
                                </div>
                                <div class="col-md-2 mt-3">
                                    <input type="text" class="form-control" name="length[]" value="{{$value->length??''}}">
                                </div>
                                <div class="col-md-2 mt-3">
                                    <input type="text" class="form-control" name="width[]" value="{{$value->width??''}}">
                                </div>
                                <div class="col-md-2 mt-3">
                                    <input type="text" class="form-control" name="height[]" value="{{$value->height??''}}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateAddressModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Book Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('orders.update_address',['id'=>$orders->id]) }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$orders->id}}">
                    <input type="hidden" name="address_id" value="{{$orders->address_id??''}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mt-3">
                                <label class="form-label">H No/ Flat No</label>
                                <input type="text" class="form-control" name="flat_no"
                                       value="{{ $address->flat_no??'' }}">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="building_name"
                                       value="{{ $address->building_name??'' }}">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Landmark</label>
                                <input type="text" class="form-control" name="landmark"
                                       value="{{ $address->landmark??'' }}">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode"
                                       value="{{ $address->pincode??'' }}">
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- JS --}}
    <script>
        function update_order_status(item_id, status, delivery_boy, vendor_id) {
            if(confirm('Are You Sure Want To Update The Status')){
                var order_id = '{{ $orders->id }}';
                var _token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('orders.update_order_status') }}",
                    type: "POST",
                    data: {status, order_id, item_id, delivery_boy, vendor_id},
                    headers: {'X-CSRF-TOKEN': _token},
                    success: function (resp) {
                        // alert('Updated...');
                    }
                });
            }
        }

        function update_logistics(logistics) {
            var order_id = '{{ $orders->id }}';
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('orders.update_logistics') }}",
                type: "POST",
                data: {logistics, logistics, order_id: order_id},
                headers: {'X-CSRF-TOKEN': _token},
                success: function () {
                    location.reload();
                }
            });
        }

        function get_varients(product_id) {

            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('orders.get_varients') }}",
                type: "POST",
                data: {product_id, product_id},
                headers: {'X-CSRF-TOKEN': _token},
                dataType: "HTML",
                success: function (resp) {
                    $('#varient_id').html(resp);
                }
            });
        }

        function get_varient_detail() {
            var varient_id = $('#varient_id').val();
            var total_amount = '{{ $orders->total_amount??0 }}';
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('orders.get_varient_detail') }}",
                type: "POST",
                data: {varient_id, varient_id},
                headers: {'X-CSRF-TOKEN': _token},
                dataType: "JSON",
                success: function (resp) {
                    var qty = $('#qty').val();
                    var total_selling = parseInt(qty) * parseInt(resp.varient.selling_price);
                    total_amount = parseFloat(total_amount) + parseFloat(total_selling);
                    $('#price').val(resp.varient.selling_price);
                    $('#total').val(total_amount);
                }
            });
        }

        function openAddProductForm() {
            $('#update_order_form').toggle(1000);
        }
    </script>

@endsection
