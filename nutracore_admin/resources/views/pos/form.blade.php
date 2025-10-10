@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $id = $pos->id ?? '';
    $invoice_no = $pos->invoice_no ?? '';
    $date = $pos->date ?? '';
    $due_date = $pos->due_date ?? '';
    $user_id = $pos->user_id ?? '';
    $total_amount = $pos->total_amount ?? '';
    $payment_mode = $pos->payment_mode ?? '';
    $payment_status = $pos->payment_status ?? '';
    $order_type = $pos->order_type ?? '';
    $created_by = $pos->created_by ?? '';
    $status = $pos->status ?? '';
    $is_delete = $pos->is_delete ?? '';
    $created_at = $pos->created_at ?? '';
    $updated_at = $pos->updated_at ?? '';

    $settings = \App\Models\Setting::where('id', 1)->first();
    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $brands = \App\Helpers\CustomHelper::getBrands();

    $products = \App\Helpers\CustomHelper::getProductsWithVarients();
    $customers = [];
//    $exist = \App\Models\POSDailyCash::where('date', date('Y-m-d'))->first();
    $vendor_id_selected = '';
    $admin = Auth::guard('admin')->user();
    if ($admin->role_id === 0) {
        // Superadmin: use session or request store_id
        $vendor_id_selected = session('store_id') ?? ($request->store_id ?? null);
    } else {
        // Normal vendor/admin: use own vendor_id
        $vendor_id_selected = $admin->vendor_id ?? null;
    }
    $exist = \App\Models\POSDailyCash::whereDate('date', date('Y-m-d'))->where('store_id',$vendor_id_selected)->first();
    $order_amount = \App\Models\Order::where('delivery_date',date('Y-m-d'))->where('order_from','POS')->where('payment_method','Cash')->sum('total_amount');
    $orders = \App\Models\Order::where('delivery_date',date('Y-m-d'))->where('order_from','Multipay')->where('payment_method','Cash')->get();
    if(!empty($orders)){
        foreach ($orders as $order){
            $payment_method_values = json_decode($order->payment_method_values)??'';
            if(!empty($payment_method_values)){
                if((int)$payment_method_values->cash > 0){
                    $order_amount+=(float)$payment_method_values->cash;
                }
            }
        }
    }
    $total_cash = 0;
    if(!empty($exist)){
        $total_cash = (float)$exist->today_balance + (float)$order_amount;
    }

    ?>
    <style>
        .transaction-details {
            display: flex;
            flex-wrap: wrap;
            background-color: #f0f0f0;
            margin-top: 20px;
            gap: 20px;
            justify-content: space-between;
        }

        .transaction-item {
            display: flex;
            margin-top: 20px;
            flex-direction: column;
            align-items: center;
        }

        .transaction-details {
            display: flex; /* arrange items horizontally */
            gap: 20px; /* optional spacing between items */
        }

        .transaction-item {
            padding: 0 10px; /* space inside each item */
            border-right: 1px solid #ccc; /* vertical line */
        }

        /* Remove the border from the last item */
        .transaction-item:last-child {
            border-right: none;
        }

        .transaction-details {
            display: flex; /* horizontal layout */
        }

        .transaction-item {
            flex: 1; /* equal width for all items */
            text-align: center; /* center text */
            /* light gray background */
            padding: 10px 0; /* top/bottom padding */
            border-right: 1px solid #ccc; /* vertical line */
        }

        /* Remove border for last item */
        .transaction-item:last-child {
            border-right: none;
        }

        /* Optional: make labels stack vertically nicely */
        .transaction-item label {
            display: block;
        }

        .payment-option.active {
            background-color: #11AEAE !important;
            color: #fff !important;
            border: 2px solid #11AEAE;
        }

        .payment-option.active a {
            color: #fff !important;
        }

        /* Wrap product name on small screens */
        #cartTable td, #cartTable th {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            #cartTable th:nth-child(5),
            #cartTable th:nth-child(6),
            #cartTable th:nth-child(7),
            #cartTable th:nth-child(8),
            #cartTable th:nth-child(9),
            #cartTable th:nth-child(10) {
                display: none; /* hide less important columns on mobile */
            }

            #cartTable td:nth-child(5),
            #cartTable td:nth-child(6),
            #cartTable td:nth-child(7),
            #cartTable td:nth-child(8),
            #cartTable td:nth-child(9),
            #cartTable td:nth-child(10) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .input-group {
                flex-direction: column;
            }

            .input-group .btn, .input-group .qty {
                width: 100%;
                margin-bottom: 2px;
            }
        }

        .small-card {
            margin: 0 auto; /* center horizontally */
            font-size: 12px; /* smaller font size */
        }

        .small-card .card-body {
            padding: 0.5rem; /* smaller padding */
        }

    </style>

    <div class="modal fade" id="closeStore" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Current Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="closePos" action="{{ route('pos.close') }}" method="POST">
                    @csrf
                    <input type="hidden" name="store_id" value="{{$vendor_id_selected}}">
                    <div class="modal-body">
                        <div class="row">
                            <h4>Opening Cash : ₹ {{$exist->today_balance??0}}</h4>
                            <h4>Cash Payment : ₹ {{$order_amount??0}}</h4>
                            <h4>Total Cash Left In Drawer : ₹ <span id="total_cash_display">{{ $total_cash }}</span></h4>


                            <div class="col-md-12 mt-3">
                                <label>Physical Drawer <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="today_last_balance" id="today_last_balance" class="form-control"
                                           placeholder="Physical Drawer" oninput="checkCashDifference()">
                                </div>
                            </div>
                            <span id="cash_remark" style="font-weight:600;"></span>
                            <div class="col-md-12 mt-3">
                                <label>Closing Note</label>
                                <div class="input-group">
                                    <input type="text" name="closing_note" class="form-control" placeholder="Closing Note">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="bsubmit" onclick="submitclosePos()" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <form class="card-body" action="#" id="posForm" method="post" accept-chartset="UTF-8"
          enctype="multipart/form-data" role="form">
        {{ csrf_field() }}
        <input type="hidden" id="id" value="{{ $id }}">
        <div class="content ">

            <div class="mb-4">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">
                                <i class="bi bi-globe2 small me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                    </ol>
                </nav>
            </div>
            @include('snippets.errors')
            @include('snippets.flash')

            <div class="row">
                <div class="col-md-12">
                    <div class="card d-none">
                        <div class="card-body">
                            <div class="d-md-flex gap-4 align-items-center">
                                <div class="d-none d-md-flex">{{$page_heading}}</div>
                                <?php if (request()->has('back_url')){
                                    $back_url = request('back_url'); ?>
                                <div class="dropdown ms-auto">
                                    <a href="{{ url($back_url) }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i></a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <div class="d-md-flex gap-4 align-items-center">
                                <div class="d-none d-md-flex"></div>

                                <div class="dropdown ms-auto">
                                    <a href="#" data-bs-toggle="modal"
                                       data-bs-target="#closeStore" class="btn btn-danger"><i
                                            class="fa fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-4">
                                    <label>Select Store</label>
                                    <select name="vendor_id" class="form-control" id="vendor_id">
                                        <option value="" selected>Select Store</option>
                                        @foreach($vendors as $vendor)
                                            <option
                                                value="{{$vendor->id??""}}" {{$vendor_id_selected == $vendor->id ?"selected":""}}>{{$vendor->name??""}}</option>
                                        @endforeach
                                    </select>
                                    <br>
                                    <select name="user_id" class="form-control select2user" id="user_id">
                                        <option value="" selected>Walk-in Customer</option>
                                    </select>
                                    <br>

                                    <div class="form-group mt-3">
                                        <label>Order Type:</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="order_type" id="walkin"
                                                   value="walk_in" checked>
                                            <label class="form-check-label" for="walkin">Walk In</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="order_type" id="delivery"
                                                   value="delivery">
                                            <label class="form-check-label" for="delivery">Delivery</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><span class="fw-bold">Last Visited:</span> <span
                                            id="lastVisited">--</span></p>
                                    <p class="mb-1"><span class="fw-bold">Total Purchase:</span> <span
                                            id="totalPurchase">--</span></p>
                                    <p class="mb-1"><span class="fw-bold">Membership Status:</span> <span
                                            id="membershipStatus" class="text-success">--</span></p>
                                    <p class="mb-1"><span class="fw-bold">Membership End Date:</span> <span
                                            id="membershipEndDate">--</span></p>
                                    <p class="mb-1"><span class="fw-bold">NC Cash Balance:</span> <span
                                            id="cashBalance">₹0</span></p>
                                    <p class="mb-1"><span class="fw-bold">Coupon:</span> <span id="coupon">₹0</span></p>
                                    <p class="mb-1"><span class="fw-bold">Last Bill No.:</span> <span
                                            id="lastBillNo">--</span></p>
                                    <p class="mb-1"><span class="fw-bold">Last Bill Amount:</span> <span
                                            id="lastBillAmount">₹0</span></p>
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <a href="#" data-bs-toggle="modal"
                                               data-bs-target="#redeemNCCash">
                                                <div class="card text-center shadow-sm small-card">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-0 small">Redeem NC Cash</h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <a href="#" onclick="getFreebiesProduct()">
                                                <div class="card text-center shadow-sm small-card">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-0 small">Add Freebies</h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <a href="#" onclick="getMembershipPlans()">
                                                <div class="card text-center shadow-sm small-card">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-0 small"> Add Membership</h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
{{--                                        <div class="col-md-12 mt-3">--}}
{{--                                            <a href="#" onclick="applyCredit(event)">--}}
{{--                                                <div class="card text-center shadow-sm small-card" id="creditCard">--}}
{{--                                                    <div class="card-body p-2">--}}
{{--                                                        <h6 class="card-title mb-0 small">--}}
{{--                                                            Add Credit <span id="credit_amount">(₹ 0.00)</span>--}}
{{--                                                        </h6>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </a>--}}
{{--                                        </div>--}}

                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="card mt-3">
                        <div class="card-body pt-0">
                            <div class="row mb-3 mt-3">
                                <div class="col-md-6">
                                    <input type="text" id="product_search" class="form-control"
                                           placeholder="Search Product" autocomplete="off">
                                    <div id="product_suggestions" class="list-group position-absolute"
                                         style="z-index:1000; max-height:200px; overflow-y:auto;"></div>
                                </div>


                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-lg mb-0" id="cartTable">
                                    <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Itemcode</th>
                                        <th style="width: 100px;">Product</th>
                                        <th>Qty</th>
                                        <th>MRP</th>
                                        <th>Selling Price</th>
                                        <th>Member Price</th>
                                        <th>Discount</th>
                                        <th>Unit Cost</th>
                                        <th>Net Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cartBody">
                                    <!-- Dynamic Rows -->
                                    </tbody>
                                </table>
                            </div>


                            <div class="transaction-details mt-10">
                                <div class="transaction-item">
                                    <label id="total_qty">0</label>
                                    <label>Quantity</label>

                                </div>
                                <div class="transaction-item">
                                    <label id="total_mrp">0</label>
                                    <label>MRP</label>

                                </div>
                                <div class="transaction-item">
                                    <label>0</label>
                                    <label>Tax Amount</label>

                                </div>
                                <div class="transaction-item">
                                    <label id="additional_charge_value">0</label>
                                    <a class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                       data-bs-target="#addCharge"><label>Add Charges</label></a>

                                </div>
                                <div class="transaction-item">
                                    <label id="couponDiscountValue">0</label>
                                    <label>Discount</label>

                                </div>
                                <div class="transaction-item">
                                    <label id="nccash_value">0</label>
                                    <label>NC Cash</label>

                                </div>
                                <div class="transaction-item">
                                    <label id="subscription_price_value">0.00</label>
                                    <label>Subscription Price</label>

                                </div>
                                <div class="transaction-item">
                                    <label><input type="number" class="form-control" name="" id="flat_discount"
                                                  style="width: 100px"></label>
                                    <label>Flat Discount (%)</label>

                                </div>
                                <input type="hidden" name="subtotal" value="0" id="subtotal">
                                <input type="hidden" id="original_subtotal" value="0">
                                <div class="transaction-item">
                                    <label style="font-size: 20px;color: #11AEAE;font-weight: 600"
                                           id="subtotal_html">0</label>

                                    <label style="font-size: 20px;color: #11AEAE;font-weight: 600">Amount</label>

                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col p-1">
                                    <div
                                        class="card text-white bg-dark d-flex justify-content-center align-items-center"
                                        style="height: 40px;">
                                        <a href="#" onclick="couponApply()"
                                           class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                            <h5 class="card-title m-0 product-color-white">
                                                <i class="fa fa-columns"></i> Apply Coupon
                                            </h5>
                                        </a>
                                    </div>
                                </div>

                                <div class="col p-1">
                                    <div
                                        class="card payment-option text-white bg-dark d-flex justify-content-center align-items-center"
                                        style="height: 40px;" data-value="Multipay">
                                        <a href="#" onclick="openMultipayModal()"
                                           class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                            <h5 class="card-title m-0 product-color-white">
                                                <i class="fa fa-columns"></i> Multipay
                                            </h5>
                                        </a>
                                    </div>
                                </div>

                                <div class="col p-1">
                                    <div
                                        class="card payment-option text-white bg-dark d-flex justify-content-center align-items-center"
                                        style="height: 40px;" data-value="UPI">
                                        <a href="#"
                                           class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                            <h5 class="card-title m-0 product-color-white">
                                                <i class="fa fa-caret-right"></i><i class="fa fa-caret-right"></i> UPI
                                            </h5>
                                        </a>
                                    </div>
                                </div>

                                <div class="col p-1">
                                    <div
                                        class="card payment-option text-white bg-dark d-flex justify-content-center align-items-center"
                                        style="height: 40px;" data-value="Card">
                                        <a href="#"
                                           class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                            <h5 class="card-title m-0 product-color-white">
                                                <i class="fa fa-credit-card"></i> Card
                                            </h5>
                                        </a>
                                    </div>
                                </div>

                                <div class="col p-1">
                                    <div
                                        class="card payment-option text-white bg-dark d-flex justify-content-center align-items-center"
                                        style="height: 40px;" data-value="Cash">
                                        <a href="#"
                                           class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                            <h5 class="card-title m-0 product-color-white">
                                                <i class="fa fa-inr currency_style"></i> Cash
                                            </h5>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div id="payment_method_html" class="text-end"></div>
                                </div>

                            </div>
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="submit" class="btn btn-primary" onclick="save_print()">Save & Print
                                    </button>
                                    <a href="" class="btn btn-danger"
                                       onclick="return confirm('Are You Sure Want To Rest?')">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="modal fade" id="applyCouponModal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Apply Coupon </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>Invoice Balance: <span id="invoiceBalance"></span></h6>
                        <table class="table" id="couponTable">
                            <thead>
                            <tr>
                                <th>Coupon Name</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
        <input type="hidden" id="appliedFreebieId" name="freebie_id" value="">
        <input type="hidden" id="appliedCouponId" name="coupon_id" value="">
        <input type="hidden" id="appliedSubscriptionId" name="subscription_id" value="">
        <input type="hidden" id="appliedSubscriptionPrice" name="subscription_price" value="">
        <input type="hidden" id="appliedCoupon" name="appliedCoupon" value="">
        <input type="hidden" id="couponDiscount" name="couponDiscount" value="">
        <input type="hidden" id="ncCash" name="ncCash" value="">
        <input type="hidden" id="maxncCashBalance" name="maxncCashBalance" value="">
        <input type="hidden" id="appliedncCash" name="appliedncCash" value="">
        <input type="hidden" id="userPhone" name="userPhone" value="">
        <input type="hidden" id="membership_active" name="membership_active" value="">
        <input type="hidden" id="payment_method" name="payment_method" value="">
        <input type="hidden" id="payment_method_values" name="payment_method_values" value="">
        <input type="hidden" id="flatDiscountValue" name="flatDiscountValue" value="">
        <input type="hidden" id="flat_discount_percent" name="flat_discount_percent" value="">
        <input type="hidden" id="credit_balance" name="credit_balance" value="">
        <input type="hidden" id="is_applied_credit_balance" name="is_applied_credit_balance" value="0">


        <input type="hidden" id="cashback_wallet_use" name="cashback_wallet_use"
               value="{{$settings->cashback_wallet_use??0}}">
        <input type="hidden" id="is_print" name="is_print" value="0">


        <div class="modal fade" id="FreeBeisModal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Freebies </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="freebiesTable">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Dynamic rows will be appended here -->
                            </tbody>
                        </table>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Subscription </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="subscriptionTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                                <th>MRP</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Dynamic rows will be appended here -->
                            </tbody>
                        </table>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="multiplayModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pay</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <h6>Total: <span id="modalTotal">0</span></h6>

                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6"><label class="fw-bold mb-0">Cash</label></div>
                            <div class="col-md-6">
                                <input type="number" class="form-control pay-input" id="cash"
                                       placeholder="Enter Amount">
                            </div>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6"><label class="fw-bold mb-0">Card</label></div>
                            <div class="col-md-6">
                                <input type="number" class="form-control pay-input" id="card"
                                       placeholder="Enter Amount">
                            </div>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6"><label class="fw-bold mb-0">UPI</label></div>
                            <div class="col-md-6">
                                <input type="number" class="form-control pay-input" id="upi" placeholder="Enter Amount">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="submitPayment()">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="redeemNCCash" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Redeem Loyalty Point</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <h6>Available NC Cash: <span id="ncCashBalance">0</span></h6>
                            <h6>Max Applied NC Cash: <span id="maxncCashBalanceShow">0</span></h6>
                            <!-- Enter Points + Button -->
                            <div class="col-md-6 mt-3">
                                <label>Enter Points</label>
                                <div class="input-group">
                                    <input type="number" id="nc_cash_val" class="form-control"
                                           placeholder="Enter Points">
                                    <a type="button" class="btn btn-primary btn-sm" onclick="sendOTPNCCashRedeem()">Send
                                        OTP</a>
                                </div>
                            </div>

                            <!-- Enter OTP + Button -->
                            <div class="col-md-6 mt-3">
                                <label>Enter OTP</label>
                                <div class="input-group">
                                    <input type="text" id="nc_cash_otp" class="form-control" placeholder="Enter OTP">
                                    <a type="button" class="btn btn-primary btn-sm" onclick="verifyOtpRedeemNCCash()">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="addCharge" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Additional Charge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                Additional Charge
                            </div>
                            <div class="col-md-6 mt-3">
                                Value
                            </div>
                            <div class="col-md-6 mt-3">
                                <input type="text" value="" class="form-control" name="addtitional_charge_title"
                                       id="addtitional_charge_title">
                            </div>
                            <div class="col-md-6 mt-3">
                                <input type="number" value="" class="form-control" name="addtitional_charge"
                                       id="addtitional_charge" onkeyup="calculateTotal()">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>


    </form>
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="addCustomerForm" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                       placeholder="Enter customer name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone"
                                       placeholder="Enter phone number" maxlength="10"
                                       pattern="\d{10}"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                >
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email"
                                       placeholder="Enter email (optional)">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateCashModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cash In Hand details</h5>
                </div>
                <form action="{{ route('pos.update_pos_daily_cash') }}" method="post">
                    @csrf
                    <input type="hidden" name="date" value="{{date('Y-m-d')}}">
                    <div class="modal-body">
                        <div class="row">
                            @if(empty($vendor_id_selected))
                                <label>Select Store</label>
                                <select name="store_id" class="form-control">
                                    <option value="" selected>Select Store</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{$vendor->id??""}}" {{$vendor_id_selected == $vendor->id ?"selected":""}}>{{$vendor->name??""}}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="store_id" value="{{$vendor_id_selected}}">
                            @endif
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Cash In Hand
                                </label>
                                <input type="text" class="form-control" name="today_balance"
                                       value="" placeholder="Cash In Hand">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Start Selling</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <form id="" action="" method="post">
        @csrf
        <input type="hidden" name="user_id" value="" id="user_id">
        <input type="hidden" name="total_amount" value="" id="total_amount">

    </form>
    <div id="qr-reader" style="width:300px; margin-bottom:15px;"></div>

    <!-- Include html5-qrcode library just before your custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>


    <script>


        let rowCount = 0;
        const products = @json(\App\Helpers\CustomHelper::getProductsWithVarients());
        $(document).ready(function () {
            const $input = $('#product_search');
            const $suggestions = $('#product_suggestions');

            $input.on('input', function () {
                const query = $(this).val().toLowerCase().trim();
                $suggestions.empty();

                if (!query) return;

                const matches = products.filter(p => {
                    const name = (p.product_name || '').toString().toLowerCase();
                    const sku = (p.product_sku || '').toString().toLowerCase();
                    return name.includes(query) || sku.includes(query);
                });

                matches.forEach(p => {
                    const text = `${p.product_sku} - ${p.product_name}${p.unit ? ' (' + p.unit + ')' : ''} - ₹${p.selling_price}`;
                    const $item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
                    $item.text(text);
                    $item.data('product', p);
                    $suggestions.append($item);
                });
            });
            $input.on('keypress', function (e) {
                console.log(e.which);
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const code = $input.val().trim();
                    if (code) {
                        searchAndAdd(code); // Add product automatically
                        $input.val(''); // Clear box after scan
                    }
                }
            });
            $input.on('keyup', function (e) {
                const code = $input.val().trim();
                if (code) {
                    var match_val = searchAndAdd(code); // Add product automatically
                    console.log("match_val" + match_val);
                    if (match_val == true || match_val == "true") {
                        $input.val(''); // Clear box after scan
                    } else {
                        $input.val(code); // Clear box after scan
                    }

                }

            });

            function searchAndAdd(skuOrName) {
                const query = skuOrName.toLowerCase().trim();
                const match = products.find(p => (p.product_sku || '').toLowerCase() === query);
                var match_val = false;
                if (match) {
                    match_val = true;
                    let product = {
                        id: match.product_id,
                        variant_id: match.variant_id,
                        code: match.product_sku,
                        name: match.product_name,
                        selling_price: parseFloat(match.selling_price),
                        unit: match.unit,
                        type: "product",
                        discount: parseFloat(match.discount),
                        varient_sku: match.varient_sku,
                        membership_price: parseFloat(match.subscription_price),
                        mrp: parseFloat(match.mrp)
                    };
                    addProductRow(product); // Add to cart
                    $('#product_search').val(''); // Clear search box
                    $('#product_suggestions').empty();
                }
                return match_val;
            }


            // Click on suggestion
            $suggestions.on('click', '.list-group-item', function (e) {
                e.preventDefault();
                const p = $(this).data('product');
                console.log(p);
                let product = {
                    id: p.product_id,
                    variant_id: p.variant_id,
                    code: p.product_sku,
                    name: p.product_name,
                    selling_price: parseFloat(p.selling_price),
                    unit: p.unit,
                    type: "product",
                    discount: parseFloat(p.discount),
                    varient_sku: p.varient_sku,
                    membership_price: parseFloat(p.subscription_price),
                    mrp: parseFloat(p.mrp)
                };

                addProductRow(product);

                // Clear search box & suggestions
                $input.val('');
                $suggestions.empty();
            });

            // Optional: close suggestions on outside click
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#product_search, #product_suggestions').length) {
                    $suggestions.empty();
                }
            });
        });

        // Function to add row
        function addProductRow(product) {
            // Check if the product already exists in the cart
            let $existingRow = $(`#cartBody tr[data-product-id="${product.id}"][data-variant-id="${product.variant_id || 0}"]`);

            if ($existingRow.length) {
                // Product exists → increase qty by 1
                let $qtyInput = $existingRow.find(".qty");
                let qty = parseInt($qtyInput.val()) || 1;
                $qtyInput.val(qty + 1);
            } else {
                // Product does not exist → add new row
                rowCount++;
                let row = `<tr data-id="${rowCount}"
                    data-product-id="${product.id}"
                    data-variant-id="${product.variant_id || 0}"
                    data-subscription-price="${product.membership_price}" data-selling-price="${product.selling_price}"  data-type="${product.type}">
            <td>${rowCount}</td>
            <td>${product.code}</td>
            <td>${product.name} <br> ${product.unit}</td>
            <td>
                <div class="input-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">-</button>
                    <input type="text" class="form-control text-center qty" value="1" style="width:50px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
                </div>
            </td>
            <td class="mrp">${product.mrp.toFixed(2)}</td>
            <td class="selling_price">${product.selling_price.toFixed(2)}</td>
            <td class="membership_price">${product.membership_price.toFixed(2)}</td>
            <td class="discount">${product.discount.toFixed(2)}</td>
            <td class="unit-cost">${product.selling_price.toFixed(2)}</td>
            <td class="net-amount">${product.selling_price.toFixed(2)}</td>
            <td><a class="btn btn-danger btn-sm remove-row">X</a></td>
        </tr>`;
                $("#cartBody").append(row);
            }

            // Recalculate totals
            recalc();
        }


        // Quantity +/- handler
        $(document).on("click", ".qty-plus", function () {
            let input = $(this).siblings(".qty");
            input.val(parseInt(input.val()) + 1);
            recalc();
        });
        $(document).on("click", ".qty-minus", function () {
            let input = $(this).siblings(".qty");
            let val = parseInt(input.val());
            if (val > 1) input.val(val - 1);
            recalc();
        });

        // Discount & Qty handler
        $(document).on("input", ".discount, .add-disc, .qty", function () {
            recalc();
        });

        // Remove row
        $(document).on("click", ".remove-row", function () {
            $(this).closest("tr").remove();
            recalc();
        });

        // Recalculate totals
        // Recalculate totals
        function recalc() {
            let subtotal = 0; // initialize subtotal
            let total_qty = 0; // initialize subtotal
            let total_mrp = 0; // initialize subtotal
            var membership_active = $('#membership_active').val();
            var is_active_member = 0;
            if (membership_active == "Subscribed") {
                is_active_member = 1;
            }
            $("#cartBody tr").each(function () {
                let qty = parseFloat($(this).find(".qty").val());
                let mrp = parseFloat($(this).find(".mrp").text());
                let selling_price = parseFloat($(this).find(".selling_price").text());
                let membership_price = parseFloat($(this).find(".membership_price").text());
                let discount = parseFloat($(this).find(".discount").text());
                let addDisc = parseFloat($(this).find(".add-disc").val()) || 0;


                let unitCost = 0;
                let netAmount = 0;
                let totalAmount = 0;
                if (is_active_member == 1) {
                    unitCost = membership_price;
                } else {
                    unitCost = mrp - discount;
                    unitCost -= (unitCost * addDisc / 100);
                }
                netAmount = unitCost * qty;
                totalAmount = mrp * qty;
                $(this).find(".unit-cost").text(unitCost.toFixed(2));
                $(this).find(".net-amount").text(netAmount.toFixed(2));

                subtotal += netAmount; // add to subtotal
                total_qty += qty; // add to subtotal
                total_mrp += parseInt(totalAmount); // add to subtotal
            });
            // var additionalCharge = parseFloat($('#addtitional_charge').val()) || 0;
            // subtotal += additionalCharge;
            // Display subtotal
            $("#subtotal_html").html(subtotal.toFixed(2));
            $("#subtotal").val(subtotal.toFixed(2));
            $("#total_qty").html(total_qty);
            $("#total_mrp").html(total_mrp);
            $("#original_subtotal").val(subtotal.toFixed(2));
            $("#total_amount").val(subtotal.toFixed(2));
            calculateTotal();
        }

        $("#flat_discount").keyup(function () {
            var flat_discount = parseInt($('#flat_discount').val()) || 0;
            if (flat_discount <= 100) {
                calculateTotal();
            } else {
                $('#flat_discount').val('');
                alert('Max Discount Percent is 100');
            }

        });


        function calculateTotal() {
            var originalSubtotal = parseFloat($('#original_subtotal').val()) || 0;
            var additionalCharge = parseFloat($('#addtitional_charge').val()) || 0;
            var subscriptionPrice = parseFloat($('#appliedSubscriptionPrice').val()) || 0;
            var couponDiscount = parseFloat($('#couponDiscount').val()) || 0;
            var appliedncCash = parseFloat($('#appliedncCash').val()) || 0;
            var flatDiscount = parseFloat($('#flat_discount').val()) || 0;

            var total = originalSubtotal + additionalCharge + subscriptionPrice - couponDiscount - appliedncCash;

            var flatDiscountValue = (parseInt(total) * parseInt(flatDiscount)) / 100;

            total = total - parseInt(flatDiscountValue);
            $('#flat_discount_percent').val(flatDiscount);
            $('#flatDiscountValue').val(flatDiscountValue);
            // Update display
            $("#subtotal_html").html(total.toFixed(2));

            var cashbackWallet = parseFloat($('#ncCash').val()) || 0; // total cashback available
            var cashbackWalletUse = parseFloat($('#cashback_wallet_use').val()) || 0; // % usage allowed

            var appliedCashback = 0;
            if (cashbackWallet > 0 && cashbackWalletUse > 0) {
                appliedCashback = (total * cashbackWalletUse) / 100;
                if (appliedCashback > cashbackWallet) {
                    appliedCashback = cashbackWallet;
                }
                appliedCashback = parseFloat(appliedCashback.toFixed(2));
                $('#maxncCashBalanceShow').html(appliedCashback);
                $('#maxncCashBalance').val(appliedCashback);
            }


            $("#total_amount").val(total.toFixed(2));
            $("#additional_charge_value").html(additionalCharge.toFixed(2));
        }


    </script>


    @if(empty($exist))
        <script>
            $(document).ready(function () {
                $('#updateCashModal').modal('show');
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            $('#addCustomerForm').on('submit', function (e) {
                e.preventDefault(); // stop normal form submit

                $.ajax({
                    url: "{{ route('users.save_user') }}", // Laravel route
                    type: "POST",
                    data: $(this).serialize(), // send form data
                    success: function (response) {
                        if (response.success) {
                            // close modal
                            $('#addUserModal').modal('hide');
                            $('#addCustomerForm')[0].reset();
                            // show success message (optional)
                            //alert('Customer added successfully!');
                            // Add new user to select2 (if exists)
                            let newOption = new Option(response.user.phone + ' - ' + response.user.name, response.user.id, true, true);

                            getUserDetails(response.user.id);
                            $('.select2user').append(newOption).trigger('change');
                        } else {
                            //alert('Something went wrong!');
                        }
                    },
                    error: function (xhr) {
                        //alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });


    </script>
    <script>
        $(document).on('click', '.apply-btn', function () {
            let btn = $(this);
            let id = btn.data('id');
            let type = btn.data('type');
            console.log("ID:", id);
            console.log("Type:", type);
            if (btn.hasClass('btn-success')) {
                if (type == 'freebies') {
                    var _token = '{{ csrf_token() }}';
                    $.ajax({
                        url: "{{ route('pos.getFreebiesProductDetails') }}",
                        type: "POST",
                        data: {id: id},
                        dataType: "json",
                        headers: {'X-CSRF-TOKEN': _token},
                        success: function (p) {
                            $("#cartBody tr[data-type='freebies']").remove();
                            console.log(p);
                            let product = {
                                id: p.id,
                                variant_id: 0,
                                code: p.sku,
                                name: p.name,
                                selling_price: 0,
                                unit: "",
                                discount: 0,
                                varient_sku: 0,
                                membership_price: 0,
                                type: "freebies",
                                mrp: 0
                            };
                            addProductRow(product);
                        }
                    });
                }
                if (type == 'coupon') {
                    var _token = '{{ csrf_token() }}';
                    var user_id = $('#user_id').val();
                    var total_amount = $('#original_subtotal').val();
                    let items = [];
                    $('#appliedCoupon').val("");
                    $('#couponDiscount').val("");
                    $('#couponDiscountValue').html(0);
                    $("#cartBody tr").each(function () {
                        items.push({
                            product_id: $(this).data('product-id'),
                            variant_id: $(this).data('variant-id'),
                            type: $(this).data('type'),
                            qty: $(this).find('.qty').val(),
                            price: $(this).find('.unit-cost').text(),
                            net_price: $(this).find('.net-amount').text(),
                            subscription_price: $(this).data('subscription-price') || null,
                            net_subscription_price: null
                        });
                    });
                    $.ajax({
                        url: "{{ route('pos.applyCoupon') }}",
                        type: "POST",
                        data: {id: id, total_amount: total_amount, user_id: user_id, items: items},
                        dataType: "JSON",
                        headers: {'X-CSRF-TOKEN': _token},
                        success: function (response) {
                            if (response.result === true) {
                                $('#appliedCoupon').val(response.coupon_code);
                                $('#couponDiscount').val(response.coupon_discount);
                                $('#couponDiscountValue').html(response.coupon_discount.toFixed(2));
                                calculateTotal();
                            } else {
                                // ❌ Coupon not applied
                                alert(response.message);
                                btn.removeClass('btn-danger')
                                    .addClass('btn-success')
                                    .text('Apply');
                                calculateTotal();
                            }
                        }
                    });
                }


                if (type === 'freebies') {
                    $('#appliedFreebieId').val(id);
                }
                if (type === 'coupon') {
                    $('#appliedCouponId').val(id);
                }
                if (type === 'subscription') { // typo fixed: subscription
                    let duration = btn.data('duration');
                    let mrp = btn.data('mrp');
                    let price = btn.data('price');
                    $('#subscription_price_value').html(price.toFixed(2));
                    $('#appliedSubscriptionPrice').val(price);
                    $('#appliedSubscriptionId').val(id);
                    $('#membership_active').val('Subscribed');
                    calculateTotal();
                }

                // Reset all other buttons
                $('.apply-btn')
                    .removeClass('btn-danger')
                    .addClass('btn-success')
                    .text('Apply');

                // Change this one to Remove
                btn.removeClass('btn-success')
                    .addClass('btn-danger')
                    .text('Remove');

                // Optionally: store applied coupon name
                let couponName = btn.closest('tr').find('td:first').text();
                console.log("Applied Coupon:", couponName);

            } else {

                if (type === 'freebies') {
                    $('#appliedFreebieId').val('');
                }
                if (type === 'coupon') {
                    $('#appliedCouponId').val('');
                    $('#appliedCoupon').val("");
                    $('#couponDiscount').val("");
                    $('#couponDiscountValue').html(0);
                    calculateTotal();
                }
                if (type === 'subscription') {
                    $('#subscription_price_value').html('0.0');
                    $('#appliedSubscriptionPrice').val(0);
                    $('#membership_active').val('Not Subscribed');
                    calculateTotal();
                    $('#appliedSubscriptionId').val('');
                }
                // Remove coupon
                btn.removeClass('btn-danger')
                    .addClass('btn-success')
                    .text('Apply');

                console.log("Coupon removed");
            }
        });

    </script>

    <script>
        function getFreebiesProduct() {
            var cart_price = $('#original_subtotal').val();
            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('pos.getFreebiesProduct') }}",
                type: "POST",
                data: {cart_price: cart_price},
                dataType: "json",
                headers: {'X-CSRF-TOKEN': _token},
                success: function (data) {
                    var tbody = $('#freebiesTable tbody');
                    tbody.empty(); // Clear previous rows

                    if (data.length > 0) {
                        $.each(data, function (index, item) {
                            var row = '<tr>' +
                                '<td><img src="' + item.image + '" alt="' + item.product_name + '" style="width:50px;height:50px;"></td>' +
                                '<td>' + item.product_name + '</td>' +
                                '<td><a class="btn btn-success btn-sm apply-btn" data-id="' + item.id + '" data-type="freebies">Apply</a></td>' +
                                '</tr>';
                            tbody.append(row);
                        });

                        $('#FreeBeisModal').modal('show');
                    } else {
                        tbody.append('<tr><td colspan="3">No freebies available for this cart amount.</td></tr>');
                        $('#FreeBeisModal').modal('show');
                    }
                }
            });
        }

    </script>

    <script>
        function couponApply() {
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('pos.getCoupons') }}",
                type: "POST",
                data: {},
                dataType: "json",
                headers: {'X-CSRF-TOKEN': _token},
                success: function (data) {
                    var tbody = $('#couponTable tbody');
                    tbody.empty(); // Clear previous rows

                    if (data.length > 0) {
                        $.each(data, function (index, item) {
                            var appliedCouponId = $('#appliedCouponId').val();
                            if (appliedCouponId == item.id) {
                                var row = '<tr>' +
                                    '<td>' + item.offer_code + '</td>' +
                                    '<td><a class="btn btn-danger btn-sm apply-btn" data-id="' + item.id + '" data-type="coupon">Remove</a></td>' +
                                    '</tr>';
                                tbody.append(row);
                            } else {
                                var row = '<tr>' +
                                    '<td>' + item.offer_code + '</td>' +
                                    '<td><a class="btn btn-success btn-sm apply-btn" data-id="' + item.id + '" data-type="coupon">Apply</a></td>' +
                                    '</tr>';
                                tbody.append(row);
                            }
                        });

                        $('#applyCouponModal').modal('show');
                    } else {
                        tbody.append('<tr><td colspan="3">No freebies available for this cart amount.</td></tr>');
                        $('#applyCouponModal').modal('show');
                    }
                }
            });
        }

    </script>
    <script>
        function getMembershipPlans() {
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('pos.getMembershipPlans') }}",
                type: "POST",
                data: {user_id: $('#user_id').val()},
                dataType: "json",
                headers: {'X-CSRF-TOKEN': _token},
                success: function (data) {
                    var appliedSubscriptionId = $('#appliedSubscriptionId').val();
                    var tbody = $('#subscriptionTable tbody');
                    tbody.empty(); // Clear previous rows

                    if (data.length > 0) {

                        $.each(data, function (index, item) {
                            if (appliedSubscriptionId == item.id) {
                                var row = '<tr>' +
                                    '<td>' + item.name + '</td>' +
                                    '<td>' + item.duration + ' Months</td>' +
                                    '<td>' + item.mrp + '</td>' +
                                    '<td>' + item.price + '</td>' +
                                    '<td><a class="btn btn-danger btn-sm apply-btn" data-id="' + item.id + '"  data-duration="' + item.duration + '"  data-mrp="' + item.mrp + '" data-price="' + item.price + '" data-type="subscription">Remove</a></td>' +
                                    '</tr>';
                            } else {
                                var row = '<tr>' +
                                    '<td>' + item.name + '</td>' +
                                    '<td>' + item.duration + ' Months</td>' +
                                    '<td>' + item.mrp + '</td>' +
                                    '<td>' + item.price + '</td>' +
                                    '<td><a class="btn btn-success btn-sm apply-btn" data-id="' + item.id + '"  data-duration="' + item.duration + '"  data-mrp="' + item.mrp + '" data-price="' + item.price + '" data-type="subscription">Apply</a></td>' +
                                    '</tr>';
                            }

                            tbody.append(row);
                        });

                        $('#subscriptionModal').modal('show');
                    } else {
                        tbody.append('<tr><td colspan="3">No Subscription available for this.</td></tr>');
                        $('#subscriptionModal').modal('show');
                    }
                }
            });
        }

    </script>

    <script>

        function save_print() {
            $('#is_print').val(1);
        }


        $('#posForm').on('submit', function (e) {
            e.preventDefault();
            let $submitBtn = $(this).find('button[type="submit"], input[type="submit"]');
            if ($submitBtn.prop('disabled')) return; // already clicked, do nothing
            $submitBtn.prop('disabled', true);

            let orderType = $('input[name="order_type"]:checked').val();
            let items = [];
            $("#cartBody tr").each(function () {
                items.push({
                    product_id: $(this).data('product-id'),
                    variant_id: $(this).data('variant-id'),
                    type: $(this).data('type'),
                    qty: $(this).find('.qty').val(),
                    price: $(this).find('.unit-cost').text(),
                    net_price: $(this).find('.net-amount').text(),
                    subscription_price: $(this).data('subscription-price') || null,
                    net_subscription_price: null
                });
            });

            $.ajax({
                url: "{{ route('pos.savePos') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: $('#user_id').val(),
                    subtotal: $('#subtotal').val(),
                    vendor_id: $('#vendor_id').val(),
                    payment_method: $('#payment_method').val(),
                    coupon_code: $('#appliedCoupon').val() || '',
                    coupon_discount: $('#couponDiscount').val() || 0,
                    delivery_charges: $('#addtitional_charge').val() || 0,
                    items: items,
                    freebie_id: $('#appliedFreebieId').val(),
                    coupon_id: $('#appliedCouponId').val(),
                    subscription_id: $('#appliedSubscriptionId').val(),
                    appliedSubscriptionPrice: $('#appliedSubscriptionPrice').val(),
                    appliedCoupon: $('#appliedCoupon').val(),
                    couponDiscount: $('#couponDiscount').val(),
                    ncCash: $('#ncCash').val(),
                    appliedncCash: $('#appliedncCash').val(),
                    userPhone: $('#userPhone').val(),
                    payment_method_values: $('#payment_method_values').val(),
                    is_print: $('#is_print').val(),
                    flatDiscountValue: $('#flatDiscountValue').val(),
                    flat_discount_percent: $('#flat_discount_percent').val(),
                    is_applied_credit_balance: $('#is_applied_credit_balance').val(),
                    order_type: orderType
                },
                success: function (res) {
                    if (res.success) {
                        alert('Order Saved. Order ID: ' + res.order_id);
                        if (res.invoice_url) {
                            window.open(res.invoice_url, '_blank'); // Opens in a new tab
                        }
                        window.location.reload();
                    } else {
                        alert('Error: ' + res.message);
                        $submitBtn.prop('disabled', false); // Re-enable on error
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                    $submitBtn.prop('disabled', false); // Re-enable on error
                }
            });
        });


        function sendOTPNCCashRedeem() {
            var nc_cash_val = $('#nc_cash_val').val();
            var nc_cash = $('#maxncCashBalance').val();
            var userPhone = $('#userPhone').val();
            if (parseInt(nc_cash_val) <= parseInt(nc_cash)) {
                var _token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('pos.send_redeem_nc_cash_otp') }}",
                    type: "POST",
                    data: {userPhone: userPhone, nc_cash_val: nc_cash_val},
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': _token},
                    success: function (data) {
                        $('#nc_cash_val').prop('readonly', true);
                    }
                });
            } else {
                alert('You Can Apply Max - ' + nc_cash);
            }
        }

        function verifyOtpRedeemNCCash() {
            var nc_cash_val = $('#nc_cash_val').val();
            var nc_cash = $('#maxncCashBalance').val();
            var userPhone = $('#userPhone').val();
            var nc_cash_otp = $('#nc_cash_otp').val();
            if (parseInt(nc_cash_val) <= parseInt(nc_cash)) {
                var _token = '{{ csrf_token() }}';
                $.ajax({
                    url: "{{ route('pos.verify_redeem_nc_cash_otp') }}",
                    type: "POST",
                    data: {userPhone: userPhone, nc_cash_val: nc_cash_val, nc_cash_otp: nc_cash_otp},
                    dataType: "JSON",
                    headers: {'X-CSRF-TOKEN': _token},
                    success: function (data) {
                        if (data.success) {
                            $('#nccash_value').html(parseInt(nc_cash_val).toFixed(2));
                            $('#appliedncCash').val(nc_cash_val);
                            calculateTotal();
                            $('#redeemNCCash').modal('hide');
                        } else {
                            alert('Invalid OTP');
                        }
                    }
                });
            } else {
                alert('You Can Apply Max - ' + nc_cash);
            }
        }


        $(document).on('click', '.payment-option', function (e) {
            e.preventDefault();

            // Remove active class from all
            $('.payment-option').removeClass('active bg-primary').addClass('bg-dark');

            // Add active class to clicked one
            $(this).removeClass('bg-dark').addClass('active');

            // Get selected value
            let selectedValue = $(this).data('value');
            console.log("Selected Payment Method:", selectedValue);

            // Store in hidden input
            $('#payment_method').val(selectedValue);
            var totalAmount = parseFloat($('#total_amount').val()) || 0;
            let html = "";
            if (selectedValue == 'UPI') html += `<div><strong>UPI:</strong> ₹${totalAmount.toFixed(2)}</div>`;
            if (selectedValue == 'Card') html += `<div><strong>Card:</strong> ₹${totalAmount.toFixed(2)}</div>`;
            if (selectedValue == 'Cash') html += `<div><strong>Cash:</strong> ₹${totalAmount.toFixed(2)}</div>`;

            // Inject into right side div
            $("#payment_method_html").html(html);
        });


        let totalAmount = 0;

        function openMultipayModal() {
            totalAmount = parseFloat($('#total_amount').val()) || 0;
            $("#modalTotal").text(totalAmount.toFixed(2));

            // Reset values
            $("#cash, #card, #upi").val("");

            // By default cash = total
            $("#cash").val(totalAmount.toFixed(2));
            $("#card").val("0.00");
            $("#upi").val("0.00");

            $('#multiplayModal').modal('show');
        }

        // Always keep UPI as balancing field
        $(".pay-input").on("input", function () {
            let cash = parseFloat($("#cash").val()) || 0;
            let card = parseFloat($("#card").val()) || 0;

            // Remaining goes to UPI
            let remaining = totalAmount - (cash + card);

            $("#upi").val(Math.max(remaining, 0).toFixed(2));
        });

        function submitPayment() {
            let cash = parseFloat($("#cash").val()) || 0;
            let card = parseFloat($("#card").val()) || 0;
            let upi = parseFloat($("#upi").val()) || 0;

            if ((cash + card + upi).toFixed(2) != totalAmount.toFixed(2)) {
                alert("Amounts do not match total!");
                return;
            }
            let html = "";
            if (cash > 0) html += `<div><strong>Cash:</strong>₹ ${cash.toFixed(2)}</div>`;
            if (card > 0) html += `<div><strong>Card:</strong>₹ ${card.toFixed(2)}</div>`;
            if (upi > 0) html += `<div><strong>UPI:</strong>₹ ${upi.toFixed(2)}</div>`;

            let paymentJson = {
                cash: cash,
                card: card,
                upi: upi
            };
            // Inject into payment method div
            $("#payment_method_html").html(html);
            $("#payment_method_values").val(JSON.stringify(paymentJson));

            console.log("Cash:", cash, "Card:", card, "UPI:", upi);
            $('#multiplayModal').modal('hide');
        }

    </script>

    <script>
        function applyCredit(e) {
            e.preventDefault(); // prevent link from jumping
            var creditCard = document.getElementById('creditCard');
            var isApplied = document.getElementById('is_applied_credit_balance');

            if (isApplied.value == "0") {
                // Apply color
                creditCard.style.backgroundColor = "#11AEAE";
                creditCard.style.color = "white";
                isApplied.value = "1";
            } else {
                // Remove color
                creditCard.style.backgroundColor = "";
                creditCard.style.color = "";
                isApplied.value = "0";
            }
        }
    </script>


    <script>
        function checkCashDifference() {
            const totalCash = parseFloat(document.getElementById('total_cash_display').innerText) || 0;
            const physicalCash = parseFloat(document.getElementById('today_last_balance').value) || 0;
            const remarkEl = document.getElementById('cash_remark');

            const difference = physicalCash - totalCash;

            if (difference < 0) {
                remarkEl.innerHTML = `Short : ₹ ${Math.abs(difference).toFixed(2)}`;
                remarkEl.style.color = 'red';
            } else if (difference > 0) {
                remarkEl.innerHTML = `Excess : ₹ ${difference.toFixed(2)}`;
                remarkEl.style.color = 'green';
            } else {
                remarkEl.innerHTML = `Matched ✅`;
                remarkEl.style.color = 'blue';
            }
        }


    </script>

    <script>
        $('#closePos').on('submit', function (e) {
            e.preventDefault();
            const physicalCash = parseFloat($('#today_last_balance').val()) || 0;
            // Prepare data
            let data = {
                _token: $('input[name="_token"]').val(),
                today_last_balance: physicalCash,
                closing_note: $('input[name="closing_note"]').val(),
                store_id: $('input[name="store_id"]').val() || null
            };

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: function (res) {
                    if (res.status) {
                        alert(res.message || 'POS closed successfully!');
                        $('#closePos')[0].reset();
                        $('#cash_remark').text('');
                        location.reload();
                    } else {
                        alert('Error: ' + (res.message || 'Failed to close POS.'));
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Something went wrong while closing POS!');
                },
                complete: function () {

                }
            });
        });

    </script>

@endsection
