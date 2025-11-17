@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;
$sort_by = $_GET['sort_by']??'';
    ?>

    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Products
                </div>
            </div>
        </div>
        <style>
            .custom-col {
                flex: 0 0 20%;
                max-width: 20%;
            }
            @media (max-width: 768px) {
                .custom-col {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
            }


        </style>
        <style>
            .sort-by-product-area {
                position: relative;
                display: inline-block;
            }

            .sort-by-product-wrap {
                display: flex;
                align-items: center;
                cursor: pointer;
            }

            .sort-by-dropdown-wrap span {
                font-weight: 600;
            }

            .sort-by-dropdown {
                position: absolute;
                top: 45px;
                right: 0;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                width: 180px;
                display: none;
                z-index: 999999;
                box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            }

            .sort-by-dropdown.active {
                display: block;
            }

            .sort-by-dropdown ul {
                list-style: none;
                margin: 0;
                padding: 10px 12px;
            }

            .sort-by-dropdown ul li {
                padding: 6px 0;
            }

            .sort-by-dropdown ul li a {
                color: #333;
                text-decoration: none;
                font-size: 14px;
                display: block;
            }

            .sort-by-dropdown ul li a:hover {
                color: #007bff;
            }
        </style>

        <div class="container mb-30 mt-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-product-fillter">
                        <div class="totall-product">
                            <p>We found <strong class="text-brand">{{count($products)}}</strong> items for you!</p>
                        </div>

                       <form action="" method="" id="filterForm">
                           <div class="sort-by-product-area">
                               <div class="sort-by-cover">
                                   <select id="sortSelect" class="form-select" name="sort_by">
                                       <option value="" selected disabled>Sort by</option>
                                       <option value="low_to_high" {{$sort_by == "low_to_high" ? "selected":""}}>Price: Low to High</option>
                                       <option value="high_to_low" {{$sort_by == "high_to_low" ? "selected":""}}>Price: High to Low</option>
                                   </select>

                               </div>
                           </div>
                       </form>


                    </div>
                    <div class="row product-grid">
                        @foreach ($products as $product)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 custom-col">
                                @include('home.single_product', ['product' => $product])
                            </div>
                        @endforeach

                    </div>
                    <!--product grid-->
                    <div class="pagination-area mt-20 mb-20">

                    </div>

                </div>
            </div>
        </div>

    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.getElementById("sortSelect").addEventListener("change", function () {
          $('#filterForm').submit();
        });
    </script>

@endsection
