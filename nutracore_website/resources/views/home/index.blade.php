@extends('home.layout')
@section('content')
    <?php 
                                                                                        use App\Helpers\CustomHelper;
                                                                                        ?>

    <main class="main">
        <section class="home-slider position-relative mb-30">
            <div class="container">
                <div class="home-slide-cover mt-30">
                    <div class="hero-slider-1 style-4 dot-style-1 dot-style-1-position-1">
                        <div class="single-hero-slider single-animation-wrap"
                            style="background-image: url({{url('public/assets')}}/banner.png)">
                        </div>
                        <div class="single-hero-slider single-animation-wrap"
                            style="background-image: url({{url('public/assets')}}/banner.png)">
                        </div>
                    </div>
                    <div class="slider-arrow hero-slider-1-arrow"></div>
                </div>
            </div>
        </section>
        <!--End hero slider-->
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Categories</h3>
                    </div>
                    <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow"
                        id="carausel-10-columns-arrows"></div>
                </div>
                <div class="carausel-10-columns-cover position-relative">
                    <div class="carausel-10-columns" id="carausel-10-columns">
                        @foreach($categories as $category)
                            <div class="card-2 bg-9 ">
                                <figure class="img-hover-scale overflow-hidden">
                                    <a href='{{ url('collections/' . $category->slug) }}'><img
                                            src="{{CustomHelper::getImageUrl('categories', $category->image ?? '')}}"
                                            alt="" /></a>
                                </figure>
                                <h6><a href='{{ url('collections/' . $category->slug) }}'>{{$category->name ?? ''}}</a></h6>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </section>


        <!--End category slider-->

        <section class="featured section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6 mb-md-4 mb-xl-0">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay="0">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-1.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Best prices & offers</h3>
                                <p>Orders $50 or more</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".1s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-2.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Free delivery</h3>
                                <p>24/7 amazing services</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".2s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-3.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Great daily deal</h3>
                                <p>When you sign up</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".3s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-4.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Wide assortment</h3>
                                <p>Mega Discounts</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".4s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-5.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Easy returns</h3>
                                <p>Within 30 days</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6 d-xl-none">
                        <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp"
                            data-wow-delay=".5s">
                            <div class="banner-icon">
                                <img src="{{url('public/assets')}}/imgs/theme/icons/icon-6.svg" alt="" />
                            </div>
                            <div class="banner-text">
                                <h3 class="icon-box-title">Safe delivery</h3>
                                <p>Within 30 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <section class="banners mb-25">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            <img src="{{url('public/assets')}}/images/banner1.png" alt="" />
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                            <img src="{{url('public/assets')}}/images/banner2.png" alt="" />
                        </div>
                    </div>
                    <div class="col-lg-4 d-md-none d-lg-flex">
                        <div class="banner-img mb-sm-0 wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                            <img src="{{url('public/assets')}}/images/banner3.png" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End banners-->
        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div class="section-title style-2 wow animate__animated animate__fadeIn">
                    <h3>Popular Products</h3>
                </div>
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @foreach ($products as $product)


                                <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                                    @include('home.single_product', ['product' => $product])
                                </div>
                            @endforeach
                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>
        <!--Products Tabs-->
        <section class="section-padding pb-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 d-none d-lg-flex wow animate__animated animate__fadeIn">
                        <div class="banner-img style-2"
                            style="background-image: url('{{ $fixed_banner_1->banner_img ?? '' }}'); height:400px;object-fit:cover">
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12 wow animate__animated animate__fadeIn" data-wow-delay=".4s">
                        <div class="tab-content" id="myTabContent-1">
                            <div class="tab-pane fade show active" id="tab-one-1" role="tabpanel"
                                aria-labelledby="tab-one-1">
                                <div class="carausel-4-columns-cover arrow-center position-relative">
                                    <div class="slider-arrow slider-arrow-2 carausel-4-columns-arrow"
                                        id="carausel-4-columns-arrows"></div>
                                    <div class="carausel-4-columns carausel-arrow-center" id="carausel-4-columns">
                                        @if(!empty($fixed_banner_1->products))

                                            @foreach ($fixed_banner_1->products as $product)
                                                @include('home.single_product', ['product' => $product])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--End tab-content-->
                    </div>
                    <!--End Col-lg-9-->
                </div>
            </div>
        </section>


        <section class="popular-categories section-padding">
            <div class="container">
                <div class="section-title">
                    <div class="title">
                        <h3>Shop by Brands</h3>
                    </div>
                    <div class="slider-arrow slider-arrow-2 flex-right carausel-8-columns-arrow"
                        id="carausel-8-columns-arrows"></div>
                </div>
                <div class="carausel-8-columns-cover position-relative">
                    <div class="carausel-8-columns" id="carausel-8-columns">
                        @foreach ($brands as $brand)
                            <div class="">
                                <figure class="">
                                    <a href='{{ url('collections/' . $brand->slug) }}'><img
                                            src="{{CustomHelper::getImageUrl('brands', $brand->brand_img ?? '')}}" alt=""
                                            style="height:100px" /></a>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>


        <section class="section-padding pb-5">
            <div class="container">
                <div class="section-title wow animate__animated animate__fadeIn" data-wow-delay="0">
                    <img src="{{url('public/assets')}}/images/banner5.png" alt="" />
                </div>
            </div>
        </section>
        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div class="section-title style-2 wow animate__animated animate__fadeIn">
                    <h3>Best Deals</h3>
                </div>
                <!--End nav-tabs-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                        <div class="row product-grid-4">
                            @foreach ($products as $product)
                                <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                                    @include('home.single_product', ['product' => $product])
                                </div>
                            @endforeach
                            <!--end product card-->

                        </div>
                        <!--End product-grid-4-->
                    </div>

                </div>
                <!--End tab-content-->
            </div>
        </section>


        <section class="product-tabs section-padding position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="banner-img">
                            <img src="{{url('public/assets/images/refer.png')}}" alt="" />
                            <div class="banner-text left">
                                <span style="color:white !important;font-size:22px">
                                    Refer a friend and earn rewards!
                                </span>
                                <br>
                                <span>Explore the perfect supplements designed just for you!Start your journey to better
                                    health today and find what suits your needs best.</span>

                                <br>
                                <a class='btn btn-xl btn-center' href='shop-grid-right.html'>Join Now</a>
                            </div>
                        </div>

                        <!-- <div class="banner-img">
                                    <img src="{{url('public/assets/images/consultation.png')}}" alt="" />
                                    <div class="banner-text center">
                                        <h4 style="color:white !important">
                                            Instant Expert Guidance
                                        </h4>
                                        <h6 style="color:white !important">
                                            Get immediate access to expert advice and insights tailored just for you!
                                        </h6>
                                        <a class='btn btn-xl btn-center' href='shop-grid-right.html'>Connect now</a>
                                        <p style="color:white !important">
                                            Get your customized nutrition and lifestyle plan
                                        </p>
                                    </div>
                                </div> -->
                        <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">

                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="banner-img">
                            <img src="{{url('public/assets/images/consultation.png')}}" alt="" />
                            <div class="banner-text center">
                                <h4 style="color:white !important">
                                    Instant Expert Guidance
                                </h4>
                                <h6 style="color:white !important">
                                    Get immediate access to expert advice and insights tailored just for you!
                                </h6>
                                <a class='btn btn-xl btn-center' href='shop-grid-right.html'>Connect now</a>
                                <p style="color:white !important">
                                    Get your customized nutrition and lifestyle plan
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>





    </main>
@endsection