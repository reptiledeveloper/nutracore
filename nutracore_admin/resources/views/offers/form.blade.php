@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $offers_id = $offers->id ?? '';
    $offer_code = $offers->offer_code ?? '';
    $status = $offers->status ?? '';
    $description = $offers->description ?? '';
    $start_date = $offers->start_date ?? '';
    $end_date = $offers->end_date ?? '';
    $no_of_times = $offers->no_of_times ?? '';
    $min_cart_value = $offers->min_cart_value ?? '';
    $offer_type = $offers->offer_type ?? '';
    $offer_value = $offers->offer_value ?? '';
    $max_discount = $offers->max_discount ?? '';
    $allowed_user_times = $offers->allowed_user_times ?? '';
    $category_restrictions = $offers->category_restrictions ?? '';
    $product_restrictions = $offers->product_restrictions ?? '';
    $category_ids = $offers->category_ids ?? '';
    $brand_ids = $offers->brand_ids ?? '';
    $vendor_id = $offers->vendor_id ?? '';
    $product_ids = $offers->product_ids ?? '';
    $mem_type = $offers->mem_type ?? '';
    $user_id = $offers->user_id ?? '';

    $category_ids = explode(",",$category_ids);
    $brand_ids = explode(",",$brand_ids);
    $product_ids = explode(",",$product_ids);


    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $brands = \App\Helpers\CustomHelper::getBrands();
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
                    <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                </ol>
            </nav>
        </div>
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $offers_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Promo Code</label>
                                    <input type="text" class="form-control" placeholder="Promo Code" name="offer_code"
                                           value="{{old('offer_code',$offer_code)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Description</label>
                                    <input type="text" class="form-control" placeholder="Description" name="description"
                                           value="{{old('description',$description)}}">
                                </div>
                                 <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">Type</label>
                                  <select class="form-control" name="mem_type">
                                        <option value="" selected>Select</option>
                                        <option value="subscribe" {{ $mem_type == 'subscribe' ?"selected":"" }}>Member</option>
                                        <option value="not_subscribe" {{ $mem_type == 'not_subscribe' ?"selected":"" }}>NonMember</option>
                                        <option value="both" {{ $mem_type == 'both' ?"selected":"" }}>Both</option>
                                  </select>

                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" placeholder="Promo Code" name="start_date"
                                           value="{{old('start_date',$start_date)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">End Date</label>
                                    <input type="date" class="form-control" placeholder="Promo Code" name="end_date"
                                           value="{{old('end_date',$end_date)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">No of Times</label>
                                    <input type="number" class="form-control" placeholder="No of Times"
                                           name="no_of_times"
                                           value="{{old('no_of_times',$no_of_times)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Minimum Order Amount </label>
                                    <input type="number" class="form-control" placeholder="Minimum Order Amount "
                                           name="min_cart_value"
                                           value="{{old('min_cart_value',$min_cart_value)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Offer Type</label>
                                    <select class="form-control" name="offer_type">
                                        <option value="" selected>Select Offer Type</option>
                                        <option value="PERCENTAGE" {{$offer_type == 'PERCENTAGE'?"selected":""}}>
                                            PERCENTAGE
                                        </option>
                                        <option value="FIXED" {{$offer_type == 'FIXED'?"selected":""}}>FIXED</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Offer Value </label>
                                    <input type="number" class="form-control" placeholder="Offer Value"
                                           name="offer_value"
                                           value="{{old('offer_value',$offer_value)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Max Discount Amount</label>
                                    <input type="number" class="form-control" placeholder="Max Discount Amount"
                                           name="max_discount"
                                           value="{{old('max_discount',$max_discount)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Max Time Allowed Per User</label>
                                    <input type="number" class="form-control" placeholder="Max Discount Amount"
                                           name="allowed_user_times"
                                           value="{{old('allowed_user_times',$allowed_user_times)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Max Discount Amount"
                                           name="image" value="">
                                </div>



                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName" class="form-label">Status<span
                                                class="text-danger">*</span></label>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="1"
                                               <?php echo $status == '1' ? 'checked' : ''; ?> checked>
                                        <label class="form-check-label"
                                               for="customRadioBox1">Active</label>
                                    </div>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="0" <?php echo strlen($status) > 0 && $status == '0' ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                               for="customRadioBox1">InActive</label>
                                    </div>
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Category Restrictions</label>
                                    <select name="category_restrictions" class="form-control">
                                        <option value="0">None</option>
                                        <option value="1">Include</option>
                                        <option value="2">Exclude</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Category</label>
                                    <select name="category_ids[]" class="form-control select2" multiple>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id??''}}" {{in_array($category->id,$category_ids)?"selected":""}}>{{$category->name??''}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Brand Restrictions</label>
                                    <select name="brand_restrictions" class="form-control">
                                        <option value="0">None</option>
                                        <option value="1">Include</option>
                                        <option value="2">Exclude</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Brands</label>
                                    <select name="brand_ids[]" class="form-control select2" multiple>
                                        @foreach($brands as $category)
                                            <option value="{{$category->id??''}}" {{in_array($category->id,$brand_ids)?"selected":""}}>{{$category->name??''}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Product Restrictions</label>
                                    <select name="product_restrictions"  class="form-control">
                                        <option value="0">None</option>
                                        <option value="1">Include</option>
                                        <option value="2" >Exclude</option>
                                    </select>
                                </div>
                               @include('layouts.product_search',['selected_data'=>$offers,'multiple'=>'multiple'])


                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Enter User Phone</label>
                                    <input type="text" class="form-control" onkeyup="fetch_user(this.value)"
                                           name="user_id"
                                           value="{{\App\Helpers\CustomHelper::getUserDetails($user_id)->phone??''}}">
                                    <span id="response">{{\App\Helpers\CustomHelper::getUserDetails($user_id)->name??''}}</span>
                                </div>

                            </div>

                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        function fetch_user(inputValue) {
            if (inputValue.length === 10) {  // Check if length is 10
                $.ajax({
                    url: "{{ route('offers.fetch_user') }}", // Define the route
                    method: "POST",
                    data: {phone: inputValue, _token: "{{ csrf_token() }}"},
                    success: function (response) {
                        $("#response").html(response); // Display the response
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                $("#response").html("");
            }
        }
    </script>
@endsection
