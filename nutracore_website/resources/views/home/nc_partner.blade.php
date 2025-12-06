@extends('home.layout')
@section('content')
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

        input, select, textarea {
            background: #0f1a26;
            border: 1px solid #2c3b50;
            padding: 12px;
            border-radius: 8px;
            color: #fff;
            outline: none;
            font-size: 15px;
        }

        textarea {
            resize: none;
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
    </style>
    <main class="main pages">
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
                                src="https://img.freepik.com/free-photo/young-adult-doing-indoor-sport-gym_23-2149205542.jpg?semt=ais_hybrid&w=740&q=80"
                                alt="Gym Owner">
                            <h4>Gym Owner</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="https://img.freepik.com/free-photo/young-adult-doing-indoor-sport-gym_23-2149205542.jpg?semt=ais_hybrid&w=740&q=80"
                                alt="Trainers / Coaches">
                            <h4>Trainers / Coaches</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="https://img.freepik.com/free-photo/young-adult-doing-indoor-sport-gym_23-2149205542.jpg?semt=ais_hybrid&w=740&q=80"
                                alt="Influencers">
                            <h4>Influencers</h4>
                        </div>

                        <div class="join-item">
                            <img
                                src="https://img.freepik.com/free-photo/young-adult-doing-indoor-sport-gym_23-2149205542.jpg?semt=ais_hybrid&w=740&q=80"
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
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
                            <h3>Commissions & Earnings</h3>
                            <p>Earn upto 8% Commissions on every recurring order</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
                            <h3>Exclusive Pricing</h3>
                            <p>Get access to exclusive rates on your purchase</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
                            <h3>Exclusive Gifts & Freebie</h3>
                            <p>Get free goodies, gifts on becoming a pro partner</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
                            <h3>Earning Dashboard</h3>
                            <p>Clear & transparent dashboard to see your earning real-time</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
                            <h3>Easy Withdrawals</h3>
                            <p>Withdraw your earning or purchase at your convenience</p>
                        </div>

                        <div class="benefit-item">
                            <img src="{{url('public/commission.png')}}" alt="" class="icon">
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

                    <div class="join-grid">

                        <div class="join-item">
                            <img src="{{url('public/commission.png')}}" alt="Gym Owner" style=" border-radius: 0%;">
                            <h4>100% Genuine
                                Supplements</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/commission.png')}}" alt="Trainers / Coaches"
                                 style=" border-radius: 0%;">
                            <h4>Exclusive
                                Member Pricing</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/commission.png')}}" alt="Influencers" style=" border-radius: 0%;">
                            <h4>2-Hour Xpress
                                Delivery</h4>
                        </div>

                        <div class="join-item">
                            <img src="{{url('public/commission.png')}}" alt="Nutritionists" style=" border-radius: 0%;">
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


                    <div class="nc-form-container" id="apply_now">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">
                                <div class="field">
                                    <label>Full name *</label>
                                    <input type="text" name="full_name">
                                </div>

                                <div class="field">
                                    <label>Role *</label>
                                    <select name="role">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <label>Mobile number *</label>
                                    <input type="text" name="mobile_number">
                                </div>

                                <div class="field">
                                    <label>WhatsApp number (optional)</label>
                                    <input type="text" name="whatsapp_number">
                                </div>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <label>Email *</label>
                                    <input type="email" name="email">
                                </div>

                                <div class="field">
                                    <label>City *</label>
                                    <input type="text" name="city">
                                </div>
                            </div>

                            <div class="field full">
                                <label>Gym / Studio / Brand name *</label>
                                <input type="text" name="brand_name">
                            </div>

                            <div class="field full">
                                <label>Approx. active clients *</label>
                                <select name="active_clients">
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <div class="field full">
                                <label>How do you plan to promote NutraCore? *</label>
                                <textarea rows="4" name="promotion_plan"></textarea>
                            </div>

                            <div class="field full">
                                <label>Instagram / Social links</label>
                                <input type="text" name="social_links" placeholder="@handle or URL">
                            </div>

                            <div class="field full">
                                <label>Preferred contact method</label>
                                <select name="contact_method">
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Call">Call</option>
                                    <option value="Email">Email</option>
                                </select>
                            </div>

                            <label class="checkbox">
                                <input type="checkbox" name="agree_terms" value="1">
                                I agree to the NC Partner Network terms & conditions and consent to be contacted by
                                NutraCore®.
                            </label>

                            <button type="submit" class="submit-btn">Submit Application</button>

                        </form>
                    </div>


                </div>
                <div class="faq-container">
                    <h2>Frequently Asked Questions</h2>

                    <div class="faq-item">
                        <button class="faq-question">What is your return policy?</button>
                        <div class="faq-answer">
                            <p>We accept returns within 30 days of purchase. The item must be unused and in original packaging.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">Do you ship internationally?</button>
                        <div class="faq-answer">
                            <p>Yes, we ship to most countries. Shipping costs and delivery times vary depending on the location.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">How can I track my order?</button>
                        <div class="faq-answer">
                            <p>After your order is shipped, you will receive an email with tracking details.</p>
                        </div>
                    </div>
                </div>

                <section class="benefits-section">
                    <h2 class="title">Terms & Conditions Explained</h2>
                    <p class="subtitle">
                        A complete support system to help you increase income, deliver better results for your clients
                        and grow your brand.
                    </p>

                </section>

            </div>
        </div>
    </main>
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
    </script>
@endsection
