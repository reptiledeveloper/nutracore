@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();



    $stores = \App\Models\Vendors::where('status', 1)->where('is_delete', 0)->get();

    $defaultStoreId = optional($stores->firstWhere('name', 'Warehouse'))->id;

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
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Store</label>
                                    <select class="form-select" name="from_location" id="from_location" required>
                                        <option value="">-- Select Store --</option>
                                        @foreach($stores as $store)
                                            <option
                                                value="{{ $store->id }}" {{ $store->id == $defaultStoreId ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2">Items</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="addRow()">+ Add Row
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="transferTable">
                                        <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Product</th>
                                            <th>Variant</th>
                                            <th>Batch</th>
                                            <th>Qty</th>
                                            <th>Remove</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

                                </div>

                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>



    @php
        $stockMap = $stocks->groupBy(function($s) {
            return $s->product_id.'_'.$s->variant_id; // unique per product+variant
        })->map(function($group) {
            $first = $group->first();
            $key = $first->product->id??'';
            $key.='_';
            $key.=$first->variant->id ??'';
            return [
                'id'      => $first->id,
                'key'      => $key,
                'label'   => implode(' ', array_filter([
                                $first->product->name ?? '',
                                $first->variant ? '- '.$first->variant->unit : null,
                            ])),
                'batches' => $group->map(function($s) {
                    return [
                        'id'    => $s->id,
                        'batch' => $s->batch_number,
                        'qty'   => $s->quantity,
                        'exp'   => $s->expiry_date,
                    ];
                })->values()
            ];
        })->values();


    @endphp

    <script>
        const stockMap = @json($stockMap);
        const products = @json($products);
        // Structure: [{id, name, variants:[{id, varient_sku, unit, selling_price}]}]

        function addRow() {
            let row = `
        <tr>
            <td><input type="text" name="sku[]" class="form-control sku-input" required></td>
            <td>
                <select name="product_id[]" class="form-control product-select select2" required>
    <option value="">-- Select Product --</option>
    ${products.map((p, index) => `<option value="${p.id}">${index + 1}. ${p.name}</option>`).join('')}
</select>
            </td>
            <td>
                <select name="variant_id[]" class="form-control variant-select" >
                    <option value="">-- Select Variant --</option>
                </select>
            </td>
             <td>
            <select class="form-select batch-select" name="batch_id[]" >
                <option value="">-- Select Batch --</option>
            </select>
        </td>
            <td><input type="number" name="qty[]" class="form-control qty" required></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
        </tr>`;

            let tbody = document.querySelector("#transferTable tbody");
            tbody.insertAdjacentHTML('beforeend', row);

            // ✅ Reinitialize Select2 for new product dropdown
            // $(tbody).find('.select2').select2({
            //     width: '100%' // adjust as needed
            // });
        }

        function removeRow(button) {
            button.closest('tr').remove();
            calculateSubtotal();
        }


        // Load variants when product changes
        // document.addEventListener('change', function(e) {
        //     if (e.target.classList.contains('product-select')) {
        //         let productId = e.target.value;
        //         let row = e.target.closest('tr');
        //         let variantSelect = row.querySelector('.variant-select');
        //         let skuInput = row.querySelector('.sku-input');
        //
        //         variantSelect.innerHTML = '<option value="" selected>-- Select Variant --</option>';
        //         let product = products.find(p => p.id == productId);
        //
        //         if (product && product.variants) {
        //             product.variants.forEach(v => {
        //                 variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}">${v.unit} - ₹${v.selling_price}</option>`;
        //             });
        //         }
        //
        //         // Reset SKU when product changes
        //         skuInput.value = '';
        //     }
        // });


        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('product-select')) return;

            let productId = e.target.value;
            let row = e.target.closest('tr');
            let variantSelect = row.querySelector('.variant-select');
            let skuInput = row.querySelector('.sku-input');
            let batchSelect = row.querySelector('.batch-select');

            // Reset fields
            variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
            batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
            skuInput.value = '';

            let product = products.find(p => p.id == productId);
            if (!product) return;

            if (product.variants && product.variants.length > 0) {
                // Populate variants
                product.variants.forEach(v => {
                    variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}">${v.unit} - ₹${v.selling_price}</option>`;
                });
                // Refresh Select2
                $(variantSelect).trigger('change.select2');

            } else {
                // No variants → fill SKU with product SKU
                skuInput.value = product.sku ?? '';

                // Load batches for product only (no variant)
                const stockKey = productId + '_'; // variant id empty
                const stockItem = stockMap.find(s => s.key === stockKey) || null;

                if (stockItem && stockItem.batches) {
                    stockItem.batches.forEach(b => {
                        // const label = `Batch: ${b.batch} | Qty: ${b.qty} ${b.exp ? '| Exp: ' + b.exp : ''}`;
                        const label = `Batch: ${b.batch}`;
                        batchSelect.innerHTML += `<option value="${b.id}">${label}</option>`;
                    });
                    $(batchSelect).trigger('change.select2');
                } else {
                    batchSelect.innerHTML = `<option value="">-- No Batch Found --</option>`;
                    $(batchSelect).trigger('change.select2');
                }
            }

            // Trigger variant change manually to load batch if variants exist
            $(variantSelect).trigger('change.select2');
        });




        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('variant-select')) {
                let row = e.target.closest('tr');
                let productId = row.querySelector('.product-select').value;
                let variantId = e.target.value;
                let skuInput = row.querySelector('.sku-input');
                let batchSelect = row.querySelector('.batch-select');

                // update SKU
                let selectedOption = e.target.options[e.target.selectedIndex];
                if (selectedOption && selectedOption.dataset.sku) {
                    skuInput.value = selectedOption.dataset.sku;
                }

                // load batches
                batchSelect.innerHTML = '<option value="" selected>-- Select Batch --</option>';
                const stockKey = productId + '_' + variantId;
                const stockItem = stockMap.find(s => s.key === stockKey) || null;

                if (stockItem && stockItem.batches) {
                    stockItem.batches.forEach(b => {
                        // const label = `Batch: ${b.batch} | Qty: ${b.qty} ${b.exp ? '| Exp: '+b.exp : ''}`;
                        const label = `Batch: ${b.batch}`;
                        batchSelect.innerHTML += `<option value="${b.id}">${label}</option>`;
                    });
                } else {
                    batchSelect.innerHTML = `<option value="">-- No Batch Found --</option>`;
                }
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('batch-select')) {
                let row = e.target.closest('tr');
                let qtyInput = row.querySelector('.qty');
                let productId = row.querySelector('.product-select').value;
                let variantId = row.querySelector('.variant-select').value;
                let batchId = e.target.value;

                const stockKey = productId + '_' + variantId;
                const stockItem = stockMap.find(s => s.key === stockKey) || null;

                if (stockItem && stockItem.batches) {
                    let selectedBatch = stockItem.batches.find(b => String(b.id) === String(batchId));
                    if (selectedBatch) {
                        console.log("selectedBatch" + selectedBatch.id);
                        // var qty = getClosingStock(productId,variantId,batchId);
                        //
                        // // set max qty based on stock
                        // qtyInput.setAttribute("max", qty);
                        // qtyInput.value = qty; // reset so user enters new

                        getClosingStock(productId, variantId, selectedBatch.id)
                            .then(qty => {
                                if (qty !== null) {
                                    qtyInput.setAttribute("max", qty);
                                    qtyInput.value = qty; // reset to available stock
                                }
                            })
                            .catch(err => console.error("Error fetching stock:", err));
                    }
                }
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
        // document.addEventListener('input', function(e) {
        //     if (e.target.classList.contains('sku-input')) {
        //         let sku = e.target.value.trim();
        //         let row = e.target.closest('tr');
        //         let productSelect = row.querySelector('.product-select');
        //         let variantSelect = row.querySelector('.variant-select');
        //
        //         if (sku.length > 0) {
        //             let foundProduct = null, foundVariant = null;
        //
        //             // Search SKU in variants
        //             products.forEach(p => {
        //                 p.variants.forEach(v => {
        //                     if (v.varient_sku == sku) {
        //                         foundProduct = p;
        //                         foundVariant = v;
        //                     }
        //                 });
        //             });
        //
        //             if (foundProduct && foundVariant) {
        //                 // Select product
        //                 productSelect.value = foundProduct.id;
        //
        //                 // Rebuild variants
        //                 variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
        //                 foundProduct.variants.forEach(v => {
        //                     // variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}" ${v.id == foundVariant.id ? 'selected' : ''}>${v.unit} - ₹${v.selling_price}</option>`;
        //                     variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}" >${v.unit} - ₹${v.selling_price}</option>`;
        //                 });
        //             }
        //         }
        //     }
        // });
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('sku-input')) {
                let sku = e.target.value.trim();
                let row = e.target.closest('tr');
                let productSelect = row.querySelector('.product-select');
                let variantSelect = row.querySelector('.variant-select');
                let batchSelect = row.querySelector('.batch-select');
                if (sku.length > 0) {
                    let foundProduct = null, foundVariant = null;

                    products.forEach(p => {
                        if (p.variants && p.variants.length > 0) {
                            // Check in variants
                            p.variants.forEach(v => {
                                if (v.varient_sku == sku) {
                                    foundProduct = p;
                                    foundVariant = v;
                                }
                            });
                        } else {
                            // If no variants, match product SKU
                            if (p.sku == sku) {
                                foundProduct = p;
                            }
                        }
                    });

                    // if (foundProduct) {
                    //     // Select product
                    //     productSelect.value = foundProduct.id;
                    //
                    //     // If product has variants
                    //     if (foundProduct.variants && foundProduct.variants.length > 0) {
                    //         variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                    //         foundProduct.variants.forEach(v => {
                    //             variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}" ${foundVariant && v.id == foundVariant.id ? 'selected' : ''}>${v.unit} - ₹${v.selling_price}</option>`;
                    //         });
                    //     } else {
                    //         // No variants → clear or disable variant dropdown
                    //         variantSelect.innerHTML = '<option value="">No Variant</option>';
                    //     }
                    // }

                    if (foundProduct) {
                        // Select product
                        $(productSelect).val(foundProduct.id).trigger('change'); // ✅ Fix here

                        // If product has variants
                        if (foundProduct.variants && foundProduct.variants.length > 0) {
                            variantSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                            foundProduct.variants.forEach(v => {
                                variantSelect.innerHTML += `<option value="${v.id}" data-sku="${v.varient_sku}" ${foundVariant && v.id == foundVariant.id ? 'selected' : ''}>${v.unit} - ₹${v.selling_price}</option>`;
                            });

                            if (foundVariant) {
                                $(variantSelect).val(foundVariant.id).trigger('change'); // ✅ auto-select variant too
                            }
                        } else {
                            variantSelect.innerHTML = '<option value="">No Variant</option>';
                        }


                        let productId = foundProduct.id;
                        let variantId = foundVariant ? foundVariant.id : '';
                        batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';

                        const stockKey = productId + '_' + variantId;
                        const stockItem = stockMap.find(s => s.key === stockKey) || null;

                        if (stockItem && stockItem.batches) {
                            stockItem.batches.forEach(b => {
                                // const label = `Batch: ${b.batch} | Qty: ${b.qty} ${b.exp ? '| Exp: ' + b.exp : ''}`;
                                const label = `Batch: ${b.batch}`;
                                batchSelect.innerHTML += `<option value="${b.id}">${label}</option>`;
                            });
                        } else {
                            batchSelect.innerHTML = '<option value="">-- No Batch Found --</option>';
                        }

                    }

                }
            }
        });


        function getClosingStock(product_id, varient_id, batch_id) {
            return new Promise((resolve, reject) => {
                var store_id = $('#from_location').val();

                if (!store_id) {
                    alert('Please Select Store');
                    resolve(null);
                    return;
                }

                $.ajax({
                    url: '{{route('stocks.get_closing_stock')}}', // Laravel route
                    type: 'GET',
                    data: {
                        product_id: product_id,
                        varient_id: varient_id,
                        batch_id: batch_id,
                        store_id: store_id
                    },
                    success: function (response) {
                        // expect Laravel to return { stock: 10 }
                        if (response && response.stock !== undefined) {
                            resolve(response.stock);
                        } else {
                            resolve(0);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        reject(error);
                    }
                });
            });
        }



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
