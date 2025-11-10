@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $fixed_banner_1 = [];
    $fixed_banner_2 = [];
    $fixed_banner_3 = [];
    $download_banner = [];
    $small_banners = [];
    if (!empty($banners)) {
        foreach ($banners as $banner) {
            if ($banner->type == 'Fixed_banner1') {
                $fixed_banner_1 = $banner;
            }
            if ($banner->type == 'Fixed_banner2') {
                $fixed_banner_2 = $banner;
            }
            if ($banner->type == 'Fixed_banner3') {
                $fixed_banner_3 = $banner;
            }
            if ($banner->type == 'download_banner') {
                $download_banner = $banner;
            }
            if ($banner->type == 'small_banner') {
                $small_banners[] = $banner;
            }
        }
    }
    $banner_types = ['', 'product', 'brand', 'category', 'link'];

    ?>



    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Hero Section */
        .hero-section {
            background: url('https://via.placeholder.com/1200x400') center/cover no-repeat;
            color: white;
            text-align: center;
            padding: 100px 20px;
            border-radius: 10px;
            position: relative;
        }
        .hero-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        .hero-section .content {
            position: relative;
            z-index: 1;
        }

        /* Features Row */
        .features-row .col {
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            padding: 20px;
            font-weight: 500;
        }

        /* Membership Section */
        .membership-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            margin-top: 40px;
            padding: 20px;
        }

        .membership-section h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .membership-table {
            margin-top: 20px;
        }

        .membership-table th, .membership-table td {
            text-align: center;
            vertical-align: middle;
        }

        .tick {
            color: #28a745;
            font-weight: bold;
        }

        .cross {
            color: #dc3545;
            font-weight: bold;
        }

        .join-btn {
            background-color: #00a69c;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .join-btn:hover {
            background-color: #00877f;
        }
    </style>

    <style>
        .center {
            width: 100%;
            margin: auto;
            text-align: left;
            padding:30px;
        }

        .center img {
            width: 100%;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.5s, opacity 0.5s;
            background: pink;
        }

        /* Highlight center slide */
        .slick-center img {
            transform: scale(1.1);
            opacity: 1;
        }

        .slick-slide img {
            opacity: 0.5;
        }

        /* Optional: remove arrows on small devices */
        @media (max-width: 768px) {
            .center img {
                height: 250px;
            }
        }
    </style>


    <main class="main">

        <div class="container">

           <div style="position: relative;">
               <div style="height: 466px; background:#E1C16C; border-top-right-radius: 10px; border-top-left-radius: 10px;" >
                   <img src="{{url('public/assets/images/vikas_new.png')}}" style="width: 100%; height: 100%;" " />
               </div>
               <div style="background:black;padding:20px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; display: flex; justify-content: space-between">
                   <div style="padding:10px;width:40%; display:flex; justify-content: center; flex-direction: column; align-items: center">
                       <p style="color: white;">Membership Status</p>
                       <h5 style="color:#ff9900;">GOLD</h5>
                   </div>
                   <div style="padding:10px;width:40%; display:flex; justify-content: center; flex-direction: column; align-items: center">
                       <p style="color: white;">Membership Status</p>
                       <h5 style="color:#ff9900;">GOLD</h5>
                   </div>
                   <div style="padding:10px;width:40%; display:flex; justify-content: center; flex-direction: column; align-items: center">
                       <p style="color: white;">Membership Status</p>
                       <h5 style="color:#ff9900;">GOLD</h5>
                   </div>
               </div>
           </div>



            <!-- Benefits Row -->
            <div class="row g-3 features-row" style="padding-left: 40px; margin-top: 30px; padding-right: 40px;">
               <div style="padding:5px;" class="col-md-3 col-6">
                   <div style="background: white; text-align: center; border:1px solid gray; padding:10px;">
                       <img src="{{url('public/assets/images/ic_twotone-discount.png')}}" />

                       <p style="color: #171616;">10% OFF every order</p>
                   </div>
               </div>

                <div style="padding:5px;" class="col-md-3 col-6">
                    <div style="background: white; text-align: center; border:1px solid gray; padding:10px;">
                        <img src="{{url('public/assets/images/streamline-cyber-color_delivery-package-2.png')}}"/>

                        <p style="color: #171616;">Free Express Delivery</p>
                    </div>
                </div>

                <div style="padding:5px;" class="col-md-3 col-6">
                    <div style="background: white; text-align: center; border:1px solid gray; padding:10px;">
                        <img src="{{url('public/assets/images/streamline-cyber-color_delivery-package-open.png')}}"/>

                        <p style="color: #171616;">Monthly Freebie Box</p>
                    </div>
                </div>

                <div style="padding:5px;" class="col-md-3 col-6">
                    <div style="background: white; text-align: center; border:1px solid gray; padding:10px;">
                        <img src="{{url('public/assets/images/solar_ticket-sale-line-duotone.png')}}"/>

                        <p style="color: #171616;">Early Access & Secret Sales</p>
                    </div>
                </div>

            </div>

            <!-- Membership Section -->
            <div class="membership-section" style="background: #0a0a0a;">
                <div class="row g-8"> <!-- g-4 = column gap -->

                    <!-- Left Column (5 parts width) -->
                    <div class="col-md-5">
                        <div class="row g-3">
                            <!-- Top Box -->
                            <div class="col-12" style="padding:0; margin:0; position:relative; display: flex; align-items: center; justify-content: center;">
                                <img src="{{url('public/assets/images/MembershipValidity.png')}}" style="width: 100%;"/>
                                <div style="position: absolute; top: 0; left: 0; padding:35px;">
                                    <img src="{{url('public/assets/images/vip_icon.png')}}" style="width:60px; height: 60px;"/>
                                    <h5 style="color: white;">Membership Validity</h5>
                                    <h6 style="color: white;">Active till <span class="fs-bold" style="color: #ff9900;font-weight: bold;">31/Dec/23</span></h6>
                                </div>


                            </div>
                            <!-- Bottom Box -->
                            <div class="col-12" style="padding-top:10px; padding-left: 0;padding-right: 0; padding-bottom: 0; margin:0;  display: flex; align-items: center; justify-content: center;">


                                            <div style="
                                                        background: linear-gradient(135deg, #f7d77a, #e4b958);
                                                        border-radius: 30px;
                                                        overflow: hidden;
                                                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                                        text-align: left;
                                                        font-family: 'Poppins', sans-serif;
                                                        width: 100%;
                                                        height: 100%;
                                                         border: 2px solid #ddd;


                                                    ">
                                              <div style="padding:35px; padding-bottom: 20px">
                                                  <h4 style="font-weight: 600; letter-spacing: 1px; color: #2c2c2c; ">
                                                      MEMBERSHIP CALCULATOR
                                                  </h4>

                                                  <p style="margin: 18px 0 10px; color: #2c2c2c; font-size: 18px;">
                                                      Spend <span style="font-weight: 600;">₹3,000</span>/month?
                                                  </p>

                                                  <p style="color: #2c2c2c; font-size: 18px; margin-bottom: 20px;">
                                                      You save <span style="color: #00a3ad; font-weight: 600;">₹2,700</span>/year with NutraPass!
                                                  </p>
                                              </div>

                                                <div style="
                                                    background: #fff;
                                                    padding: 14px 14px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    gap: 8px;
                                                    font-size: 14px;
                                                    color: #2c2c2c;
                                                    font-weight: 500;
                                                ">
                                                    <img src="{{url('public/assets/images/calc_icon.png')}}" style="width:40px; height: 40px;"/>
                                                    <span>See how much you save</span>
                                                </div>
                                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (7 parts width) -->
                    <div class="col-md-7" style="padding:0; margin:0;">
                        <div style="
        background-color: #10b6b6;
        border-radius: 30px;
        padding: 25px;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        height: 100%;
        font-family: 'Poppins', sans-serif;
        border: 2px solid white;
    ">

                            <div style="display: flex; justify-content: end; gap: 50px; color: white; margin-bottom: 20px;">
                                <h4 style="color: white;">NutraPass<br>
                                    Member</h4>
                                <h4 style="color: white;">Non<br>member</h4>
                            </div>
                            <div style="display: flex; justify-content: space-between; justify-items: center;">
                                <div style="display: flex; gap:20px; align-items: center;">
                                    <div>
                                        <img src="{{url('public/assets/images/Newspaper.png')}}" style="width:40px; height: 40px;"/>
                                    </div>
                                    <div>
                                        <h5 style="color: white;">10% OFF every order</h5>
                                    </div>
                                </div>
                                <div style="margin-left: 50px;">
                                    <img src="{{url('public/assets/images/right.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/worng.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; justify-items: center;margin-top: 15px;">
                                <div style="display: flex; gap:20px; align-items: center;">
                                    <div>
                                        <img src="{{url('public/assets/images/Sale.png')}}" style="width:40px; height: 40px;"/>
                                    </div>
                                    <div>
                                        <h5 style="color: white;">Exclusive Discounts</h5>
                                    </div>
                                </div>
                                <div style="margin-left: 70px;">
                                    <img src="{{url('public/assets/images/right.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/worng.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; justify-items: center;margin-top: 15px;">
                                <div style="display: flex; gap:20px; align-items: center;">
                                    <div>
                                        <img src="{{url('public/assets/images/Support.png')}}" style="width:40px; height: 40px;"/>
                                    </div>
                                    <div>
                                        <h5 style="color: white;">Priority Support</h5>
                                    </div>
                                </div>
                                <div style="margin-left: 105px;">
                                    <img src="{{url('public/assets/images/right.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/worng.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; justify-items: center;margin-top: 15px;">
                                <div style="display: flex; gap:20px; align-items: center;">
                                    <div>
                                        <img src="{{url('public/assets/images/image 1403.png')}}" style="width:40px; height: 40px;"/>
                                    </div>
                                    <div>
                                        <h5 style="color: white;"> Monthly Freebie Box</h5>
                                    </div>
                                </div>
                                <div style="margin-left: 65px;">
                                    <img src="{{url('public/assets/images/right.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/worng.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; justify-items: center; margin-top: 15px;">
                                <div style="display: flex; gap:20px; align-items: center;">
                                    <div>
                                        <img src="{{url('public/assets/images/image 1404.png')}}" style="width:40px; height: 40px;"/>
                                    </div>
                                    <div>
                                        <h5 style="color: white;">Early Access & Secret Sales</h5>
                                    </div>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/right.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                                <div>
                                    <img src="{{url('public/assets/images/worng.png')}}" style="width:30px; height: 30px;"/>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>



            </div>


                <!-- Membership Section -->
                <section style="background-color:#063232; color:white; padding:40px 0; margin-top: 50px;">
                    <div class="container">
                        <div class="row align-items-center">

                            <!-- Left Image -->
                            <div class="col-md-6 text-center">
                                <div style="display:flex; justify-content:center; gap:15px; padding:10px;">
                                    <img src="{{url('public/assets/images/Body.png')}}" style="width:100%" />                                </div>
                            </div>

                            <!-- Right Membership Details -->
                            <div class="col-md-6 text-center" style="height: 100%">
                                <div style="border:2px solid white; padding:30px; border-radius:28px;">
                                   <div style="display: flex; justify-content: center; align-items: center; justify-items: center; flex-direction: column">
                                       <img src="{{url('public/assets/images/logo.png')}}"
                                            style="width: 30%; display: block; border-radius: 12px;">
                                       <h2 style="font-weight:700; color: white; font-size: 30px;" >Join Wellness+ Membership</h2>
                                   </div>

                                    <div class="position-relative banner-img" style="position: relative;">




                                        <!-- Overlay scrollable plans -->
                                      <div style="display: flex; align-items: center; justify-content: center;width: 100%">
                                          <div class="scroll-container-right" style=" display: flex;
                                            gap: 20px;
                                            overflow-x: auto;
                                            scroll-behavior: smooth;
                                            width: 100%;
                                            ">
                                              <div class="plan active" onclick="selectPlan(this)">
                                                  <h4 class="plan-title">Best Value</h4>
                                                  <div class="1">12</div>
                                                  <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                                  <hr>
                                                  <div class="price">₹ 8,000</div>
                                              </div>

                                              <div class="plan" onclick="selectPlan(this)">
                                                  <h4 class="plan-title highlight">Best Value</h4>
                                                  <div class="months">6</div>
                                                  <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                                  <hr>
                                                  <div class="price">₹ 8,000</div>
                                              </div>

                                              <div class="plan" onclick="selectPlan(this)">
                                                  <div class="months"style="padding-top:25px;">12</div>
                                                  <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                                  <hr>
                                                  <div class="price">₹ 8,000</div>
                                              </div>

{{--                                              <div class="plan" onclick="selectPlan(this)">--}}
{{--                                                  <div class="months" style="padding-top:25px;">3</div>--}}
{{--                                                  <div class="details">months<br>₹ 1000/mo<br><strong>SAVE 25%</strong></div>--}}
{{--                                                  <hr>--}}
{{--                                                  <div class="price">₹ 3,000</div>--}}
{{--                                              </div>--}}
                                          </div>
                                      </div>

                                        <style>
                                            /* General */
                                            * {
                                                font-family: "Poppins", sans-serif;
                                                box-sizing: border-box;
                                            }

                                            /* Main Container */
                                            .nutrapass-container {
                                                display: flex;
                                                justify-content: space-between;
                                                align-items: stretch;
                                                background: linear-gradient(90deg, #f8d57a, #f6c34a);
                                                border-radius: 16px;
                                                padding: 30px;
                                                width: 100%;
                                                max-width: 850px;
                                                margin: 50px auto;
                                                color: #000;
                                                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                                            }

                                            /* Left Section */
                                            .left-section {
                                                flex: 1;
                                                display: flex;
                                                flex-direction: column;
                                                justify-content: center;
                                                gap: 8px;
                                            }

                                            .left-section h3 {
                                                font-size: 20px;
                                                font-weight: 700;
                                            }

                                            .left-section p {
                                                font-size: 14px;
                                                margin: 4px 0;
                                            }

                                            .join-btn {
                                                margin-top: 10px;
                                                background: #fff;
                                                color: #000;
                                                border: none;
                                                padding: 10px 20px;
                                                border-radius: 8px;
                                                font-weight: 600;
                                                width: fit-content;
                                                cursor: pointer;
                                                transition: all 0.3s ease;
                                            }

                                            .join-btn:hover {
                                                background: #fef1c5;
                                            }

                                            /* Right Section */
                                            .right-section {
                                                flex: 1.2;
                                                display: flex;
                                                flex-direction: column;
                                                align-items: center;
                                                justify-content: space-between;
                                            }

                                            .logo {
                                                text-align: center;
                                                margin-bottom: 10px;
                                            }

                                            .logo h2 {
                                                font-size: 22px;
                                                font-weight: 700;
                                            }

                                            .nutra {
                                                color: #fff;
                                            }

                                            .pass {
                                                background: #fff;
                                                color: #f5b700;
                                                padding: 0 5px;
                                                border-radius: 4px;
                                            }

                                            .tagline {
                                                font-size: 10px;
                                                color: #333;
                                                font-weight: 500;
                                            }

                                            /* Plans Scroll Container */
                                            .scroll-container-right {
                                                display: flex;
                                                justify-content: flex-start;
                                                gap: 20px;
                                                overflow-x: auto;
                                                overflow-y: hidden;
                                                scroll-behavior: smooth;
                                                width: 100%;
                                                padding-bottom: 10px;
                                                scrollbar-width: none; /* Firefox */
                                            }

                                            .scroll-container-right::-webkit-scrollbar {
                                                display: none; /* Chrome/Safari/Edge */
                                            }

                                            /* Plan Cards */
                                            .plan {
                                                background: rgba(255, 255, 255, 0.8);
                                                color: #333;
                                                padding: 10px;
                                                padding-top: 0px;
                                                border-radius: 12px;
                                                width: 100px;
                                                text-align: center;
                                                cursor: pointer;
                                                transition: all 0.3s
                                                ease;
                                                border: 1px solid transparent;
                                            }

                                            .plan:hover {
                                                transform: translateY(-4px);
                                            }

                                            .plan.active {
                                                background: #fff;
                                                border: 2px solid #f5b700;
                                                color: #000;
                                                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                                                transform: scale(1.05);
                                            }

                                            /* 👇 Highlight "months" text in active plan */
                                            .plan.active .months {
                                                color: #f5b700;
                                            }

                                            .plan .plan-title {
                                                background: #f6c34a;
                                                color: white;
                                                font-size: 12px;
                                                font-weight: 600;
                                                /* padding: 3px 6px; */
                                                border-radius: 3px;
                                                margin-bottom: 8px;
                                                display: inline-block;
                                            }

                                            .plan .months {
                                                font-size: 26px;
                                                font-weight: 700;
                                            }

                                            .plan .details {
                                                font-size: 12px;
                                                line-height: 1.4;
                                                margin-top: 6px;
                                            }

                                            .plan .price {
                                                font-weight: 600;
                                                font-size: 13px;
                                            }

                                            .highlight {
                                                background: #f5b700 !important;
                                            }

                                            .save {
                                                color: #ff3b3b;
                                                font-weight: 700;
                                            }

                                            /* Responsive */
                                            @media (max-width: 768px) {
                                                .nutrapass-container {
                                                    flex-direction: column;
                                                    text-align: center;
                                                }

                                                .left-section,
                                                .right-section {
                                                    align-items: center;
                                                }

                                                .scroll-container-right {
                                                    justify-content: center;
                                                }
                                            }
                                        </style>

                                    </div>

                                    <style>
                                        .scroll-container-right {
                                            display: flex;
                                            overflow-x: auto;
                                            /* Horizontal scroll */
                                            justify-content: flex-end;
                                            /* Align plans to right */
                                            gap: 15px;
                                            padding: 10px;
                                            scroll-behavior: smooth;
                                            /* smooth scroll */
                                        }

                                        .scroll-container-right::-webkit-scrollbar {
                                            height: 6px;
                                            /* scrollbar height */
                                        }

                                        .scroll-container-right::-webkit-scrollbar-thumb {
                                            background: #888;
                                            border-radius: 3px;
                                        }

                                        .plan {
                                            min-width: 150px;
                                            background: transparent;
                                            color: white;
                                            text-align: center;
                                            padding: 15px;
                                            border-radius: 10px;
                                            cursor: pointer;
                                            flex-shrink: 0;
                                            /* prevent shrinking */
                                        }

                                        .plan.selected {
                                            background: white;
                                            color: black;
                                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                                            border: 2px solid #f5b300;
                                        }

                                        /* Recolor text inside selected plan */
                                        .plan.selected h4,
                                        .plan.selected .months,
                                        .plan.selected .price {
                                            color: #f5b300;
                                        }

                                        .plan.selected h4 {
                                            background: linear-gradient(90deg, #f7ce68, #f5b300);
                                            color: #0A6A6A
                                        }

                                        .months {
                                            font-size: 28px;
                                            font-weight: bold;
                                            color: white
                                        }

                                        .plan h4 {
                                            font-size: 18px;
                                            margin-bottom: 10px;
                                            padding: 5px 10px;
                                            display: inline-block;
                                            border-radius: 5px;
                                            background: transparent;
                                            color: white;
                                        }

                                        .plan:not(:has(h4)) .details {
                                            margin-top: 20px;
                                            /* or any value that aligns the text properly */
                                        }
                                    </style>

                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {
                                            const plans = document.querySelectorAll(".plan");

                                            // ✅ Default first plan active
                                            if (plans.length > 0) {
                                                plans[0].classList.add("active");
                                                plans[0].querySelector(".months").style.color = "#db6001";
                                            }

                                            // ✅ Click event for each plan
                                            plans.forEach(plan => {
                                                plan.addEventListener("click", function () {
                                                    // sab plans se active class hatao
                                                    plans.forEach(p => {
                                                        p.classList.remove("active");
                                                        p.querySelector(".months").style.color = "#000"; // reset color
                                                    });

                                                    // clicked plan ko active karo
                                                    this.classList.add("active");
                                                    this.querySelector(".months").style.color = "#db6001";
                                                });
                                            });
                                        });
                                    </script>

                                    <button class="btn btn-warning mt-4 px-4 py-2 fw-bold" style="width:50% ">JOIN NOW</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section style="background:white; padding:40px 0;">
                    <div class="container">
                        <h4 class="text-center" style="color:#00a57a; font-weight:700;">FAQ</h4>
                        <p class="text-center text-muted">We’re here to help you with anything and everything on NutraCore.</p>

                        <div class="accordion mt-4" id="faqAccordion">

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        What is NutraCore?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem ipsum has been the industry’s standard dummy text since the 1500s.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item mt-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        How to apply for a Subscription?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You can apply by visiting our official website and choosing your preferred plan.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item mt-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        How to know status of a Delivery?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Once your product is shipped, you’ll receive a tracking link on your registered email or phone number.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>


            <section style="overflow: hidden">
                <h1 style="text-align: center; font-size: 35px; padding-top: 30px;color: #00A8A8;">Our Member Reviews</h1>
                <div class="center">

                    <div style="display: flex; align-items: center; padding: 20px; gap:30px; ">
                       <div style="border:1px solid gray; overflow: hidden;border-radius: 10px; display: flex; align-items: center; gap:30px; padding:20px;">
                           <div style="border:3px solid #ff9900;overflow:hidden; border-radius: 8px">
                               <img src="{{url('public/assets/images/reviews.png')}}" />
                           </div>
                           <div class="testimonial-content">
                               <h3 style="color: #ff9900;">Amit Kumar</h3>
                               <p class="role">Gym Trainer</p>
                               <p class="quote" style="font-size: 12px;">
                                   <span class="quote-icon" style="color:#ff9900;font-size: 28px;">❝</span>
                                   Since I joined NutriCare Membership, my fitness journey has reached new heights! The exclusive access to personalized nutrition plans has been a game changer, and I’ve noticed significant improvements in my energy levels and overall performance. The community support is fantastic, and I appreciate the variety of resources available. It’s truly a vital part of my wellness routine!
                                   <span class="quote-icon" style="color: #ff9900; font-size: 28px;">❞</span>
                               </p>
                               <ul style="font-size: 12px;">
                                   <li>Enhanced workout efficiency with tailored fitness resources.</li>

                               </ul>
                           </div>
                       </div>
                    </div>
                    <div style="display: flex; align-items: center; padding: 20px; gap:30px; ">
                        <div style="border:1px solid gray; overflow: hidden;border-radius: 10px; display: flex; align-items: center; gap:30px; padding:20px;">
                            <div style="border:3px solid #ff9900;overflow:hidden; border-radius: 8px">
                                <img src="{{url('public/assets/images/reviews.png')}}" />
                            </div>
                            <div class="testimonial-content">
                                <h3 style="color: #ff9900;">Amit Kumar</h3>
                                <p class="role">Gym Trainer</p>
                                <p class="quote" style="font-size: 12px;">
                                    <span class="quote-icon" style="color:#ff9900;font-size: 28px;">❝</span>
                                    Since I joined NutriCare Membership, my fitness journey has reached new heights! The exclusive access to personalized nutrition plans has been a game changer, and I’ve noticed significant improvements in my energy levels and overall performance. The community support is fantastic, and I appreciate the variety of resources available. It’s truly a vital part of my wellness routine!
                                    <span class="quote-icon" style="color: #ff9900; font-size: 28px;">❞</span>
                                </p>
                                <ul style="font-size: 12px;">
                                    <li>Enhanced workout efficiency with tailored fitness resources.</li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; padding: 20px; gap:30px; ">
                        <div style="border:1px solid gray; overflow: hidden;border-radius: 10px; display: flex; align-items: center; gap:30px; padding:20px;">
                            <div style="border:3px solid #ff9900;overflow:hidden; border-radius: 8px">
                                <img src="{{url('public/assets/images/reviews.png')}}" />
                            </div>
                            <div class="testimonial-content">
                                <h3 style="color: #ff9900;">Amit Kumar</h3>
                                <p class="role">Gym Trainer</p>
                                <p class="quote" style="font-size: 12px;">
                                    <span class="quote-icon" style="color:#ff9900;font-size: 28px;">❝</span>
                                    Since I joined NutriCare Membership, my fitness journey has reached new heights! The exclusive access to personalized nutrition plans has been a game changer, and I’ve noticed significant improvements in my energy levels and overall performance. The community support is fantastic, and I appreciate the variety of resources available. It’s truly a vital part of my wellness routine!
                                    <span class="quote-icon" style="color: #ff9900; font-size: 28px;">❞</span>
                                </p>
                                <ul style="font-size: 12px;">
                                    <li>Enhanced workout efficiency with tailored fitness resources.</li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; padding: 20px; gap:30px; ">
                        <div style="border:1px solid gray; overflow: hidden;border-radius: 10px; display: flex; align-items: center; gap:30px; padding:20px;">
                            <div style="border:3px solid #ff9900;overflow:hidden; border-radius: 8px">
                                <img src="{{url('public/assets/images/reviews.png')}}" />
                            </div>
                            <div class="testimonial-content">
                                <h3 style="color: #ff9900;">Amit Kumar</h3>
                                <p class="role">Gym Trainer</p>
                                <p class="quote" style="font-size: 12px;">
                                    <span class="quote-icon" style="color:#ff9900;font-size: 28px;">❝</span>
                                    Since I joined NutriCare Membership, my fitness journey has reached new heights! The exclusive access to personalized nutrition plans has been a game changer, and I’ve noticed significant improvements in my energy levels and overall performance. The community support is fantastic, and I appreciate the variety of resources available. It’s truly a vital part of my wellness routine!
                                    <span class="quote-icon" style="color: #ff9900; font-size: 28px;">❞</span>
                                </p>
                                <ul style="font-size: 12px;">
                                    <li>Enhanced workout efficiency with tailored fitness resources.</li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </div>




    </main>

    <!-- jQuery + Slick JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.center').slick({
                centerMode: true,
                centerPadding: '200px', // 👈 side slides thoda visible rahe
                slidesToShow: 1, // 👈 ek main slide dikhe
                arrows: true,
                autoplay: true,
                autoplaySpeed: 2000,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            centerPadding: '100px',
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: false,
                            centerPadding: '40px',
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            arrows: false,
                            centerPadding: '0px',
                        }
                    }
                ]
            });
        });
    </script>

    <script>
        function initSlider(trackId) {
            const track = document.getElementById(trackId);
            const slides = track.querySelectorAll('.slide');
            let index = 0;

            function showSlide(i) {
                index = (i + slides.length) % slides.length;
                track.style.transform = `translateX(-${index * 100}%)`;
            }

            // attach events to buttons with matching data-target
            document.querySelectorAll(`[data-target="${trackId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.classList.contains('prev-btn')) {
                        showSlide(index - 1);
                    } else {
                        showSlide(index + 1);
                    }
                });
            });
        }

        // Init sliders
        initSlider('videoSlider');
        initSlider('testimonialSlider');
    </script>

    <script>
        function selectPlan(selected) {
            // Remove active class from all
            document.querySelectorAll('.plan').forEach(plan => {
                plan.classList.remove('selected');
            });

            // Add active class to clicked plan
            selected.classList.add('selected');
        }
    </script>

@endsection
