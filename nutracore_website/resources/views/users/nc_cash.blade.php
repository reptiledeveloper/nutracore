@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $subscription = CustomHelper::subscriptionsData($user);
    $faqs = \App\Models\FAQ::where('type', 'nc_cash')->where('is_delete', 0)->get();
    $transactions = [];
    if (!empty($user)) {
        $transactions = \App\Models\Transaction::where('userID', $user->id)->where('wallet_type', 'cashback_wallet')->latest()->limit(5)->get();

    }
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
            text-align: center;
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
            text-align: center;
        }

        .amount1 {
            color: red;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
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
    <style>

        .nc-table-wrapper {
            width: 100%;
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .nc-table-wrapper h4 {
            background: #e6f7f6;
            color: #008d7d;
            padding: 12px 20px;
            margin: 0;
            font-weight: 600;
            border-bottom: 1px solid #cdebea;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            color: black;
        }

        th, td {
            padding: 10px 12px;
            border: 2px solid #d6eaea;
            font-size: 14px;
        }

        th {
            color: #008d7d;
            font-weight: 600;
        }

        td:first-child {
            text-align: left;
            color: #333;
            font-weight: 500;
        }



        .check {
            color: #00b894;
            font-weight: bold;
        }.curved-table-container {
             overflow: hidden;
             border-radius: 16px; /* curved corners */
             border: 1px solid #dde5eb;
         }

        .curved-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 16px;
        }

        /* Header */
        .curved-table thead tr {
            background-color: #00A8A8;
        }

        .curved-table th {
            color: white;
            padding: 7px;
            text-align: center;
            font-size: 12px;
        }

        /* Body rows */
        .curved-table td {
            padding: 12px;
            font-size: 11px;
            border-bottom: 1px solid #eef2f3;
        }

        /* Last row no border */
        .curved-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Non-member column color */
        .curved-table td.non-member {
            background-color: #9fcec9;
        }

        /* Optional hover effect */
        .curved-table tbody tr:hover {
            background-color: #f7fefe;
        }
        .rewards-card {
            width: 100%;
            display: flex;
            justify-content: center;   /* Center horizontally */
            align-items: center;       /* Center vertically */
            gap: 12px;
            padding: 12px 0;           /* Optional */
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
                                <img src="{{ url('public/assets/coins.png') }}" alt="Coins">
                                <div class="reards-content">
                                    <div class="amount">₹ {{ $user->cashback_wallet ?? 0 }}</div>
                                    <div class="text">Total Rewards Cash</div>
                                </div>
                            </div>


                            <h4 class="mt-10">Member Vs Non-Member Benefits</h4>
                            <div class="mt-10 mb-5">


                                    <table class="curved-table">
                                        <thead>
                                        <tr>
                                            <th>Benefits</th>
                                            <th>Member</th>
                                            <th>Non-Member</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>✔ Extra Discount</td>
                                            <td>Extra 5% Off</td>
                                            <td class="non-member">Extra 0% Off</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Extra Cashback</td>
                                            <td>Upto 8% Cash</td>
                                            <td class="non-member">Upto 6% Cash</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Silver</td>
                                            <td>4% NC Cash</td>
                                            <td class="non-member">2% NC Cash</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Gold</td>
                                            <td>6% NC Cash</td>
                                            <td class="non-member">4% NC Cash</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Platinum</td>
                                            <td>8% NC Cash</td>
                                            <td class="non-member">6% NC Cash</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Free 2-Hour Delivery</td>
                                            <td>Above 2000</td>
                                            <td class="non-member">Above 3000</td>
                                        </tr>
                                        <tr>
                                            <td>✔ Birthday Reward</td>
                                            <td>Yes</td>
                                            <td class="non-member">No</td>
                                        </tr>
                                        </tbody>
                                    </table>


                            </div>






                            <div class="transactions-header">
                                <h3>Transaction Logs</h3>

                                @php
                                    $url = route('transactions',['type'=>'nc_cash']);
                                @endphp
                                <a onclick="checkLoginRedirect('{{$url}}')">See
                                    All</a>
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

                            <button class="faq-button">FAQ</button>

                            <div class="faq accordion">
                                @foreach($faqs as $faq)
                                    <div class="faq-item">
                                        <div class="faq-header" onclick="toggleAccordion(this)">
                                            <h4>{{ strip_tags($faq->question ?? 'No question') }}</h4>
                                            <span class="icon">+</span>
                                        </div>
                                        <div class="faq-body">
                                            <p>{{ strip_tags($faq->answer ?? 'No answer available') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleAccordion(element) {
            const item = element.parentElement;
            item.classList.toggle('active');
        }

    </script>
@endsection
