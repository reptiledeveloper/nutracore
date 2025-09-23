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
            display: flex;          /* arrange items horizontally */
            gap: 20px;              /* optional spacing between items */
        }

        .transaction-item {
            padding: 0 10px;        /* space inside each item */
            border-right: 1px solid #ccc;  /* vertical line */
        }

        /* Remove the border from the last item */
        .transaction-item:last-child {
            border-right: none;
        }
        .transaction-details {
            display: flex;              /* horizontal layout */
        }

        .transaction-item {
            flex: 1;                    /* equal width for all items */
            text-align: center;         /* center text */
            /* light gray background */
            padding: 10px 0;            /* top/bottom padding */
            border-right: 1px solid #ccc;  /* vertical line */
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="#" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $id }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select name="product" id="product_dropdown" class="form-control ">
                                        <option value="">Select Product</option>
                                        @foreach(\App\Helpers\CustomHelper::getProductsWithVarients() as $p)
                                            <option value="{{ $p['varient_id'] }}">
                                                {{ $p['product_sku'] }} - {{ $p['product_name'] }}
                                                @if($p['unit'])
                                                    ({{ $p['unit'] }})
                                                @endif
                                                - ₹{{ $p['selling_price'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="customer_id" class="form-control">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" id="invoice_search" class="form-control" placeholder="Scan Sales Invoice">
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
                                    <th>Discount</th>
                                    <th>Add Disc %</th>
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
                                    <label>Quantity</label>
                                    <label id="total_qty">0</label>
                                </div>
                                <div class="transaction-item">
                                    <label>MRP</label>
                                    <label id="total_mrp">0</label>
                                </div>
                                <div class="transaction-item">
                                    <label>Tax Amount</label>
                                    <label>0</label>
                                </div>
                                <div class="transaction-item">
                                    <label>Discount</label>
                                    <label>0</label>
                                </div>
                                <div class="transaction-item">
                                    <label>Flat Discount</label>
                                    <label>0</label>
                                </div>
                                <div class="transaction-item">
                                    <label>Round OFF</label>
                                    <label>0.00</label>
                                </div>
                                <div class="transaction-item">
                                    <label>Amount</label>
                                    <label id="subtotal">0</label>

                                </div>
                            </div>




                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        let rowCount = 0;

        $(document).on("change", "#product_dropdown", function () {
            let varientId = $(this).val();

            if (!varientId) return;

            // Get selected option text
            let option = $(this).find('option:selected');
            let text = option.text();

            // Extract code, name, price from text
            let code = text.split(' - ')[0];
            let namePart = text.split(' - ')[1];
            let name = namePart ? namePart.replace(/\(\d*\)?/,'').trim() : namePart; // remove unit if any
            let priceMatch = text.match(/₹(\d+(\.\d+)?)/);
            let mrp = priceMatch ? parseFloat(priceMatch[1]) : 0;

            let product = {
                id: varientId,
                code: code,
                name: name,
                mrp: mrp
            };

            addProductRow(product);

            // Reset dropdown
            $(this).val(null).trigger('change');
        });

        // Function to add row
        function addProductRow(product) {
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
        <td><input type="text" class="form-control discount" value="0"></td>
        <td><input type="text" class="form-control add-disc" value="0"></td>
        <td class="unit-cost">${product.mrp.toFixed(2)}</td>
        <td class="net-amount">${product.mrp.toFixed(2)}</td>
        <td><button class="btn btn-danger btn-sm remove-row">X</button></td>
    </tr>`;
            $("#cartBody").append(row);
            recalc();
        }

        // Quantity +/- handler
        $(document).on("click", ".qty-plus", function() {
            let input = $(this).siblings(".qty");
            input.val(parseInt(input.val()) + 1);
            recalc();
        });
        $(document).on("click", ".qty-minus", function() {
            let input = $(this).siblings(".qty");
            let val = parseInt(input.val());
            if (val > 1) input.val(val - 1);
            recalc();
        });

        // Discount & Qty handler
        $(document).on("input", ".discount, .add-disc, .qty", function() {
            recalc();
        });

        // Remove row
        $(document).on("click", ".remove-row", function() {
            $(this).closest("tr").remove();
            recalc();
        });

        // Recalculate totals
        // Recalculate totals
        function recalc() {
            let subtotal = 0; // initialize subtotal
            let total_qty = 0; // initialize subtotal
            let total_mrp = 0; // initialize subtotal

            $("#cartBody tr").each(function() {
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
                total_mrp += totalAmount; // add to subtotal
            });

            // Display subtotal
            $("#subtotal").html(subtotal.toFixed(2));
            $("#total_qty").html(total_qty);
            $("#total_mrp").html(total_mrp);
        }


    </script>

@endsection
