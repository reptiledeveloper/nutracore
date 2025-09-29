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


    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $brands = \App\Helpers\CustomHelper::getBrands();

    $products = \App\Helpers\CustomHelper::getProductsWithVarients();
    $customers = [];
    $exist = \App\Models\POSDailyCash::where('date', date('Y-m-d'))->first();
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


    </style>
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
        {{--        <form class="card-body" action="#" method="post" accept-chartset="UTF-8"--}}
        {{--              enctype="multipart/form-data" role="form">--}}
        {{--            {{ csrf_field() }}--}}
        {{--            <input type="hidden" id="id" value="{{ $id }}">--}}
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

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4">
                                <select name="user_id" class="form-control select2user">
                                    <option value="" selected>Walk-in Customer</option>
                                </select>
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
                                    <div class="col-md-4">
                                        <a href="#" data-bs-toggle="modal"
                                           data-bs-target="#redeemNCCash">
                                            <div class="card text-center shadow-sm">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-0">Redeem NC Cash</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="#" onclick="getFreebiesProduct()">
                                            <div class="card text-center shadow-sm">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-0">Add Freebies</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="#" onclick="getMembershipPlans()">
                                            <div class="card text-center shadow-sm">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-0"> Add Membership</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

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

                        <table class="table table-bordered table-striped" id="cartTable">
                            <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Itemcode</th>
                                <th>Product</th>
                                <th style="width: 120px;">Qty</th>
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
                                <label>0</label>
                                <label>Discount</label>

                            </div>
                            <div class="transaction-item">
                                <label>0</label>
                                <label>Flat Discount</label>

                            </div>
                            <div class="transaction-item">
                                <label>0.00</label>
                                <label>Round OFF</label>

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
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#applyCouponModal"
                                       class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                        <h5 class="card-title m-0 product-color-white">
                                            <i class="fa fa-columns"></i> Apply Coupon
                                        </h5>
                                    </a>
                                </div>
                            </div>

                            <div class="col p-1">
                                <div
                                    class="card text-white bg-dark d-flex justify-content-center align-items-center"
                                    style="height: 40px;">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#multiplayModal"
                                       class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                        <h5 class="card-title m-0 product-color-white">
                                            <i class="fa fa-columns"></i> Multipay
                                        </h5>
                                    </a>
                                </div>
                            </div>

                            <div class="col p-1">
                                <div
                                    class="card text-white bg-dark d-flex justify-content-center align-items-center"
                                    style="height: 40px;">
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
                                    class="card text-white bg-dark d-flex justify-content-center align-items-center"
                                    style="height: 40px;">
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
                                    class="card text-white bg-dark d-flex justify-content-center align-items-center"
                                    style="height: 40px;">
                                    <a href="#"
                                       class="text-white text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center">
                                        <h5 class="card-title m-0 product-color-white">
                                            <i class="fa fa-inr currency_style"></i> Cash
                                        </h5>
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group mb-0 mt-3 justify-content-end">
                            <div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--        </form>--}}
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
                    <h6>Invoice Balance: <span id="invoiceBalance">7474</span></h6>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Coupon Name</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>LAUNCHCOUPON250</td>
                            <td>
                                <button class="btn btn-success btn-sm apply-btn">Apply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>PAYDAY500</td>
                            <td>
                                <button class="btn btn-success btn-sm apply-btn">Apply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>PAYDAY10%</td>
                            <td>
                                <button class="btn btn-success btn-sm apply-btn">Apply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CLEARANCE15%</td>
                            <td>
                                <button class="btn btn-success btn-sm apply-btn">Apply</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

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
                    <h5 class="modal-title" id="exampleModalLabel">Add Freebies </h5>
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

    <div class="modal fade" id="multiplayModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pay </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <h6>Pay</h6>
                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6">
                                <label class="fw-bold mb-0">Cash</label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Received Amount</label>
                                <input type="text" class="form-control" placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6">
                                <label class="fw-bold mb-0">Card</label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Received Amount</label>
                                <input type="text" class="form-control" placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="row mt-3 align-items-center">
                            <div class="col-md-6">
                                <label class="fw-bold mb-0">UPI</label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Received Amount</label>
                                <input type="text" class="form-control" placeholder="Enter Amount">
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
                        <h6>Available Points: <span id="ncCashBalance">0</span></h6>

                        <!-- Enter Points + Button -->
                        <div class="col-md-6 mt-3">
                            <label>Enter Points</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter Points">
                                <button type="button" class="btn btn-primary btn-sm">Send OTP</button>
                            </div>
                        </div>

                        <!-- Enter OTP + Button -->
                        <div class="col-md-6 mt-3">
                            <label>Enter OTP</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter OTP">
                                <button type="button" class="btn btn-primary btn-sm">Submit</button>
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
                                <input type="text" class="form-control" name="name" placeholder="Enter customer name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" placeholder="Enter phone number"
                                       required>
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


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


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
                    return p.product_name.toLowerCase().includes(query) || p.product_sku.toLowerCase().includes(query);
                });

                matches.forEach(p => {
                    const text = `${p.product_sku} - ${p.product_name}${p.unit ? ' (' + p.unit + ')' : ''} - ₹${p.selling_price}`;
                    const $item = $('<a href="#" class="list-group-item list-group-item-action"></a>');
                    $item.text(text);
                    $item.data('product', p);

                    $suggestions.append($item);
                });
            });

            // Click on suggestion
            $suggestions.on('click', '.list-group-item', function (e) {
                e.preventDefault();
                const p = $(this).data('product');
                console.log(p);
                let product = {
                    id: p.varient_id,
                    code: p.product_sku,
                    name: p.product_name,
                    selling_price: parseFloat(p.selling_price),
                    unit: p.unit,
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
            console.log(product);
            rowCount++;
            let row = `<tr data-id="${rowCount}">
        <td>${rowCount}</td>
        <td>${product.code}</td>
        <td>${product.name}</td>
        <td>
            <div class="input-group">
                <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">-</button>
                <input type="text" class="form-control text-center qty" value="1" style="width:50px;">
                <button  type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
            </div>
        </td>
        <td class="mrp">${product.mrp.toFixed(2)}</td>
        <td class="mrp">${product.selling_price.toFixed(2)}</td>
         <td class="mrp">${product.membership_price.toFixed(2)}</td>
        <td class="mrp">${product.discount.toFixed(2)}</td>
        <td class="unit-cost">${product.selling_price.toFixed(2)}</td>
        <td class="net-amount">${product.selling_price.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm remove-row">X</button></td>
    </tr>`;
            $("#cartBody").append(row);
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

            $("#cartBody tr").each(function () {
                let qty = parseFloat($(this).find(".qty").val());
                let mrp = parseFloat($(this).find(".mrp").text());
                let discount = parseFloat($(this).find(".discount").val()) || 0;
                let addDisc = parseFloat($(this).find(".add-disc").val()) || 0;

                let unitCost = mrp - discount;
                unitCost -= (unitCost * addDisc / 100);

                let netAmount = unitCost * qty;
                let totalAmount = mrp * qty;


                $(this).find(".unit-cost").text(unitCost.toFixed(2));
                $(this).find(".net-amount").text(netAmount.toFixed(2));

                subtotal += netAmount; // add to subtotal
                total_qty += qty; // add to subtotal
                total_mrp += parseInt(totalAmount); // add to subtotal
            });
            var additionalCharge = parseFloat($('#addtitional_charge').val()) || 0;
            subtotal += additionalCharge;
            // Display subtotal
            $("#subtotal_html").html(subtotal.toFixed(2));
            $("#subtotal").val(subtotal.toFixed(2));
            $("#total_qty").html(total_qty);
            $("#total_mrp").html(total_mrp);
            $("#original_subtotal").val(subtotal.toFixed(2));
        }

        function calculateTotal() {
            var originalSubtotal = parseFloat($('#original_subtotal').val()) || 0;
            var additionalCharge = parseFloat($('#addtitional_charge').val()) || 0;

            var total = originalSubtotal + additionalCharge;
            // Update display
            $("#subtotal_html").html(total.toFixed(2));
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
                            alert('Customer added successfully!');
                            // Add new user to select2 (if exists)
                            let newOption = new Option(response.user.phone + ' - ' + response.user.name, response.user.id, true, true);

                            getUserDetails(response.user.id);
                            $('.select2user').append(newOption).trigger('change');
                        } else {
                            alert('Something went wrong!');
                        }
                    },
                    error: function (xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });


    </script>
    <script>
        $(document).on('click', '.apply-btn', function () {
            let btn = $(this);

            if (btn.hasClass('btn-success')) {
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
                // Remove coupon
                btn.removeClass('btn-danger')
                    .addClass('btn-success')
                    .text('Apply');

                console.log("Coupon removed");
            }
        });

    </script>

    <script>
        function getFreebiesProduct(){
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

                    if(data.length > 0){
                        $.each(data, function(index, item){
                            var row = '<tr>'+
                                '<td><img src="'+item.image+'" alt="'+item.product_name+'" style="width:50px;height:50px;"></td>'+
                                '<td>'+item.product_name+'</td>'+
                                '<td><button class="btn btn-success btn-sm apply-btn" data-id="'+item.id+'">Apply</button></td>'+
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
        function getMembershipPlans(){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('pos.getMembershipPlans') }}",
                type: "POST",
                data: {},
                dataType: "json",
                headers: {'X-CSRF-TOKEN': _token},
                success: function (data) {
                    var tbody = $('#subscriptionTable tbody');
                    tbody.empty(); // Clear previous rows

                    if(data.length > 0){
                        $.each(data, function(index, item){
                            var row = '<tr>'+
                                '<td>'+item.name+'</td>'+
                                '<td>'+item.duration+' Months</td>'+
                                '<td>'+item.mrp+'</td>'+
                                '<td>'+item.price+'</td>'+
                                '<td><button class="btn btn-success btn-sm apply-btn" data-id="'+item.id+'">Apply</button></td>'+
                                '</tr>';
                            tbody.append(row);
                        });

                        $('#subscriptionModal').modal('show');
                    } else {
                        tbody.append('<tr><td colspan="3">No freebies available for this cart amount.</td></tr>');
                        $('#subscriptionModal').modal('show');
                    }
                }
            });
        }

    </script>

@endsection
