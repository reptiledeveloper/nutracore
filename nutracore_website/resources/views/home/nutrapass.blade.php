@extends('home.layout')
@section('content')
    <?php 
                                                                                                                                                                                                                                                use App\Helpers\CustomHelper;
                                                                                                        ?>

    <style>
        .hero-section {
            background-image: url('{{url('public/assets/images/Frame 1261155282.png')}}');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        /* 
                                                                                            .benefits-card {
                                                                                                border: 1px solid #ddd;
                                                                                                border-radius: 15px;
                                                                                                background-color: #f8f9fa;
                                                                                            }

                                                                                            .faq-section .accordion-button {
                                                                                                font-weight: 600;
                                                                                            }

                                                                                            .review-card {
                                                                                                border: none;
                                                                                                background-color: #f8f9fa;
                                                                                            } */

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

        .faq-question.active+.faq-answer {
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
    </style>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='index.html' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
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

                    <div class="slider-container mt-4 position-relative">
                        <div class="d-flex justify-content-between px-2 mb-1">
                            <span>Silver</span>
                            <span>Gold</span>
                            <span>Platinum</span>
                        </div>
                        <input type="range" class="form-range custom-slider" min="0" max="2" value="1" disabled>
                        <div class="slider-value">₹12,000</div>
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
                    <div class="fw-bold text-warning fs-5">GOLD</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Valid Till</div>
                    <div class="fw-bold fs-5 text-warning">20 Jul 2025</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Saved So Far</div>
                    <div class="fw-bold fs-5 text-warning">₹6,000</div>
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


        <section class="p-5 m-2 " style="background: black; width: 99%;border-radius:10px">
            <div class="row p-3">
                <div class="col-md-5">
                    <div class="bg-dark text-white p-3 rounded-box m-3 border_radius_white">
                        <div class="d-flex align-items-center">
                            <img src="{{ url('public/assets/images/vip_icon.png') }}" class="me-3" style="width: 50px;"
                                alt="VIP">
                            <div>
                                <div class="fw-bold">Membership Validity</div>
                                <div>Active till <span class="fw-bold text-warning">31/Dec/23</span></div>
                            </div>
                        </div>
                        <!-- <img src="{{ url('public/assets/images/athlete.png') }}" class="mt-3 w-100 rounded" alt="Athlete"> -->
                    </div>

                    <!-- Membership Calculator -->
                    <div class="bg-warning p-3 m-3 text-dark rounded-box border_radius_white">
                        <h5 class="fw-bold text-uppercase">Membership Calculator</h5>
                        <p class="mb-1">Spend <strong>₹3,000</strong>/month?</p>
                        <p class="mb-3">You save <strong class="text-primary">₹2,700</strong>/year with NutraPass!</p>
                        <div class="d-flex align-items-center border-top pt-2 bg-white w-100">
                            <img src="{{ url('public/assets/images/calc_icon.png') }}" class="me-2" width="24">
                            <span class="fw-semibold">See how much you save</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 ">
                    <div class="bg-info text-white p-4 rounded-box h-100 border_radius_white">
                        <div class="row mb-3">
                            <div class="col-6 fw-bold"> </div>
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
                            <div class="plan selected" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">6</div>
                                <div class="details ">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <div class="plan" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">12</div>
                                <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <div class="plan" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">12</div>
                                <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <div class="plan" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">12</div>
                                <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <div class="plan" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">12</div>
                                <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <div class="plan" onclick="selectPlan(this)">
                                <h4 class="text-white">Best Value</h4>
                                <div class="months">12</div>
                                <div class="details">months<br>₹ 800/mo<br><strong>SAVE 47%</strong></div>
                                <hr>
                                <div class="price">₹ 8,000</div>
                            </div>
                            <!-- Add more if needed -->
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="#" class="btn btn-warning text-white w-50">JOIN NOW</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="p-5 m-2 " style="background: #FFFFFF00; width: 99%;border-radius:10px">
            <div class="row p-3">
                <h3 class="text-center">FAQ</h3>
                <p class="description">
                    We’re here to help you with anything and everything on NutraCore.<br>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                    industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
                    scrambled.
                </p>


                <div class="faq-item">
                    <div class="faq-question">
                        <span>What is NutraCore?</span>
                        <span class="icon">−</span>
                    </div>
                    <div class="faq-answer open">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                        industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                        and scrambled it to make a type specimen book. It has survived not only five centuries, but also the
                        leap into electronic typesetting, remaining essentially .
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>How to apply for a Subscription?</span>
                        <span class="icon">+</span>
                    </div>
                    <div class="faq-answer">
                        You can apply for a subscription by going to our Plans page and selecting the desired plan and
                        duration.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>How to know status of a Delivery?</span>
                        <span class="icon">+</span>
                    </div>
                    <div class="faq-answer">
                        Track your delivery in your account under "Orders" or use the tracking link sent to your email.
                    </div>
                </div>
            </div>
        </section>





        <section class="p-5 m-2 ">
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
                                    “Since I joined NutraCore Membership, my fitness journey has reached new heights! The
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



        <section class="p-4 m-5" style="background: #00A8A870; border-radius: 30px;">
            <div class="row align-items-stretch" style="min-height: 350px;">
                <!-- Left Side: Text and Subscription -->
                <div class="col-md-6 text-white px-4 d-flex flex-column justify-content-center">
                    <h3 class="fw-bold">Stay home & get your health</h3>
                    <h3 class="fw-bold">needs from our shop</h3>
                    <p class="mb-4">Start You'r Health Shopping with <strong>NutraCore</strong></p>

                    <!-- Email Subscription Form -->
                    <form class="d-flex bg-white rounded-pill overflow-hidden shadow-sm" style="max-width: 400px;">
                        <input type="email" class="form-control border-0 ps-4" placeholder="Your email address" required>
                        <button type="submit" class="btn text-white px-4"
                            style="background-color: #00A8A8;">Subscribe</button>
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
    <script>
        const slider = document.getElementById('slider');
        const bullets = document.getElementById('bullets').children;
        let currentSlide = 0;

        function goToSlide(index) {
            currentSlide = index;
            slider.style.transform = `translateX(-${index * 100}%)`;
            [...bullets].forEach(b => b.classList.remove('active'));
            bullets[index].classList.add('active');
        }

        // Optional: Drag to scroll
        let isDragging = false;


        slider.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.pageX;
            slider.style.cursor = 'grabbing';
        });

        slider.addEventListener('mouseup', () => {
            isDragging = false;
            slider.style.cursor = 'grab';
        });

        slider.addEventListener('mouseleave', () => {
            isDragging = false;
            slider.style.cursor = 'grab';
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const x = e.pageX;
            const walk = x - startX;
            if (walk > 50 && currentSlide > 0) {
                goToSlide(currentSlide - 1);
                isDragging = false;
            } else if (walk < -50 && currentSlide < bullets.length - 1) {
                goToSlide(currentSlide + 1);
                isDragging = false;
            }
        });

        // Optional: Autoplay
        // setInterval(() => {
        //   let next = (currentSlide + 1) % bullets.length;
        //   goToSlide(next);
        // }, 5000);
    </script>
@endsection