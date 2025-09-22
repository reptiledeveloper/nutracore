@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $subscription = CustomHelper::subscriptionsData($user);
    $tire_system = $subscription['tire_system'] ?? '';
    $active_loyalty = $subscription['active_loyalty'] ?? '';

    $nutrapassbanner = CustomHelper::getBannerData('nutrapass_banner');

    ?>

    <style>
        .hero-section {
            background-image: url('{{$nutrapassbanner}}');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .border_radius_white {
            border: 1px solid;
            border-radius: 26px;
        }

        .text-white {
            color: #f8f9fa;
        }
    </style>


    <style>
        .scroll-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 10px;
            cursor: grab;
        }

        .plan {
            flex: 0 0 auto;
            background-color: #003333;
            border-radius: 16px;
            border: 2px solid transparent;
            padding: 20px;
            text-align: center;
            width: 180px;
            transition: all 0.3s ease;
        }

        .plan h4 {
            color: #ccc;
            margin-bottom: 10px;
        }

        .plan .months {
            font-size: 48px;
            font-weight: bold;
        }

        .plan .details {
            margin: 10px 0;
            font-size: 16px;
        }

        .plan .price {
            font-size: 20px;
            margin-top: 10px;
        }

        .plan.selected {
            background-color: #FFD54F;
            color: white;
            box-shadow: 0 0 20px #FFD54F88;
            border: 2px solid #fff;
        }

        .plan.selected .details strong {
            font-weight: bold;
        }

        /* Hide scrollbar */
        .scroll-container::-webkit-scrollbar {
            display: none;
        }

        .scroll-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .faq-container {
            background: #fff;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .faq-container h2 {
            text-align: center;
            color: #00b6b9;
            margin-bottom: 10px;
        }

        .faq-container p.description {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }

        .faq-heading {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .faq-item {
            border-top: 1px solid #eee;
        }

        .faq-question {
            padding: 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            cursor: pointer;
            color: #333;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 15px;
            color: #666;
            background: #fafafa;
        }

        .faq-answer.open {
            padding-top: 10px;
            padding-bottom: 15px;
        }

        .faq-question.active + .faq-answer {
            max-height: 500px;
        }

        .icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .faq-question.active .icon {
            transform: rotate(180deg);
        }
    </style>


    <style>
        .section {
            padding: 50px 20px;
            text-align: center;
            background: #fff;
        }

        .section h2 {
            color: #009999;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .slider-container {
            position: relative;
            margin: 0 auto;
            overflow: hidden;
        }

        .slider {
            display: flex;
            gap: 20px;
            transition: transform 0.5s ease-in-out;
            cursor: grab;
        }

        .slide {
            min-width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .slide img {
            width: 200px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 4px solid #f9a825;
        }

        .review-content {
            text-align: left;
        }

        .review-content h3 {
            margin: 0;
            color: #f9a825;
        }

        .review-content small {
            color: #555;
        }

        .review-content p {
            margin-top: 10px;
            color: #333;
            font-size: 14px;
        }

        .bullets {
            text-align: center;
            margin-top: 20px;
        }

        .bullets span {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background: #ccc;
            border-radius: 50%;
            cursor: pointer;
        }

        .bullets span.active {
            background: #009999;
        }

        .nutrapass-section {
            background: linear-gradient(to bottom, #cba545, #000);
            color: white;
            border-radius: 30px;
            overflow: hidden;
            position: relative;
        }

        .nutrapass-section .highlight {
            background: rgba(255, 255, 255, 0.3);
            padding: 0 0.5rem;
            border-radius: 4px;
        }

        .custom-slider {
            width: 100%;
            accent-color: #fff;
            background: transparent;
        }

        .slider-container {
            position: relative;
        }

        .slider-value {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #fff;
        }

        .slider-wrapper {
            margin-top: 20px;
        }

        .levels {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .range-values {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-top: 5px;
        }

        .progress-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: #ddd;
            outline: none;
        }

        .progress-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f0c14b;
            cursor: pointer;
            border: none;
            margin-top: -6px;
        }

        .progress-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f0c14b;
            cursor: pointer;
            border: none;
        }

        .filled {
            height: 8px;
            border-radius: 4px;
            background: red;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 1;
        }

        .slider-container {
            position: relative;
        }
    </style>
    <style>

        .slider-container {
            max-width: 400px;
            margin: auto;
        }

        .levels {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .range-values {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 14px;
        }

        .slider-inner {
            position: relative;
            height: 8px;
            background: #ddd;
            border-radius: 4px;
        }

        .filled {
            height: 100%;
            background: #f0c14b;
            border-radius: 4px;
            width: 0;
            transition: width 0.3s ease;
        }


    </style>

    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Nutrapass

                </div>
            </div>
        </div>


        <section class="hero-section">
            <div class="container">
                <h1 class="display-5 fw-bold text-white">It’s Time To Gain More Muscles</h1>
                <p class="lead text-white">BODY PERFORMANCE</p>
                <a href="#" class="btn btn-warning text-white w-25">Join Now</a>
            </div>
        </section>
        <section class="nutrapass-section text-white p-4 rounded-4">
            <div class="row align-items-center">
                <!-- Left Image -->
                <div class="col-md-3 d-none d-md-block">
                    <img src="{{ url('public/assets/images/image 1414.svg') }}"
                         class="img-fluid h-100 object-fit-cover rounded-start-4" alt="">
                </div>

                <!-- Center Content -->
                <div class="col-md-6 text-center px-4">
                    <img src="{{ url('public/assets/images/nutrapasslogo.svg') }}"
                         class="img-fluid h-100 object-fit-cover rounded-start-4" alt="">
                    <p class="text-white-50">Exclusive Pricing · Unlimited Benefits</p>


                    <div class="slider-container">
                        <div class="levels">
                            @php
                                $per_val = 100 / count($tire_system);
                                $filled = 0;
                            @endphp

                            @foreach($tire_system as $tire)
                                @php
                                    if($active_loyalty->id == $tire->id){
                                        // Matched tier → stop adding further
                                        $filled += $per_val;
                                        break;
                                    } else {
                                        $filled += $per_val;
                                    }
                                @endphp

                            @endforeach

                            @foreach($tire_system as $tire)
                                <span>{{ $tire->title ?? '' }}</span>
                            @endforeach


                        </div>
                        <div class="slider-inner"
                             style="background-color: black; border-radius: 4px; height: 8px; width: 100%;">
                            <div id="filledBar" class="filled"
                                 style="width: {{$filled}}%; background-color: #f9b201; height: 100%; border-radius: 4px;"></div>
                        </div>
                        <div class="range-values">
                            @foreach($tire_system as $tire)
                                <span>₹{{$tire->from_amount??''}}</span>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Right Image -->
                <div class="col-md-3 d-none d-md-block">
                    <img src="{{ url('public/assets/images/image 1419.svg') }}"
                         class="img-fluid h-100 object-fit-cover rounded-end-4" alt="">
                </div>
            </div>

            <div class="row mt-4 border-top pt-3 text-center" style="background-color: #212828;margin:10px">
                <div class="col-md-4">
                    <div class="text-muted">Membership Status</div>
                    <div class="fw-bold text-warning fs-5">{{$active_loyalty->title??''}}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Valid Till</div>
                    <div
                        class="fw-bold fs-5 text-warning">{{!empty($user->subscription_end) ? date('d M Y',strtotime($user->subscription_end)) : ""}}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Saved So Far</div>
                    <div class="fw-bold fs-5 text-warning">₹{{$subscription['saved_amount'] ?? ''}}</div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container text-center">
                <div class="row g-3 justify-content-center">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <img src="{{ url('public/assets/images/ic_twotone-discount.svg') }}" class="mb-2"
                                 style="width: 40px; height: 40px;">
                            <div>10% off every order</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <img src="{{ url('public/assets/images/ic_twotone-discount.svg') }}" class="mb-2"
                                 style="width: 40px; height: 40px;">
                            <div>Free Expert Delivery</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <img src="{{ url('public/assets/images/ic_twotone-discount.svg') }}" class="mb-2"
                                 style="width: 40px; height: 40px;">
                            <div>Monthly Fitness Box</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded text-center">
                            <img src="{{ url('public/assets/images/ic_twotone-discount.svg') }}" class="mb-2"
                                 style="width: 40px; height: 40px;">
                            <div>Early Access & Secret Sales</div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <style>
            .calculator-card {
                background-color: #4fc3c3;
                color: white;
                border-radius: 12px;
                padding: 20px;

                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .calculator-card h5 {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 15px;
                text-transform: uppercase;
            }

            .slider-container1 {
                margin-bottom: 15px;
            }

            .slider {
                width: 100%;
            }

            .text-line {
                font-size: 13px;
                margin: 5px 0;
            }

            .text-line strong {
                font-weight: bold;
            }

            .bottom-section {
                background-color: white;
                color: #000;
                border-radius: 0 0 12px 12px;
                padding: 10px 15px;
                display: flex;
                align-items: center;
                font-weight: bold;
                cursor: pointer;
            }

            .bottom-section img {
                margin-right: 8px;
            }

            .slider-container1 {
                width: 100%; /* Ensure the container is wide enough */
            }

            .slider {
                width: 100%; /* Make the slider span the full container width */
            }

            input {

                padding-left: 0px;

            }

        </style>

        <section class="p-5 m-2 " style="background: black; width: 99%;border-radius:10px">
            <div class="row p-3">
                <div class="col-md-5">
                    <div class="bg-dark text-white p-3 rounded-box m-3 border_radius_white">
                        <div class="d-flex align-items-center">
                            <img src="{{ url('public/assets/images/vip_icon.png') }}" class="me-3" style="width: 50px;"
                                 alt="VIP">
                            <div>
                                <div class="fw-bold">Membership Validity</div>
                                <div>Active till <span
                                        class="fw-bold text-warning">{{!empty($user->subscription_end) ? date('d M Y',strtotime($user->subscription_end)) : ""}}</span>
                                </div>
                            </div>
                        </div>
                        <!-- <img src="{{ url('public/assets/images/athlete.png') }}" class="mt-3 w-100 rounded" alt="Athlete"> -->
                    </div>

                    <!-- Membership Calculator -->
                    <div class="calculator-card">
                        <h5>Membership Calculator</h5>
                        <div class="slider-container1">
                            <input type="range" min="0" max="25000" value="5000" class="slider" id="savingsSlider1"
                                   step="500">
                            <span class="tooltip" id="sliderTooltip">5000</span>
                        </div>
                        <div class="text-line">Spend <strong>₹<span id="spendAmount">5000</span></strong>/month?</div>
                        <div class="text-line">You save <strong>₹<span id="saveAmount">840</span></strong>/year with
                            NutraPass!
                        </div>

                        <div class="bottom-section" id="seeSave">
                            <img src="https://img.icons8.com/ios-filled/50/000000/calculator.png" width="20"
                                 alt="Calculator Icon">
                            See how much you save ₹<span id="bottomSave">840</span>
                        </div>
                    </div>


                </div>

                <div class="col-md-7 ">
                    <div class="bg-info text-white p-4 rounded-box h-100 border_radius_white">
                        <div class="row mb-3">
                            <div class="col-6 fw-bold"></div>
                            <div class="col-3 text-center fw-bold">NutraPass<br>Member</div>
                            <div class="col-3 text-center fw-bold">Non-member</div>
                        </div>

                        <!-- Feature Rows -->
                        <div class="row mb-3 align-items-center border-radius">
                            <div class="col-6 icon-text text-white">
                                <img src="{{ url('public/assets/images/Newspaper.png') }}" style="width:20px">
                                10% OFF every order
                            </div>
                            <div class="col-3 text-center checkmark">✔</div>
                            <div class="col-3 text-center crossmark">✖</div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-6 icon-text text-white">
                                <img src="{{ url('public/assets/images/Sale.png') }}" style="width:20px">
                                Exclusive Discounts
                            </div>
                            <div class="col-3 text-center checkmark">✔</div>
                            <div class="col-3 text-center crossmark">✖</div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-6 icon-text text-white">
                                <img src="{{ url('public/assets/images/Support.png') }}" style="width:20px">
                                Priority Support
                            </div>
                            <div class="col-3 text-center checkmark">✔</div>
                            <div class="col-3 text-center crossmark">✖</div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-6 icon-text text-white">
                                <img src="{{ url('public/assets/images/image 1403.png') }}" style="width:20px">
                                Monthly Freebie Box
                            </div>
                            <div class="col-3 text-center checkmark">✔</div>
                            <div class="col-3 text-center crossmark">✖</div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-6 icon-text text-white">
                                <img src="{{ url('public/assets/images/image 1404.png') }}" style="width:20px">
                                Early Access & Secret Sales
                            </div>
                            <div class="col-3 text-center checkmark">✔</div>
                            <div class="col-3 text-center crossmark">✖</div>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        <section class="p-5 m-2 " style="background: #063232; width: 99%;border-radius:10px">
            <div class="row p-3">
                <div class="col-md-6">
                    <img src="{{ url('public/assets/images/passbanner.svg') }}">
                </div>
                <div class="col-md-6 border_radius_white text-center">
                    <div>
                        <img src="{{ url('public/assets/images/nutrapasslogo.svg') }}">
                        <h3 class="text-white">Join Wellness+ Membership</h3>
                    </div>
                    <div>
                        <div class="scroll-container" id="planScroll">
                            @foreach($subscription['subscription_plans'] as $key =>  $plan)
                                @php
                                    $permonth = (int)$plan->price / (int)$plan->duration;
                                    $permonth = round($permonth);
                                     $standardMonthlyPrice = (int)$plan->price;
$totalStandardPrice = $standardMonthlyPrice * $plan->duration;
                                     $savePercent = 0;
    if ($totalStandardPrice > 0) {
        $savePercent = round((($totalStandardPrice - $plan->price) / $totalStandardPrice) * 100);
    }
                                @endphp
                                <div class="plan {{$key == 0 ? 'selected' : ''}}" onclick="selectPlan(this)">
                                    @if($plan->is_best_value == 1)
                                        <h4 class="text-white">Best Value</h4>
                                    @endif

                                    <div class="months">{{$plan->duration ?? ''}}</div>
                                    <div class="details">
                                        months<br>
                                        ₹ {{$permonth}}/mo<br>
                                        <strong>SAVE {{$savePercent}}%</strong>
                                    </div>
                                    <hr>
                                    <div class="price">₹ {{$plan->price ?? ''}}</div>
                                </div>
                            @endforeach

                            <!-- Add more if needed -->
                        </div>
                    </div>

                    <div class="mt-20">
                        <a href="#" class="btn btn-warning text-white w-50">JOIN NOW</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="p-5 m-2 " style="background: #FFFFFF00; width: 99%;border-radius:10px">
            <div class="row p-3">
                <h3 class="text-center">FAQ</h3>
                @foreach($subscription['faqs'] as $faq)
                    <div class="faq-item mt-2">
                        <div class="faq-question">
                            <span>{{ strip_tags($faq->question ?? 'No question') }}</span>
                            <span class="icon">+</span>
                        </div>
                        <div class="faq-answer">
                            {{ strip_tags($faq->answer ?? 'No answer available') }}
                        </div>
                    </div>

                @endforeach


            </div>
        </section>


        <section class="p-5 m-2 d-none">
            <h2>Our Member Reviews</h2>
            <div class="slider-container" id="sliderContainer">
                <div class="slider" id="slider">
                    <!-- Slide 1 -->
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="slide">
                            <img src="{{url('public/assets/images/reviews.png')}}" alt="User">
                            <div class="review-content">
                                <h3>Amit Kumar</h3>
                                <small>Gym Trainer</small>
                                <p>
                                    “Since I joined NutraCore Membership, my fitness journey has reached new heights!
                                    The
                                    exclusive access to personalized nutrition plans has been a game changer...”
                                    <br><br>
                                    - Enhanced workout efficiency<br>
                                    - Personalized meal plans<br>
                                    - A vibrant community that inspires and supports
                                </p>
                            </div>
                        </div>
                    @endfor


                </div>
            </div>

            <div class="bullets" id="bullets">
                <span onclick="goToSlide(0)" class="active"></span>
                <span onclick="goToSlide(1)"></span>
                <span onclick="goToSlide(2)"></span>
            </div>
        </section>


        <section class="p-4 m-5 d-none" style="background: #00A8A870; border-radius: 30px;">
            <div class="row align-items-stretch" style="min-height: 350px;">
                <!-- Left Side: Text and Subscription -->
                <div class="col-md-6 text-white px-4 d-flex flex-column justify-content-center">
                    <h3 class="fw-bold">Stay home & get your health</h3>
                    <h3 class="fw-bold">needs from our shop</h3>
                    <p class="mb-4">Start You'r Health Shopping with <strong>NutraCore</strong></p>

                    <!-- Email Subscription Form -->
                    <form class="d-flex bg-white rounded-pill overflow-hidden shadow-sm" style="max-width: 400px;">
                        <input type="email" class="form-control border-0 ps-4" placeholder="Your email address"
                               required>
                        <button type="submit" class="btn text-white px-4"
                                style="background-color: #00A8A8;">Subscribe
                        </button>
                    </form>
                </div>

                <!-- Right Side: Full Image -->
                <div class="col-md-6 p-0">
                    <div style="height: 100%; width: 100%; border-radius: 30px; overflow: hidden;">
                        <img src="{{ url('public/assets/images/commonbanner.png') }}" class="w-100 h-100"
                            style="object-fit: cover;" alt="Banner">
                    </div>
                </div>
            </div>
        </section>


    </main>

    <script>
        const slider = document.getElementById("savingsSlider1");
        const tooltip = document.getElementById("sliderTooltip");
        const spendAmount = document.getElementById("spendAmount");
        const saveAmount = document.getElementById("saveAmount");
        const bottomSave = document.getElementById("bottomSave");

        function updateSlider() {
            const value = slider.value;
            tooltip.innerHTML = value;
            spendAmount.innerHTML = value;

            // Example save calculation
            const save = Math.round(value * 0.168);




            saveAmount.innerHTML = save;
            bottomSave.innerHTML = save;

            // Position tooltip correctly
            const sliderWidth = slider.offsetWidth;
            const max = slider.max;
            const min = slider.min;
            const percent = (value - min) / (max - min);
            const offset = percent * sliderWidth;

            tooltip.style.left = `${offset}px`;
        }

        // Initial position
        updateSlider();

        // Update on slider move
        slider.addEventListener("input", updateSlider);
    </script>

    <script>
        // Highlight selected card
        function selectPlan(el) {
            document.querySelectorAll('.plan').forEach(p => p.classList.remove('selected'));
            el.classList.add('selected');
        }

        // Drag-scroll logic
        const container = document.getElementById('planScroll');
        let isDown = false;
        let startX, scrollLeft;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.classList.add('active');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });
        container.addEventListener('mouseleave', () => {
            isDown = false;
        });
        container.addEventListener('mouseup', () => {
            isDown = false;
        });
        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // speed
            container.scrollLeft = scrollLeft - walk;
        });

        // Touch support
        let startTouchX = 0;
        container.addEventListener('touchstart', (e) => {
            startTouchX = e.touches[0].clientX;
            scrollLeft = container.scrollLeft;
        });
        container.addEventListener('touchmove', (e) => {
            const x = e.touches[0].clientX;
            const walk = (x - startTouchX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    </script>


    <script>
        document.querySelectorAll('.faq-question').forEach((question) => {
            question.addEventListener('click', () => {
                const isActive = question.classList.contains('active');

                // Close all
                document.querySelectorAll('.faq-question').forEach(q => {
                    q.classList.remove('active');
                    q.querySelector('.icon').textContent = '+';
                });
                document.querySelectorAll('.faq-answer').forEach(a => {
                    a.classList.remove('open');
                });

                // Open current if not already active
                if (!isActive) {
                    question.classList.add('active');
                    question.querySelector('.icon').textContent = '−';
                    question.nextElementSibling.classList.add('open');
                }
            });
        });

        // Initialize first item as open
        document.querySelector('.faq-question').classList.add('active');
    </script>

@endsection
