@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

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
                    <li class="breadcrumb-item active" aria-current="page">Closing Stock</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['vendor_show'=>'vendor_show','search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Closing Stock</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Store</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Closing Stock</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stocks as $i => $stock)
                            <tr>
                                <td>{{ $stocks->firstItem() + $i }}</td>
                                <td>{{ $stock->seller_name ??''}}</td>
                                <td>{{ $stock->sku??'' }}</td>
                                <td>{{ $stock->product_name ??''}}</td>
                                <td>{{ $stock->unit ?? '-' }}</td>
                                <td>{{ $stock->closing_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $stocks->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
