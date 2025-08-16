@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();


    $invoices_id = isset($invoices->id) ? $invoices->id : '';
    $invoice_number = isset($invoices->invoice_number) ? $invoices->invoice_number : '';
    $vendor_id = isset($invoices->vendor_id) ? $invoices->vendor_id : '';
    $status = isset($invoices->status) ? $invoices->status : 1;
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $suppliers = \App\Models\Supplier::where('is_delete',0)->get();
    ?>

    <style>
        #itemsTable {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }
        #itemsTable th, #itemsTable td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #itemsTable-wrapper {
            overflow-x: auto;
            width: 100%;
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
                        <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $invoices_id }}">

                            <div class="row">

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Supplier</label>
                                    <select name="supplier_id" class="form-control select2" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Invoice No</label>
                                    <input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number', $invoice_number) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label class="form-label required">Invoice Date</label>
                                    <input type="date" name="invoice_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName" class="form-label">Status<span
                                            class="text-danger">*</span></label>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="1"
                                               <?php echo $status == '1' ? 'checked' : ''; ?> checked>
                                        <label class="form-check-label"
                                               for="customRadioBox1">Active</label>
                                    </div>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="0" <?php echo strlen($status) > 0 && $status == '0' ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                               for="customRadioBox1">InActive</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">

                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2">Items</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">+ Add Item</button>
                                </div>
                                <div id="itemsTable-wrapper">
                                    <table id="itemsTable" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Product</th>
                                            <th>Variant</th>
                                            <th>Batch</th>
                                            <th>MFG</th>
                                            <th>Expiry</th>
                                            <th>Qty</th>
                                            <th>Purchase Price</th>
                                            <th>Total Price</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>

                                        <tfoot>
                                        <tr>
                                            <th colspan="8" class="text-end">Subtotal</th>
                                            <th><input type="text" id="subtotal" class="form-control" readonly></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
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


    <script>
        const products = @json($products);
        // Structure: [{id, name, variants:[{id, varient_sku, unit, selling_price}]}]

        function addRow() {
            let row = `
        <tr>
            <td><input type="text" name="sku[]" class="form-control sku-input" required></td>
            <td>
                <select name="product_id[]" class="form-control product-select select2" required>
                    <option value="">-- Select Product --</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="variant_id[]" class="form-control variant-select" required>
                    <option value="">-- Select Variant --</option>
                </select>
            </td>
            <td><input type="text" name="batch[]" class="form-control" required></td>
            <td><input type="date" name="mfg[]" class="form-control" required></td>
            <td><input type="date" name="expiry[]" class="form-control" required></td>
            <td><input type="number" name="qty[]" class="form-control qty" min="1" required></td>
            <td><input type="number" name="purchase_price[]" class="form-control price" step="0.01" required></td>
            <td><input type="number" name="total_price[]" class="form-control total" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
        </tr>`;
            document.querySelector("#itemsTable tbody").insertAdjacentHTML('beforeend', row);
        }

        function removeRow(button) {
            button.closest('tr').remove();
            calculateSubtotal();
        }

        // Load variants when product changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                let productId = e.target.value;
                let row = e.target.closest('tr');
                let variantSelect = row.querySelector('.variant-select');
                let skuInput = row.querySelector('.sku-input');

                variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                let product = products.find(p => p.id == productId);

                if (product && product.variants) {
                    product.variants.forEach(v => {
                        variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}">${v.unit} - ₹${v.selling_price}</option>`;
                    });
                }

                // Reset SKU when product changes
                skuInput.value = '';
            }
        });

        // When variant changes → update SKU automatically
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select')) {
                let row = e.target.closest('tr');
                let selectedOption = e.target.options[e.target.selectedIndex];
                let skuInput = row.querySelector('.sku-input');

                if (selectedOption && selectedOption.dataset.sku) {
                    skuInput.value = selectedOption.dataset.sku;
                }
            }
        });

        // When SKU is typed → auto-select product & variant
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('sku-input')) {
                let sku = e.target.value.trim();
                let row = e.target.closest('tr');
                let productSelect = row.querySelector('.product-select');
                let variantSelect = row.querySelector('.variant-select');

                if (sku.length > 0) {
                    let foundProduct = null, foundVariant = null;

                    // Search SKU in variants
                    products.forEach(p => {
                        p.variants.forEach(v => {
                            if (v.varient_sku == sku) {
                                foundProduct = p;
                                foundVariant = v;
                            }
                        });
                    });

                    if (foundProduct && foundVariant) {
                        // Select product
                        productSelect.value = foundProduct.id;

                        // Rebuild variants
                        variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                        foundProduct.variants.forEach(v => {
                            variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}" ${v.id == foundVariant.id ? 'selected' : ''}>${v.unit} - ₹${v.selling_price}</option>`;
                        });
                    }
                }
            }
        });

        // Auto-calc row totals & subtotal
        function calculateRow(row) {
            let qty = parseFloat(row.querySelector(".qty")?.value) || 0;
            let price = parseFloat(row.querySelector(".price")?.value) || 0;
            let total = qty * price;
            if (row.querySelector(".total")) row.querySelector(".total").value = total.toFixed(2);
            return total;
        }

        function calculateSubtotal() {
            let rows = document.querySelectorAll("#itemsTable tbody tr");
            let subtotal = 0;
            rows.forEach(row => {
                subtotal += calculateRow(row);
            });
            document.getElementById("subtotal").value = subtotal.toFixed(2);
        }

        // Listen for qty/price input
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("qty") || e.target.classList.contains("price")) {
                calculateSubtotal();
            }
        });
    </script>


@endsection
