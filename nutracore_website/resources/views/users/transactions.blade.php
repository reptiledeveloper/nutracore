@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $subscription = CustomHelper::subscriptionsData($user);
    $faqs = \App\Models\FAQ::where('type', 'nc_cash')->where('is_delete', 0)->get();
    $transactions = \App\Models\Transaction::where('userID', $user->id)->where('wallet_type', 'cashback_wallet')->latest()->get();
    ?>

    <style>


        .rewards-card {
            background-color: #fff8e6;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            align-items: center;
            margin-top: 16px;
        }

        .rewards-card img {
            width: 50px;
            height: 50px;
        }

        .rewards-content {
            margin-left: 16px;
            flex: 1;
        }

        .rewards-content .amount {
            font-size: 24px;
            color: #c89f39;
            font-weight: bold;
        }

        .rewards-content .text {
            color: #777;
            font-size: 14px;
        }

        .transactions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding: 0 8px;
        }

        .transactions-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .transactions-header a {
            text-decoration: none;
            color: #00a89c;
            font-size: 14px;
        }

        .transaction-list {
            margin-top: 8px;
        }

        .transaction-item {
            background-color: #fff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .transaction-details {
            max-width: 70%;
        }

        .transaction-details .title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .transaction-details .id, .transaction-details .date {
            font-size: 12px;
            color: #555;
        }

        .amount {
            color: green;
            font-weight: bold;
            font-size: 14px;
            text-align: right;
        }

        .amount1 {
            color: red;
            font-weight: bold;
            font-size: 14px;
            text-align: right;
        }

        .faq {
            margin-top: 24px;
        }

        .faq-item {
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 8px;
            padding: 12px;
            cursor: pointer;
        }

        .faq-item h4 {
            margin: 0;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-item h4 span {
            font-size: 20px;
        }

        .faq-item p {
            margin-top: 8px;
            font-size: 12px;
            color: #555;
            display: none;
        }

        .faq-item.active p {
            display: block;
        }

        .faq-button {
            background-color: #00a89c;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-top: 16px;
            font-size: 16px;
            cursor: pointer;
        }

        .accordion .faq-item {
            border-bottom: 1px solid #ddd;
            overflow: hidden;
        }

        .accordion .faq-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            cursor: pointer;
            background: #f9f9f9;
        }

        .accordion .faq-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #fff;
            padding: 0 15px;
        }

        .accordion .faq-item.active .faq-body {
            max-height: 500px; /* or a large enough value */
            padding: 15px;
        }

        .accordion .icon {
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        .accordion .faq-item.active .icon {
            transform: rotate(45deg);
        }

    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> NC Cash
                </div>
            </div>
        </div>
        <div class="page-content pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">

                            <div class="rewards-card">
                                <img src="{{url('public/assets/coins.png')}}" alt="Coins">
                                <div class="rewards-content">
                                    <div class="amount">{{$user->cashback_wallet ?? 0}}</div>
                                    <div class="text">Total Rewards Cash</div>
                                </div>
                            </div>

                            <div class="transactions-header">
                                <h3>Transaction Logs</h3>
                            </div>

                            <div class="transaction-list">
                                @foreach($transactions as $transaction)

                                    <div class="transaction-item">
                                        <div class="transaction-details">
                                            <div class="title">{{$transaction->note??''}}</div>
                                            <div class="id">Transaction ID #{{$transaction->txn_no??''}}</div>
                                            <div class="date">Date
                                                : {{date('d M Y h:i A',strtotime($transaction->created_at))??''}}</div>
                                        </div>
                                        @if($transaction->type == 'CREDIT')
                                            <div class="amount">+ ₹{{$transaction->amount??0}}</div>
                                        @else
                                            <div class="amount1">- ₹{{$transaction->amount??0}}</div>

                                        @endif
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
