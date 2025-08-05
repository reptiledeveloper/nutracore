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
                    <li class="breadcrumb-item active" aria-current="page">Transactions</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Transactions</div>

                            <div class="dropdown ms-auto">
{{--                                <a href="{{ route('transaction.add', ['back_url' => $BackUrl]) }}"--}}
{{--                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Txn ID</th>
                            <th>User</th>
                            <th>Wallet Type</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Note</th>
                            <th>OrderID</th>
                            <th>Subscription</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($transactions)){
                        foreach ($transactions as $transaction) {
                            ?>
                        <tr>
                            <td>{{ $transaction->txn_no ?? '' }}</td>
                            <td>{{ $transaction->userID ?? '' }}</td>
                            <td>{{ $transaction->wallet_type ?? '' }}</td>
                            <td>{{ $transaction->amount ?? '' }}</td>
                            <td>{{ $transaction->type ?? '' }}</td>
                            <td>{{ $transaction->note ?? '' }}</td>
                            <td>{{ $transaction->orderID ?? '' }}</td>
                            <td>{{ $transaction->subscription_id ?? '' }}</td>
                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $transactions->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
