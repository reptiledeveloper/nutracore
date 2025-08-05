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
                        <div class="col-md-2 ">
                            <figure class="img-hover-scale overflow-hidden">
                                <a href='{{ url('collections/' . $category->slug) }}'><img
                                        src="{{CustomHelper::getImageUrl('categories', $category->image ?? '')}}"
                                        style="height: 150px;width:100%" alt="" /></a>
                            </figure>
                            <h4 class="center">{{ $category->name ?? '' }}</h4>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </main>
@endsection