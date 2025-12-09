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
        @include('layouts.filter',['vendor_show'=>'vendor_show','search_show'=>'search_show','low_stock_show'=>'low_stock_show'])

        <div class="modal fade" id="stockUpdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('stocks.update_closing_stock') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label class="form-label">File</label>
                                    <input type="file" class="form-control" name="file" value="">
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


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Closing Stock</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('stocks.update_cs_batch', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary">Stock Update</a>
                                <a data-bs-toggle="modal"
                                   data-bs-target="#stockUpdate"
                                   class="btn btn-primary" title=" "> <i class="bi bi-file-text"></i></a>


                                <a href="{{ route('stocks.closing_stock_export', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary" title="Export "><i class="fa fa-file-excel-o"
                                                                              aria-hidden="true"></i></a>
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
                                <td>{{ $stock->sku??$stock->product_sku??'' }}</td>
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
