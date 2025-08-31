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

        <div class="modal fade" id="stockUpdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('stocks.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label class="form-label required">Select Store</label>
                                    <select class="form-control" name="store_id">
                                        <option value="">Select Store</option>
                                        @foreach($vendors as $seller)
                                            <option value="{{$seller->id??''}}">{{$seller->name??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
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

        @include('layouts.filter',['expiry_show'=>'expiry_show','days'=>$days,'vendor_show'=>'vendor_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Stocks & Expiry</div>

                            <div class="dropdown ms-auto">
                                <a data-bs-toggle="modal"
                                   data-bs-target="#stockUpdate"
                                   class="btn btn-primary" title="Import "> <i class="bi bi-file-text"></i></a>

                                <a href="{{ route('stocks.export', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary" title="Export "><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Batch</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stocks as $i => $s)
                            <tr class="{{ $s->expiry_date && \Illuminate\Support\Carbon::parse($s->expiry_date)->isBefore(now()->addDays(30)) ? 'table-warning' : '' }}">
                                <td>{{ $stocks->firstItem() + $i }}</td>
                                <td>{{ $s->product?->name ?? 'N/A' }}</td>
                                <td>{{ $s->variant?->unit ?? '-' }}</td>
                                <td>{{ $s->batch_number ?? '-' }}</td>
                                <td>{{ $s->mfg_date ? \Carbon\Carbon::parse($s->mfg_date)->format('d-M-Y') : '-' }}</td>
                                <td>{{ $s->expiry_date ? \Carbon\Carbon::parse($s->expiry_date)->format('d-M-Y') : '-' }}</td>
                                <td>{{ $s->quantity }}</td>
                                <td>{{ number_format($s->purchase_price, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{ $stocks->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
