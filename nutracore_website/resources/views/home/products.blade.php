@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $sort_by = $_GET['sort_by'] ?? '';
    $category_id = $_GET['category_id'] ?? '';
    $brand_id = $_GET['brand_id'] ?? '';

    $categories = \App\Models\Category::where('status',1)->where('is_delete',0)->where('parent_id',0)->get();
    $brands = \App\Models\Brand::where('status',1)->where('is_delete',0)->get();


    if ($sort_by == 'low_to_high') {
        usort($products, function ($a, $b) {
            // Get the selling price of the first variant for product A and B
            $priceA = $a->varients[0]['selling_price'] ?? PHP_INT_MAX;
            $priceB = $b->varients[0]['selling_price'] ?? PHP_INT_MAX;

            // Use spaceship operator (<=>) for comparison (PHP 7+)
            // a <=> b returns: -1 if a < b, 0 if a == b, 1 if a > b
            // This provides ASCENDING sort order.
            return $priceA <=> $priceB;
        });
    }
    if ($sort_by == 'high_to_low') {
        // Assuming $products is the array of App\Models\Product Objects
        usort($products, function ($a, $b) {
            // Get the selling price of the first variant for product A and B
            $priceA = $a->varients[0]['selling_price'] ?? 0;
            $priceB = $b->varients[0]['selling_price'] ?? 0;

            // Use spaceship operator (<=>) and reverse the order (b <=> a)
            // This provides DESCENDING sort order.
            return $priceB <=> $priceA;
        });
    }

    ?>

    <main class="main">
        @include('home.countdown')
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Products
                </div>
            </div>
        </div>

        <div class="container mb-30 mt-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-product-fillter">
                        <div class="totall-product">
                            <p>We found <strong class="text-brand">{{count($products)}}</strong> items for you!</p>
                        </div>

                       @include('home.product_filter')


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

        <div class="container mb-30 mt-5 p-20 border" style="background-color: #FFFCDF;border-radius: 10px;">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-product-fillter">
                        <h3>Deals of the Day</h3>
                    </div>
                    <div class="row product-grid">
                        @foreach ($products as $product)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 custom-col">
                                @include('home.deal_product', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        (function(){

            function parseDateSafe(dateStr) {
                if (!dateStr) return null;
                if (dateStr.includes('T') || dateStr.endsWith('Z')) {
                    return new Date(dateStr);
                }
                let iso = dateStr.replace(' ', 'T');
                return new Date(iso);
            }

            const boxes = document.querySelectorAll('.deal-timer-box');
            const timers = [];

            boxes.forEach(box => {
                timers.push({
                    box,
                    pid: box.dataset.productId,
                    end: parseDateSafe(box.dataset.endDate)
                });
            });

            function updateTimers() {
                const now = Date.now();

                timers.forEach(t => {
                    let distance = t.end.getTime() - now;

                    const hoursEl = document.getElementById('hours' + t.pid);
                    const minsEl  = document.getElementById('mins' + t.pid);
                    const secsEl  = document.getElementById('secs' + t.pid);

                    if (distance <= 0) {
                        hoursEl.textContent = "00";
                        minsEl.textContent = "00";
                        secsEl.textContent = "00";
                        return;
                    }

                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const mins  = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const secs  = Math.floor((distance % (1000 * 60)) / 1000);

                    hoursEl.textContent = String(hours).padStart(2,'0');
                    minsEl.textContent  = String(mins).padStart(2,'0');
                    secsEl.textContent  = String(secs).padStart(2,'0');
                });
            }

            updateTimers();
            setInterval(updateTimers, 1000);

        })();

    </script>

@endsection
