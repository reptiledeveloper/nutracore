@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();

    $faqs = \App\Models\FAQ::where('type', 'nc_cash')->where('is_delete', 0)->get();
    $refer_amount = CustomHelper::getSettings('refer_amount');
    ?>
    <style>

        .referral-header {
            background: #e0f7fa;
            padding: 20px;
            text-align: center;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .referral-header img {
            width: 80px;
        }

        .referral-card {
            background: linear-gradient(135deg, #00bfa5, #00897b);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-top: -40px;
        }

        .referral-card img {
            width: 100px;
        }

        .referral-code {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .rewards-summary {
            background: #fff3e0;
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .pending-referrals {
            background: #e3f2fd;
            border-radius: 12px;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .steps {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
        }

        .steps .step {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
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

        .faq-item h4 {
            margin: 0;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Refer & Earn
                </div>
            </div>
        </div>
        <div class="page-content pb-150">
            <div class="container">
                <div class="row">
                    <!-- Header -->
                    <div class="col-md-6">
                        <div class="rewards-summary mt-20">
                            <img src="{{url('public/assets/coins.png')}}" alt="Coins"/>
                            <div>
                                <h5>Refer & Earn</h5>
                                <p>Refer your friend & earn <strong>₹{{$refer_amount}} Each</strong></p>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <!-- Rewards Summary -->
                        <div class="rewards-summary">
                            <img src="{{url('public/assets/coins.png')}}" alt="Coins">
                            <div>
                                <h6>{{$user->cashback_wallet??0}}</h6>
                                <small>Total Rewards Coins</small>
                            </div>
                            <div class="ms-auto"><img
                                    src="https://img.icons8.com/ios-filled/24/000000/chevron-right.png" alt="Next"/>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-50">
                        <!-- Referral Card -->
                        <div class="referral-card">
                            <h5>Refer your friend and earn</h5>
                            <img src="https://img.icons8.com/color/96/000000/gift.png" alt="Gift"/>
                            <p style="color: white !important;">Refer Your Friends and earn free cash rewards when they
                                sign up and shop. It's quick ,easy and rewarding for both of You!.</p>
                            <div class="referral-code">
                                <span>{{$user->referral_code??''}}</span>
                                <button class="btn btn-light btn-sm">Copy</button>
                            </div>
                            <div class="d-flex justify-content-center gap-3 mt-3">
                                <button class="btn btn-light btn-sm d-flex align-items-center">
                                    <img src="https://img.icons8.com/ios-filled/24/000000/whatsapp.png"
                                         alt="WhatsApp"
                                         style="width:16px; height:16px; margin-right:6px;"/>
                                    via WhatsApp
                                </button>
                            </div>

                        </div>


                        <!-- Pending Referrals -->
                        <div class="pending-referrals">
                            <div>
                                <strong>Pending Referrals:</strong> <span class="badge bg-warning">0</span>
                            </div>
                            <button class="btn btn-outline-primary btn-sm">Complete now</button>
                        </div>

                        <!-- Steps -->
                        <div class="steps">
                            <div class="step">
                                <img src="https://img.icons8.com/color/48/000000/link.png" alt="Invite">
                                <div>
                                    <h6>Invite your friend</h6>
                                    <small>Lorem ipsum dolor sit amet, consectetur</small>
                                </div>
                            </div>
                            <div class="step">
                                <img src="https://img.icons8.com/color/48/000000/order-history.png" alt="Order">
                                <div>
                                    <h6>Placed Order by referral</h6>
                                    <small>Lorem ipsum dolor sit amet, consectetur</small>
                                </div>
                            </div>
                            <div class="step">
                                <img src="https://img.icons8.com/color/48/000000/money.png" alt="Prize">
                                <div>
                                    <h6>Got Prize money</h6>
                                    <small>Lorem ipsum dolor sit amet, consectetur</small>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Section -->
                        <button class="faq-button">FAQ</button>

                        <div class="faq accordion mt-5">
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
    </main>

    <script>
        function toggleAccordion(element) {
            const item = element.parentElement;
            item.classList.toggle('active');
        }

    </script>
@endsection
