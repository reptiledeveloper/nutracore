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
                    <li class="breadcrumb-item active" aria-current="page">POS Order List</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All POS Order List</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('pos.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Created Date</th>
                            <th>Due Date</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
                            <th>Due Amount</th>
                            <th>Payment Mode</th>
                            <th>Payment Status</th>
                            <th>Order Type</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($pos) && count($pos) > 0)
                            @foreach($pos as $po)
                                <tr>
                                    <td>{{ $po->id }}</td>
                                    <td>{{ $po->invoice_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->due_date)->format('d-m-Y') }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getUserName($po->user_id) }}</td>
                                    <td>{{ number_format($po->total_amount, 2) }}</td>
                                    <td>{{ ucfirst($po->payment_mode) }}</td>
                                    <td>{{ ucfirst($po->payment_status) }}</td>
                                    <td>{{ ucfirst($po->order_type) }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getUserName($po->created_by) }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getStatusStr($po->status) }}</td>
                                    <td>{{ $po->is_delete ? 'Deleted' : 'Active' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->created_at)->format('d-m-Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->updated_at)->format('d-m-Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('pos.edit', $po->id.'?back_url='.$BackUrl) }}"
                                                       class="dropdown-item">Edit</a>
                                                    <a href="{{ route('pos.delete', $po->id.'?back_url='.$BackUrl) }}"
                                                       onclick="return confirm('Are you sure you want to delete this invoice?')"
                                                       class="dropdown-item">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="15" class="text-center text-muted">No records found</td>
                            </tr>
                        @endif
                        </tbody>

                    </table>

                    {{ $pos->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
