@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;


    ?>

    <main class="main">
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Brands</h3>
                    </div>
                </div>
                <div class="row">

                    @foreach($brands as $brand)
                        <div class="col-md-2">
                            <div class="border-1 text-center">
                                <figure>
                                    <a href="{{ url('collections/' . $brand->slug) }}">
                                        <img src="{{ CustomHelper::getImageUrl('brands', $brand->brand_img) ?? '' }}" alt="" style="height:100px;" />
                                    </a>
                                </figure>
                                <h4>{{ $brand->brand_name ?? '' }}</h4>
                            </div>
                        </div>


                    @endforeach

                </div>
            </div>
        </section>
    </main>
@endsection
