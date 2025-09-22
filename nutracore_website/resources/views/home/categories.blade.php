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
                        <h3>Categories</h3>
                    </div>
                </div>
                <div class="row">

                    @foreach($categories as $category)
                        <div class="col-md-2">
                            <div class="border-1 text-center">
                                <figure>
                                    <a href="{{ url('collections/' . $category->slug) }}">
                                        <img src="{{ CustomHelper::getImageUrl('categories', $category->image) ?? '' }}"
                                             alt="" style="height:100px;"/>
                                    </a>
                                </figure>
                                <h4>{{ $category->name ?? '' }}</h4>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>
        </section>
    </main>
@endsection
