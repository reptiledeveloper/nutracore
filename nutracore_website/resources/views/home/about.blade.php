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

        <div class="page-content">
            <div class="container">

                <!-- ====================== VIDEO ====================== -->
                <div class="row justify-content-center mb-40">
                    <div class="col-md-10">
                        <div class="responsive-video">
                            <iframe
                                src="https://www.youtube.com/embed/{{ CustomHelper::getSettingKey('about_us_video') }}"
                                allowfullscreen
                                frameborder="0">
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- ====================== ABOUT TEXT ====================== -->
                <div class="row text-center about-text">
                    <h3 class="section-title-main">Fuelling Your Fitness with Trust</h3>

                    <p>At <strong>NutraCore®</strong>, we’re more than just a supplement store — we’re your trusted
                        partner in achieving a stronger, healthier you. Born from a mission to bring authenticity back into
                        the fitness supplement industry, NutraCore® was founded with a simple belief:
                        <strong>everyone deserves access to safe, genuine, and effective supplements backed by expert guidance and care.</strong>
                    </p>

                    <p>Whether you're a gym-goer, athlete, or simply health-conscious, NutraCore® is here to empower
                        your wellness journey — with trust, transparency, and top-quality products.</p>

                    <h3 class="section-title-main">🏋️‍♂️ Our Mission</h3>
                    <h3 class="section-title-main">“Empowering Your Health Journey.”</h3>

                    <p>We exist to make fitness and nutrition accessible, safe, and effective for all. From sourcing
                        100% authentic supplements to offering personalized consultation, we’re committed to helping you
                        transform your goals into reality — with strength you can trust.</p>

                    <!-- Image -->
                    <div class="about-image">
                        <img src="{{ url('public/assets/aboutus.png') }}">
                    </div>

                    <h3 class="section-title-main">💼 What We Offer</h3>

                    <p>At NutraCore®, we bring you the best of both worlds — an in-store experience backed by digital
                        convenience through our website and app.</p>

                    <p>
                        “Authentic Supplements” • “NutraCore App” • “Supplement Consultation”
                        • “Loyalty Program – NutraPass” • “Genuine Product Guarantee” • “Express Delivery”
                    </p>

                    <h3 class="section-title-main">🤝 Why Trust NutraCore®</h3>

                    <p>We don’t just sell — we care.</p>
                    <p>Our customer-first approach, expert-backed support, and strict sourcing practices set us apart.</p>

                    <p>• ✅ 100% Authentic Products – No middlemen. No compromises.</p>
                    <p>• 🌟 Trusted by Thousands – 4.9⭐ rating from 10,000+ customers.</p>
                    <p>• 💳 NutraPass Membership – Exclusive pricing, cashback & VIP perks.</p>
                    <p>• 📜 Certified & Verified – Trusted global brand partnerships.</p>
                    <p>• 👨‍🔬 Expert Support – Certified trainers & nutritionists.</p>
                    <p>• 📍 Physical Stores + Online Access.</p>

                </div>

                <!-- ====================== TESTIMONIALS ====================== -->
                <div class="row mt-40">

                    <div class="col-md-4 col-sm-12 mb-3">
                        <div class="testimonial-box">
                            <p>
                                "NutraCore made me believe in authenticity again. Their membership scheme makes the
                                purchasing much more affordable. My go-to for real results."
                            </p>
                            <p>
                                — <span style="color:#d9534f;font-weight:bold;">Kushal R</span>,
                                <strong>Fitness Enthusiast</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-3">
                        <div class="testimonial-box">
                            <p>
                                "The staff educated me instead of pushing products. Now I shop online and pick up
                                in-store or get fast delivery."
                            </p>
                            <p>
                                — <span style="color:#d9534f;font-weight:bold;">Divya M</span>,
                                <strong>CrossFit Athlete</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-3">
                        <div class="testimonial-box">
                            <p>
                                "I was always skeptical about supplement authenticity. NutraCore finally gave me
                                confidence with genuine products and expert guidance."
                            </p>
                            <p>
                                — <span style="color:#d9534f;font-weight:bold;">Arvind R</span>,
                                <strong>IT Professional & Coach</strong>
                            </p>
                        </div>
                    </div>

                </div>

                <!-- ====================== DOWNLOAD BANNER ====================== -->
                <section class="section-padding pb-5 mt-40">
                    <div class="container text-center">
                        <img src="{{ $download_banner->banner_img ?? '' }}" alt="" class="img-fluid rounded">
                    </div>
                </section>

            </div>
        </div>
    </main>


@endsection
