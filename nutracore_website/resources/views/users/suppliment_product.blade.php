@extends('home.layout')
@section('content')
    <style>
        .product-section {
            margin: 30px;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .product-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .product-header img {
            width: 40px;
            height: 40px;
        }

        .product-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .product-header p {
            margin: 0;
            color: #007b7f;
            font-size: 14px;
        }

        .product-slider {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 10px;
        }

        .product-card {
            flex: 0 0 auto;
            scroll-snap-align: start;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .product-card img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .product-card h4 {
            font-size: 14px;
            margin: 5px 0;
        }

        .product-card p {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .product-card button {
            background-color: #00b4b6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
        }

        .product-slider::-webkit-scrollbar {
            height: 6px;
        }

        .product-slider::-webkit-scrollbar-thumb {
            background-color: #ddd;
            border-radius: 4px;
        }

        .product-card h4 {
            font-size: 14px;
            margin: 10px 0 5px;
            font-weight: bold;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* show only 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

    </style>

    @foreach($suppliments as $cat)

        @php
            $category = $cat['category']?? '';
            $products = $cat['products'] ?? [];
        @endphp

        <div class="product-section">
            <div class="product-header">
                <img src="{{$category->image}}" alt="Protein Icon">
                <div>
                    <h3>{{$category->name??''}}</h3>
                </div>
            </div>

            <div class="product-slider">
                @foreach($products as $product)
                    @php
                        $selectedVarient = $product->varients[0]??[];
                    //    print_r($selectedVarient);die;
                    @endphp
                    <div class="product-card">
                        <img src="{{$product->image}}" alt="Whey Protein">
                        <h4>{{$product->name??''}}</h4>
                        <p>{!! $selectedVarient['unit'] ?? '&nbsp;' !!}</p>
                        <button onclick="window.location.href='{{ url('products/' . ($product->slug ?? '')) }}'">
                            Buy Now
                        </button>
                    </div>
                @endforeach


                <!-- Add more cards as needed -->
            </div>
        </div>
    @endforeach

@endsection
