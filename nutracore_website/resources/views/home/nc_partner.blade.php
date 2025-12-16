@extends('home.layout')
@section('content')
    @php
        $faqs = \App\Models\FAQ::where('type','nc_partner')->where('status',1)->where('is_delete',0)->get();
        $settings = DB::table('settings')->where('id',1)->first();
    @endphp
    <style>
        .partner-btn {
            position: absolute;
            bottom: 100px;
            right: 100px;
            background: #00A8A8;
            color: #fff;
            padding: 12px 22px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            transition: 0.2s ease;
        }

        .partner-btn:hover {
            background: #00A8A8;
            transform: translateY(-2px);
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .partner-btn {
                padding: 10px 18px;
                font-size: 14px;
                bottom: 10px;
                right: 10px;
            }
        }
    </style>
    <style>
        .partner-section {
            padding: 60px 20px;
            margin: auto;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 40px;
            font-family: 'Poppins', sans-serif;
        }

        .partner-left {
            flex: 1 1 45%;
        }

        .partner-left h2 {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .partner-left p {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
        }

        .partner-right {
            flex: 1 1 45%;
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(2, 1fr);
        }

        .feature-box {
            background: #f8f8f8;
            padding: 20px 25px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .feature-box h4 {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .feature-box p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .partner-left, .partner-right {
                flex: 1 1 100%;
            }

            .partner-right {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <style>
        .join-section {
            background: #e6f5f6;
            padding: 60px 20px;
            border-radius: 30px;
            margin: 40px auto;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }

        .join-section h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .join-section p.subtitle {
            font-size: 17px;
            color: #555;
            margin-bottom: 40px;
        }

        .join-grid {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
        }

        .join-item {
            text-align: center;
            width: 180px;
        }

        .join-item img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
        }

        .join-item h4 {
            margin-top: 15px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .join-item {
                width: 150px;
            }

            .join-item img {
                width: 150px;
                height: 150px;
            }
        }

        .benefits-section {
            text-align: center;
            padding: 50px 20px;

            margin: auto;
        }

        .benefits-section .title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .benefits-section .subtitle {
            color: #555;
            margin: 0 auto 50px;
            font-size: 18px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .benefits-grid1 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
        }

        .benefit-item {
            text-align: center;
        }

        .benefit-item .icon {
            width: 80px;
            margin-bottom: 20px;
        }

        .benefit-item h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .benefit-item p {
            color: #555;
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .benefits-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .benefits-grid1 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .benefits-grid1 {
                grid-template-columns: 1fr;
            }

            .benefits-section .title {
                font-size: 28px;
            }

            .benefits-section .subtitle {
                font-size: 16px;
            }
        }

        .steps-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .step-box {
            background: #f8f8f8;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .steps-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .steps-row {
                grid-template-columns: 1fr;
            }
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #000; /* change color if needed */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin: 0 auto 15px auto;
        }

        .nc-form-container {
            background: #1e2a38;
            padding: 35px;
            width: 100%;
            max-width: 600px;
            margin: auto;
            border-radius: 20px;
            color: #fff;
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .field {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .field.full {
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            margin-bottom: 6px;
            opacity: 0.9;
        }

        select {
            height: 64px;
            border-radius: 10px;
        }


        input:focus,
        textarea:focus,
        select:focus {
            background-color: white !important; /* Keep original background */
        }

        input::placeholder {
            color: #6c7a8a;
        }

        .checkbox {
            display: flex;
            align-items: center;
            margin-top: 10px;
            gap: 10px;
            font-size: 14px;
        }

        .submit-btn {
            width: 100%;
            background: #ff9a26;
            color: #000;
            padding: 14px;
            border: none;
            margin-top: 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #ff8a00;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .row {
                flex-direction: column;
            }
        }

        .faq-container {
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .faq-container h2 {
            text-align: center;
            padding: 20px;
            margin: 0;
            background: #f5f5f5;
        }

        .faq-item {
            border-bottom: 1px solid #ddd;
        }

        .faq-question {
            width: 100%;
            background: #f5f5f5;
            border: none;
            padding: 15px;
            text-align: left;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }

        .faq-question:hover {
            background: #e0e0e0;
        }

        .faq-answer {
            display: none;
            padding: 15px;
            background: #fff;
            font-size: 14px;
            color: #555;
        }

        .faq-question::after {
            content: '+';
            font-weight: bold;
            transition: transform 0.3s;
        }

        .faq-question.active::after {
            content: '-';
            transform: rotate(180deg);
        }

        option {
            background-color: #fff;
            color: #fff;
        }

        select {
            background-color: #ffffff !important;
            color: #000 !important;
        }

        select option {
            background-color: #ffffff !important;
            color: #000 !important;
        }

    </style>
    <style>
        /* Container that holds popup — FULL SCREEN but transparent */
        .otp-popup {
            position: fixed;
            inset: 0;
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            background: transparent; /* No blur, no overlay */
        }

        /* Popup Box */
        .otp-card {
            width: 320px;
            background: #fff;
            border-radius: 14px;
            padding: 22px 20px;
            /*box-shadow: 0 10px 40px rgba(0,0,0,0.25);*/
            position: relative;
            animation: fadeIn 0.25s ease;
        }

        /* Close Button */
        .otp-close {
            position: absolute;
            right: 12px;
            top: 8px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .otp-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .otp-desc {
            font-size: 14px;
            color: #666;
            margin-bottom: 18px;
        }

        .otp-input {
            width: 100%;
            padding: 12px;
            text-align: center;
            font-size: 22px;
            border: 1px solid #ccc;
            border-radius: 8px;
            letter-spacing: 12px; /* Optional (bigger gap) */
            font-weight: bold;
            color: #000;
            caret-color: transparent; /* Cursor invisible */
        }


        .otp-button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: #ff7a00;
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .blur-background {
            filter: blur(5px);
            pointer-events: none; /* Cannot click background */
        }

        input, select {
            height: 45px;
        }

        select {
            text-align: center;
        }

        textarea {
            height: 100px !important;
            min-height: 100px !important;
            max-height: 100px !important;
            resize: none;
        }
    </style>

    <main class="main pages" id="pageContent">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Become a NC Partner
                </div>
            </div>
        </div>
        <div class="container">
            <div class="page-content pt-50">
                <!-- Hero Section -->
                <div class="banner-wrapper"
                     style="position: relative; width: 100%; border-radius: 30px; overflow: hidden;">
                    <img src="{{ \App\Helpers\CustomHelper::getImageUrl('banners', $banner->banner_img ?? '') }}"
                         style="width: 100%; border-radius: 30px;">

                    <!-- Become Partner Button -->
                    <a href="#apply_now"
                       class="partner-btn">
                        Apply Now
                    </a>
                </div>


                <div class="partner-section">

                    <!-- LEFT SIDE -->
                    <div class="partner-left">
                        <h2>What is the NC Partner Network?</h2>
                        <p>
                            The NC Partner Network is NutraCore’s program for verified gym trainers, coaches,
                            and fitness professionals who want to earn more while guiding their clients to the
                            right supplements.
                        </p>
                        <p>
                            You get a unique partner code, exclusive partner benefits, and commissions on every
                            valid order—not just the first, but on every repeat order your clients place.
                        </p>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="partner-right">

                        <div class="feature-box">
                            <h4>Commissions & Earnings</h4>
                            <p>Earn up to 8% recurring commission from your clients’ orders.</p>
                        </div>

                        <div class="feature-box">
                            <h4>Exclusive Pricing</h4>
                            <p>Get special prices on your own NutraCore purchases.</p>
                        </div>

                        <div class="feature-box">
                            <h4>Gifts & Freebies</h4>
                            <p>Unlock free t-shirt, shakers, bags and goodies as you grow.</p>
                        </div>

                        <div class="feature-box">
                            <h4>Co-Branding Support</h4>
                            <p>Get posters, creatives and social shoutouts with your branding.</p>
                        </div>

                    </div>

                </div>

                <div class="join-section">

                    <h2>Who can join?</h2>
                    <p class="subtitle">
                        If you directly influence people’s fitness or nutrition choices, this network is designed for
                        you.
                    </p>

                    <div class="join-grid">

                        <div class="join-item">
                            <img
                                src="{{url('public/assets/nc_partner/1.svg')}}"
                                alt="Gym Owner">
                            <h4>Gym Owner</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="{{url('public/assets/nc_partner/2.svg')}}"
                                alt="Trainers / Coaches">
                            <h4>Trainers / Coaches</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="{{url('public/assets/nc_partner/3.svg')}}"
                                alt="Influencers">
                            <h4>Influencers</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="{{url('public/assets/nc_partner/4.svg')}}"
                                alt="Nutritionists">
                            <h4>Nutritionists</h4>
                        </div>

                    </div>
                </div>
                <section class="benefits-section">
                    <h2 class="title">Benefits for you</h2>
                    <p class="subtitle">
                        A complete support system to help you increase income, deliver better results for your clients
                        and grow your brand.
                    </p>

                    <div class="benefits-grid">

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/5.svg')}}" alt="" class="icon">
                            <h3>Commissions & Earnings</h3>
                            <p>Earn upto 8% Commissions on every recurring order</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/6.svg')}}" alt="" class="icon">
                            <h3>Exclusive Pricing</h3>
                            <p>Get access to exclusive rates on your purchase</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/7.svg')}}" alt="" class="icon">
                            <h3>Exclusive Gifts & Freebie</h3>
                            <p>Get free goodies, gifts on becoming a pro partner</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/8.svg')}}" alt="" class="icon">
                            <h3>Earning Dashboard</h3>
                            <p>Clear & transparent dashboard to see your earning real-time</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/9.svg')}}" alt="" class="icon">
                            <h3>Easy Withdrawals</h3>
                            <p>Withdraw your earning or purchase at your convenience</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/assets/nc_partner/10.svg')}}" alt="" class="icon">
                            <h3>Co-Branding Support</h3>
                            <p>Leverage our brand to grow yours</p>
                        </div>

                    </div>
                </section>

                <div class="join-section">

                    <h2>Why NutraCore?
                    </h2>
                    <p class="subtitle">
                        Partner with a brand built on trust, authenticity and customer experience
                    </p>

                    <div class="join-grid" style="gap: 100px">
                        <div class="join-item">
                            <img src="{{url('public/assets/nc_partner/11.svg')}}" alt="Gym Owner"
                                 style=" border-radius: 0%;">
                            <h4>100% Genuine
                                Supplements</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/assets/nc_partner/12.svg')}}" alt="Trainers / Coaches"
                                 style=" border-radius: 0%;">
                            <h4>Exclusive
                                Member Pricing</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/assets/nc_partner/13.svg')}}" alt="Influencers"
                                 style=" border-radius: 0%;">
                            <h4>2-Hour Xpress
                                Delivery</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/assets/nc_partner/14.svg')}}" alt="Nutritionists"
                                 style=" border-radius: 0%;">
                            <h4>Trusted by 10000+
                                Customers</h4>
                        </div>

                    </div>
                </div>

                <section class="benefits-section">
                    <h2 class="title">How it works?</h2>
                    <p class="subtitle">
                        Simple flow designed so you can focus on training while we handle fulfillment and tracking.
                    </p>

                    <div class="steps-row">
                        <div class="step-box">
                            <div class="step-number">1</div>

                            <h4>Apply online</h4>
                            <p>Fill out a quick application with your basic details.</p>
                        </div>

                        <div class="step-box">
                            <div class="step-number">2</div>

                            <h4>Get approved</h4>
                            <p>Our team reviews your profile and approves you within 24 hours.</p>
                        </div>

                        <div class="step-box">
                            <div class="step-number">3</div>

                            <h4>Share your code</h4>
                            <p>Give your unique code to your clients or audience.</p>
                        </div>

                        <div class="step-box">
                            <div class="step-number">4</div>

                            <h4>Earn on every order</h4>
                            <p>Earn recurring commissions every time they place an order.</p>
                        </div>
                    </div>

                </section>

                <div class="join-section">

                    <h2>Apply to join the NC Partner Network

                    </h2>
                    <p class="subtitle">
                        Share a few details about you and your business. Our team will review your application and get
                        back to
                        you within 24 hours.
                    </p>


                    <div class="modal fade" id="otpPopup" tabindex="-1" aria-labelledby="otpPopup" aria-hidden="true">
                        <div class="modal-dialog  modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Verify with OTP</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Mobile Number -->
                                    <div class="mb-3">
                                        <input type="text" maxlength="4" class="otp-input" id="otp_input"
                                               placeholder="____">
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary" id="verifyOtpBtn">Verify OTP</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="nc-form-container" id="apply_now">
                        <form id="applyForm">
                            <div class="row">
                                <div class="field">
                                    <input type="text" name="full_name" placeholder="Full Name *">
                                </div>

                                <div class="field">
                                    <input type="text" name="mobile_number" placeholder="Mobile Number *">
                                </div>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <input type="email" name="email" placeholder="Email *">
                                </div>

                                <div class="field">
                                    <input type="text" name="city" placeholder="City *">
                                </div>
                            </div>

                            <div class="field full">
    <textarea name="full_address"
              placeholder="Complete Address with Pincode *"></textarea>

                            </div>


                            <div class="field full">
                                <select name="role">
                                    <option value="">Select Role *</option>
                                    <option value="Trainer">Trainer</option>
                                    <option value="Gym Owner">Gym Owner</option>
                                    <option value="Coach">Coach</option>
                                    <option value="Influencer">Influencer</option>
                                    <option value="Nutritionist">Nutritionist</option>
                                </select>
                            </div>

                            <div class="field full">
                                <input type="text" name="brand_name" placeholder="Gym / Studio / Brand Name *">
                            </div>

                            <div class="field full">
                                <select name="active_clients">
                                    <option value="">Select Approx. Active Clients *</option>
                                    <option value="0-20">0–20</option>
                                    <option value="20-50">20–50</option>
                                    <option value="50-100">50–100</option>
                                    <option value="100-200">100–200</option>
                                    <option value="200+">200+</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="field">

                                    <input type="text" name="bank_name" placeholder="Bank Name *">
                                </div>

                                <div class="field">

                                    <input type="text" name="ifsc_code" placeholder="IFSC Code *">
                                </div>
                            </div>

                            <div class="field full">

                                <input type="text" name="account_number" placeholder="Account Number *">
                            </div>

                            <div class="field full">
                                <textarea name="promotion_plan"
                                          placeholder="How do you plan to promote NutraCore? *"
                                          style="height:120px;"></textarea>
                            </div>

                            <div class="field full">
                                <input type="text" name="social_links" placeholder="Instagram / Social Links *">
                            </div>

                            <div class="field full">
                                <label></label>
                                <input type="text" name="active_followers" placeholder="Active Followers (optional)">
                            </div>

                            <div class="field full">
                                <label>Preferred Contact Method *</label>
                                <select name="contact_method">
                                    <option value="">Preferred Contact Method *</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Call">Call</option>
                                    <option value="Email">Email</option>
                                </select>
                            </div>

                            <label class="checkbox">
                                <input type="checkbox" name="agree_terms" value="1">
                                I agree to the NC Partner Network Terms & Conditions and consent to be contacted by
                                NutraCore®.
                            </label>

                            <button type="button" onclick="subForm()" class="submit-btn">Submit Application</button>

                            <!-- On submission, set status => Pending Review in backend -->
                        </form>
                    </div>


                </div>
                <div class="faq-container">
                    <h2>Frequently Asked Questions</h2>
                    @foreach($faqs as $faq)
                        <div class="faq-item">
                            <button class="faq-question">{!! $faq->question??"" !!}</button>
                            <div class="faq-answer">
                                <p>{!! $faq->answer??"" !!}</p>
                            </div>
                        </div>
                    @endforeach


                </div>

                <section class="benefits-section">
                    <h2 class="title">Terms & Conditions Explained</h2>
                    <p class="subtitle">
                        {!! $settings->nc_partner_tnc??'' !!}
                    </p>

                </section>

            </div>
        </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                const isActive = button.classList.contains('active');

                document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('active'));
                document.querySelectorAll('.faq-answer').forEach(a => a.style.display = 'none');

                if (!isActive) {
                    button.classList.add('active');
                    answer.style.display = 'block';
                }
            });
        });

        function subForm() {
            let mobile = document.querySelector("input[name=mobile_number]").value;

            if (mobile.length !== 10) {
                alert("Enter a valid mobile number");
                return;
            }

            // Show modal and send OTP
            sendOtp(mobile);
        }

        function sendOtp() {
            let form = document.getElementById("applyForm");
            let formData = new FormData(form);

            // Convert FormData to JSON
            let data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            fetch("{{url('send-otp')}}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("input[name=_token]").value
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(res => {
                    if (!res.status) {
                        // Display validation errors
                        if (res.errors) {
                            // Object.values(res.errors).forEach(err => alert(err[0]));
                            const firstError = Object.values(res.errors)[0][0];
                            alert(firstError);
                        } else {
                            alert(res.message || "Something went wrong");
                        }
                        return;
                    }

                    // Success → show OTP modal
                    $('#otpPopup').modal('show');
                });
        }


        function closeOtpModal() {
            document.getElementById("otpModal").style.display = "none";
        }

        document.getElementById("verifyOtpBtn").addEventListener("click", function () {

            let otp = document.getElementById("otp_input").value;
            let mobile = document.querySelector("input[name=mobile_number]").value;

            fetch("{{url('verify-otp')}}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("input[name=_token]").value
                },
                body: JSON.stringify({mobile: mobile, otp: otp})
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === true) {
                        // OTP correct → submit full form
                        submitPartnerForm();
                    } else {
                        alert("Invalid OTP");
                    }
                });
        });

        function submitPartnerForm() {
            let form = document.getElementById("applyForm");
            let formData = new FormData(form);
            fetch("{{url('submit-partner-form')}}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("input[name=_token]").value
                },
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === true) {
                        alert("Application Submitted! Status = Pending Review");
                        location.reload();
                    } else {
                        alert(res.message || "Something Went Wrong");
                    }
                });
        }


    </script>
@endsection
