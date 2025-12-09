@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $vendors = \App\Helpers\CustomHelper::getVendors();
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
                    <li class="breadcrumb-item active" aria-current="page">Stocks</li>
                </ol>
            </nav>
        </div>

        @include('snippets.errors')
        @include('snippets.flash')
        <form action="" method="post" enctype="multipart/form-data"
              onsubmit="return confirm('Are you sure you want to Update?');">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-md-flex gap-4 align-items-center">
                                <div class="d-none d-md-flex">Pending Update Approval</div>

                                <div class="dropdown ms-auto">
                                    <button type="submit" class="btn btn-success" title="Import ">Approve</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-lg mb-0" id="products">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="stock_ids[]" value="all"> Select All</th>
                                <th>Store Name</th>
                                <th>SKU</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Batch</th>
                                <th>Quantity</th>
                                <th>Updated Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stocks as $s)

                                <tr>
                                    <td><input type="checkbox" name="stock_ids[]" value="{{$s->id??''}}"></td>
                                    <td>{{ \App\Helpers\CustomHelper::getVendorName($s->store_id??'')??'' }}</td>
                                    <td>{{ $s->variant?->varient_sku ?? $s->product?->sku ??'N/A' }}</td>
                                    <td>{{ $s->product?->name ?? 'N/A' }}</td>

                                    <td>{{ $s->variant?->unit ?? '-' }}</td>
                                    <td>{{ $s->batch_number ?? '-' }}</td>
                                    <td>{{ $s->quantity ??0}}</td>
                                    <td>{{ $s->updated_qty ??0}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.querySelector('input[name="stock_ids[]"][value="all"]');
            const itemCheckboxes = document.querySelectorAll('input[name="stock_ids[]"]:not([value="all"])');

            // Toggle all when "Select All" is clicked
            selectAllCheckbox.addEventListener('change', function () {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
            });

            // Uncheck "Select All" if any one item is unchecked
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Optional: If all are checked, check "Select All"
                        if ([...itemCheckboxes].every(cb => cb.checked)) {
                            selectAllCheckbox.checked = true;
                        }
                    }
                });
            });
        });
    </script>

@endsection
