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
                    <li class="breadcrumb-item active" aria-current="page">Stock Logs</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Stock Logs</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Store</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Action</th>
                            <th>Qty</th>
                            <th>Closing Stock</th>
                            <th>Related</th>
                            <th>User</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $log->store->name ?? '-' }}</td>
                                <td>{{ $log->product->name ?? '-' }}</td>
                                <td>{{ $log->variant->name ?? '-' }}</td>
                                <td>{{ ucfirst($log->action) }}</td>
                                <td>{{ $log->quantity }}</td>
                                <td>{{ $log->closing_stock }}</td>
                                <td>{{ $log->related_type }} #{{ $log->related_id }}</td>
                                <td>{{ $log->created_by }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{ $logs->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
