@extends('home.layout')
@section('content')

    <main class="main">
        <section class="popular-categories section-padding">
            <div class="container wow ">
                <div class="section-title">
                    <div class="title">
                        <h3>Explore</h3>
                    </div>
                </div>

                <div class="container mt-4">
                    <!-- Tabs nav -->
                    <ul class="nav nav-tabs w-100" id="myTab" role="tablist" style="display: flex; gap: 8px;">
                        <li class="nav-item flex-fill text-center" role="presentation">
                            <button class="nav-link active w-100" id="tab1-tab" data-bs-toggle="tab"
                                    data-bs-target="#tab1" type="button" role="tab">
                                Category
                            </button>
                        </li>
                        <li class="nav-item flex-fill text-center" role="presentation">
                            <button class="nav-link w-100" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"
                                    type="button" role="tab">
                                Brands
                            </button>
                        </li>
                        <li class="nav-item flex-fill text-center" role="presentation">
                            <button class="nav-link w-100" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3"
                                    type="button" role="tab">
                                By Goals
                            </button>
                        </li>
                    </ul>


                    <!-- Tabs content -->
                    <div class="tab-content p-3 border border-top-0" id="myTabContent">
                        <div class="tab-pane fade show active text-center" id="tab1" role="tabpanel">
                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-6 col-md-2 mb-3"> <!-- col-6 = 2 per row on mobile -->
                                        <div class="border-1 text-center">
                                            <figure>
                                                <a href="{{ url('collections/' . $category->slug) }}">
                                                    <img src="{{ $category->image}}" alt="" style="height:100px;"/>
                                                </a>
                                            </figure>
                                            <h4 class="mt-2">{{ $category->name ?? '' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="tab-pane fade text-center" id="tab2" role="tabpanel">
                            <div class="row">

                                @foreach($brands as $brand)
                                    <div class="col-6 col-md-2 mb-3"> <!-- col-6 = 2 per row on mobile -->
                                        <div class="border-1 text-center">
                                            <figure>
                                                <a href="{{ url('collections/' . $brand->slug) }}">
                                                    <img src="{{ $brand->brand_img}}" alt="" style="height:100px;" />
                                                </a>
                                            </figure>
                                            <h4>{{ $brand->brand_name ?? '' }}</h4>
                                        </div>
                                    </div>


                                @endforeach

                            </div>
                        </div>
                        <div class="tab-pane fade text-center" id="tab3" role="tabpanel">
                            <div class="row">
                                @foreach($goalcategories as $category)
                                    <div class="col-6 col-md-2 mb-3"> <!-- col-6 = 2 per row on mobile -->
                                        <div class="border-1 text-center">
                                            <figure>
                                                <a href="{{ url('collections/' . $category->slug) }}">
                                                    <img src="{{ $category->image}}"
                                                         alt="" style="height:100px;"/>
                                                </a>
                                            </figure>
                                            <h4>{{ $category->name ?? '' }}</h4>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

@endsection
