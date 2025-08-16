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
                    <li class="breadcrumb-item active" aria-current="page">Invoices</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Invoice: {{ $invoice->invoice_number }}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="card card-body">
                            <div><strong>Supplier:</strong> {{ $invoice->supplier?->name }}</div>
                            <div><strong>Date:</strong> {{ \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                        <tr>
                            <th>#</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th>Varient</th>
                            <th>Batch</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Purchase Price</th>
                        </tr>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($stocks as $i => $s)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $s->sku ?? '-' }}</td>
                                <td>{{ $s->product->name ?? '-' }}</td>
                                <td>{{ $s->variant->unit ?? '-' }}</td>
                                <td>{{ $s->batch_number }}</td>
                                <td>{{ $s->mfg_date }}</td>
                                <td>{{ $s->expiry_date }}</td>
                                <td>{{ $s->quantity }}</td>
                                <td>{{ number_format($s->purchase_price,2) }}</td>
                                <td>{{ number_format($s->total_price,2) }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection
