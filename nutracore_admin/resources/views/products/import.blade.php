@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $current_url = url()->current();
    ?>

    <div class="content ">
        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Import Products</li>
                </ol>
            </nav>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <form action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Import File</label>
                                    <input type="file" class="form-control" placeholder="Search..." name="file"
                                           value="">
                                </div>
                                <div class="col-md-4" style="margin-top: 27px">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <a href="{{$current_url}}" class="btn btn-danger">Reset</a>
                                    <a href="{{route('products.sample')}}" class="btn btn-danger"><i
                                            class="fa fa-download"></i> Sample</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Products</div>
                            <div class="dropdown ms-auto">
                                <?php if (request()->has('back_url')){
                                    $back_url = request('back_url'); ?>
                                <div class="dropdown ms-auto">
                                    <a href="{{ url($back_url) }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i></a>
                                </div>
                                <?php } ?>

                                @if(!empty($products_data))
                                    <button class="btn btn-primary" onclick="submit_form()">Save</button>
                                    <form action="{{route('products.import_product')}}" method="post"
                                          id="product_save_form">
                                        @csrf
                                        <input type="hidden" name="productArr" value="{{json_encode($products_data)}}">
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                @include('snippets.flash')
                @include('snippets.errors')

                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0 w-100" id="products">
                        <thead>
                        <tr>
                            @if(!empty($headings))
                                @foreach($headings as $heading)
                                    <th class="text-wrap">{{$heading??''}}</th>
                                @endforeach
                            @endif
                        </tr>
                        </thead>


                        <tbody>
                        @if(!empty($products_data))
                            @foreach ($products_data as $product)
                                @php
                                    $varients = $product['varients']??'';
                                @endphp
                                <tr>
                                    <td class="text-wrap">{{ $product['id']??'' }}</td>
                                    <td class="text-wrap">{{ $product['seller_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['product_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['category_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['subcategory_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['brand_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['manufacture_name']??'' }}</td>
                                    <td class="text-wrap">{{ $product['tags']??'' }}</td>
                                    <td class="text-wrap">{!! $product['short_description']??'' !!}</td>
                                    <td class="text-wrap">{!!   $product['long_description']??'' !!}</td>
                                    <td class="text-wrap"><a><img src="{{$product['image']??''}}" height="50px"
                                                                  width="50px"></a></td>
                                    <td class="text-wrap">{{ $product['type']??'' }}</td>
                                    <td class="">
                                        <div class="row">
                                            @foreach($varients as $varient)
                                                <div class="col-md-12">
                                                    <div class="card mt-1 w-100 border-primary">
                                                        <div class="card-body " style="padding: 10px">
                                                            <span>Varient ID : {{$varient['varient_id']??''}}</span><br>
                                                            <span>Unit : {{$varient['unit']??''}}</span><br>
                                                            <span>Unit Value : {{$varient['unit_value']??''}}</span><br>
                                                            <span>MRP : ₹ {{$varient['mrp']??''}}</span><br>
                                                            <span>Selling Price : ₹ {{$varient['selling_price']??''}}</span><br>
                                                            <span>Subscription Price : ₹ {{$varient['subscription_price']??''}}</span><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submit_form() {
            $('#product_save_form').submit();
        }
    </script>
@endsection
