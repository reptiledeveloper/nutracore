@extends('home.layout')
@section('content')
    <?php
                                                                                                                                                use App\Helpers\CustomHelper;


                                                                                                                                            ?>



<main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Contact
                </div>
            </div>
        </div>
        <div class="page-content pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-xl-10 col-lg-12 m-auto">
                        <section class="mb-50">
                            <div class="row">
                                <!-- Heading and City Filter -->
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h4 class="mb-0">NutraCore Store in India</h4>
                                </div>

                                <!-- Store Cards -->
                                <div class="row" id="storeCards">
                                    @foreach($sellers_list as $seller)
                                        <div class="col-lg-4 col-md-6 mb-4 store-card"
                                             data-lat="{{ $seller->latitude ?? 0 }}"
                                             data-lng="{{ $seller->longitude ?? 0 }}">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-body">
                                                    <h5 class="mb-1">{{$seller->name??''}}</h5>
                                                    <p class="mb-1"><strong>Address:</strong> {{$seller->address??''}}</p>
                                                    <p class="mb-1"><strong>Contact:</strong> {{$seller->user_phone??''}}</p>
                                                    <p class="mb-1"><strong>Opening Time :</strong> {{$seller->open_time??''}} - {{$seller->close_time??''}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-xl-8">
                                    <div class="contact-from-area padding-20-row-col">
                                        <h5 class="text-brand mb-10">Contact form</h5>
                                        <h2 class="mb-10">Drop Us a Line</h2>
                                        <p class="text-muted mb-30 font-sm">Your email address will not be published. Required fields are marked *</p>
                                        <form class="contact-form-style mt-30" id="contact-form" action="" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="input-style mb-20">
                                                        <input name="name" placeholder="First Name" type="text" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="input-style mb-20">
                                                        <input name="email" placeholder="Your Email" type="email" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="input-style mb-20">
                                                        <input name="telephone" placeholder="Your Phone" type="tel" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="input-style mb-20">
                                                        <input name="subject" placeholder="Subject" type="text" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="textarea-style mb-30">
                                                        <textarea name="message" placeholder="Message"></textarea>
                                                    </div>
                                                    <button class="submit submit-auto-width" type="submit">Send message</button>
                                                </div>
                                            </div>
                                        </form>
                                        <p class="form-messege"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 pl-50 d-lg-block d-none">
                                    <img class="border-radius-15 mt-50" src="assets/imgs/page/contact-2.png" alt="" />
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
