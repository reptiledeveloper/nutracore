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
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="itemsTable">
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
                                            <th>Remove</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
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
        const products = @json($products); // Pass from controller with variants

        function addRow() {
            let row = `
        <tr>
            <td><input type="text" name="sku[]" class="form-control" required></td>
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
            <td><input type="number" name="qty[]" class="form-control" min="1" required></td>
            <td><input type="number" name="purchase_price[]" class="form-control" step="0.01" required></td>
            <td><input type="number" name="total_price[]" class="form-control" step="0.01" required></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
        </tr>`;
            document.querySelector("#itemsTable tbody").insertAdjacentHTML('beforeend', row);
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                let productId = e.target.value;
                let variantSelect = e.target.closest('tr').querySelector('.variant-select');
                variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                let product = products.find(p => p.id == productId);
                if (product && product.variants) {
                    product.variants.forEach(v => {
                        variantSelect.innerHTML += `<option value="${v.id}">${v.unit} - ₹${v.selling_price}</option>`;
                    });
                }
            }
        });
    </script>
@endsection
