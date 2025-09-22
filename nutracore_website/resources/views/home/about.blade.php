@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $download_banner = [];
    $banners = \App\Models\Banner::where('status', 1)->where('is_delete', 0)->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
    if (!empty($banners)) {
        foreach ($banners as $banner) {
            $banner->banner_img = CustomHelper::getImageUrl('banners', $banner->banner_img);
            if ($banner->type == 'download_banner') {
                $download_banner = $banner;
            }
        }
    }
    ?>


    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> About us
                </div>
            </div>
        </div>
        <div class="page-content pt-50">
            <div class="container">
                <div class="row">
                    <iframe width="560" height="500"
                            src="https://www.youtube.com/embed/{{CustomHelper::getSettingKey('about_us_video')}}"
                            title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
                <div class="row text-center mt-30">
                    <h3 style="font-size: 30px;font-weight: 600;color: #00a8a8">Fuelling Your Fitness with Trust</h3>
                    <p>At <strong>NutraCore®,</strong> we’re more than just a supplement store — we’re your trusted
                        partner in
                        achieving
                        a stronger, healthier you. Born from a mission to bring authenticity back into the fitness
                        supplement industry, NutraCore® was founded with a simple belief: <strong> everyone deserves
                            access to
                            safe, genuine, and effective supplements backed by expert guidance and care.</strong></p>

                    <p>Whether you're a gym-goer, athlete, or simply health-conscious, NutraCore® is here to empower
                        your wellness journey — with trust, transparency, and top-quality products.</p>

                    <h3 style="font-size: 30px;font-weight: 600;color: #00a8a8;margin-top: 10px">🏋️♂️ Our Mission</h3>
                    <h3 style="font-size: 30px;font-weight: 600;color: #00a8a8;margin-top: 10px">“Empowering Your Health
                        Journey.”</h3>
                    <p>We exist to make fitness and nutrition accessible, safe, and effective for all. From sourcing
                        100% authentic supplements to offering personalized consultation, we’re committed to helping you
                        transform your goals into reality — with strength you can trust.</p>

                    <div class="mt-3">
                        <img src="{{url('public/assets/aboutus.png')}}">
                    </div>


                    <h3 style="font-size: 30px;font-weight: 600;color: #00a8a8;margin-top: 10px"> 💼 What We Offer</h3>
                    <p>
                        At NutraCore®, we bring you the best of both worlds — an in-store experience backed by digital
                        convenience through our website and app.
                    </p>
                    <p>“Authentic Supplements” “NutraCore App” “Supplement Consultation” “Loyalty Program – NutraPass”
                        “Genuine Product Guarantee” “Express Delivery”</p>


                    <h3 style="font-size: 30px;font-weight: 600;color: #00a8a8;margin-top: 10px"> 🤝 Why Trust
                        NutraCore® </h3>
                    <p>We don’t just sell — we care.</p>
                    <p>Our customer-first approach, expert-backed support, and strict sourcing practices set us
                        apart.</p>
                    <p>What makes us different?</p>
                    <p>• ✅ 100% Authentic Products – No middlemen. No compromises.</p>
                    <p>• 🌟 Trusted by Thousands – 4.9⭐ average rating from over 10,000 happy customers.</p>
                    <p>• 💳 Unmatchable Membership Program – NutraPass offers exclusive pricing, cashback, and VIP perks
                        you won’t find anywhere else.</p>
                    <p>• 📜 Certified & Verified – Direct partnerships with top global supplement brands.</p>
                    <p>• 🧑🔬 Expert Staff – Our team includes certified trainers and nutritionists.</p>
                    <p>• 📍 Physical Stores + Online Access – shop from anywhere.</p>
                </div>


                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="testimonial-box p-3 border rounded shadow-sm">
                            <p class="mb-2" style="font-style: italic;">
                                "I’ve been buying supplements for years, but NutraCore made me believe in authenticity
                                again. Their membership scheme makes the purchasing much more affordable. It’s my go-to
                                for real results."
                            </p>
                            <p class="mb-0">
                                — <span style="color:#d9534f; font-weight:bold;">Kushal R</span>,
                                <strong>Fitness Enthusiast & Verified Customer</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="testimonial-box p-3 border rounded shadow-sm">
                            <p class="mb-2" style="font-style: italic;">
                                "From the moment I walked into their store, I knew this was different. The staff didn’t
                                push products — they educated me. I shop from home and pick up from store or get it
                                delivered fast.
                            </p>
                            <p class="mb-0">
                                — <span style="color:#d9534f; font-weight:bold;" >Divya M</span>,
                                <strong>CrossFit Enthusiast & Verified Buyer</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="testimonial-box p-3 border rounded shadow-sm">
                            <p class="mb-2" style="font-style: italic;">
                                "Finding NutraCore was a game-changer for me. As someone who's always been skeptical
                                about the authenticity, I finally feel confident in what I’m consuming. Their team
                                actually guided based on goals — not just sells.
                            </p>
                            <p class="mb-0">
                                — <span style="color:#d9534f; font-weight:bold;">Arvind R</span>,
                                <strong>IT Professional & Fitness Coach</strong>
                            </p>
                        </div>
                    </div>


                </div>


                <section class="section-padding pb-5">
                    <div class="container">
                        <div class="section-title wow animate__animated animate__fadeIn" data-wow-delay="0">
                            <img src="{{ $download_banner->banner_img ?? '' }}" alt=""/>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

@endsection
