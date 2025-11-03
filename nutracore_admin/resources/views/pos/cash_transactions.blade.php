
@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    use App\Models\POSDailyCashTransaction;

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
                    <li class="breadcrumb-item active" aria-current="page">Cash Transactions</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['start_date_show'=>'start_date_show','end_date_show'=>'end_date_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Cash Transactions List</div>

                            <div class="dropdown ms-auto">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="table-responsive mt-3">
                    @foreach($daily_cash_transactions as $date => $vendors)
                        <div class="card mb-4 mt-3">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                <span class="badge bg-light text-dark fs-6">
                    Total Vendors: {{ $vendors->count() }}
                </span>
                            </div>


                            @foreach($vendors as $vendor)
                                @php
                                    $transactions = \App\Models\POSDailyCashTransaction::where('vendor_id', $vendor->vendor_id)
                                        ->whereDate('date', $date)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp

                                <div class="card mt-2">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Store:</strong> {{ \App\Helpers\CustomHelper::getVendorName($vendor->vendor_id) ?? 'Unknown' }}
                                        </div>
                                        <div class="text-end">
                            <span class="badge bg-success fs-6">
                                Total Sales: ₹{{ number_format($vendor->total_sales, 2) }}
                            </span>
                                            <span class="badge bg-danger fs-6">
                                Total Expense: ₹{{ number_format($vendor->total_expense, 2) }}
                            </span>
                                            <span class="badge bg-secondary ms-2 fs-6">
                                Transactions: {{ $vendor->total_transactions }}
                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Order ID</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Remark</th>
                                                <th>Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($transactions as $tIndex => $t)
                                                <tr>
                                                    <td>{{ $tIndex + 1 }}</td>
                                                    <td>{{ $t->order->unique_id??'' }}</td>
                                                    <td>{{ ucfirst($t->type) ??'' }}</td>
                                                    <td>₹{{ number_format($t->amount, 2) }}</td>
                                                    <td>{{$t->remarks??''}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y h:i A') }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    </div>

@endsection
