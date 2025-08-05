@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $banners_id = isset($banners->id) ? $banners->id : '';
    $banner_name = isset($banners->banner_name) ? $banners->banner_name : '';
    $vendor_id = isset($banners->vendor_id) ? $banners->vendor_id : '';
    $phone = isset($banners->phone) ? $banners->phone : '';
    $address = isset($banners->address) ? $banners->address : '';
    $status = isset($banners->status) ? $banners->status : '';
    $image = isset($banners->banner_img) ? $banners->banner_img : '';
    $type = isset($banners->type) ? $banners->type : '';
    $type_id = $banners->type_id ?? '';
    if (!empty($image)) {
        $image = \App\Helpers\CustomHelper::getImageUrl('banners', $image);
    } else {
        $image = url('public/assets/img/products/vender-upload-preview.jpg');
    }

    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
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
                            <input type="hidden" id="id" value="{{ $banners_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Name" name="image"
                                           value="">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Title</label>
                                    <input type="text" class="form-control"

                                           name="banner_name" value="{{ old('banner_name', $banner_name) }}">
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Type</label>
                                    <select class="form-control" name="type" onchange="get_type_val(this.value)">
                                        <option value="" selected>Select</option>
                                        <option value="category" <?php if ($type == 'category') echo "selected" ?>>
                                            Category
                                        </option>
                                        <option value="product" <?php if ($type == 'product') echo "selected" ?>>
                                            Product
                                        </option>
                                        <option value="refer_earn" <?php if ($type == 'refer_earn') echo "selected" ?>>
                                            Refer & Earn
                                        </option>
                                        <option
                                            value="subscription" <?php if ($type == 'subscription') echo "selected" ?>>
                                            Subscription
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>

                                <div class="form-group col-md-6 mt-3" id="category_show" style="display: none">
                                    <label for="inputEmail4" class="form-label">Choose Category</label>
                                    <select class="form-control" name="type_id">
                                        <option value="" selected>Select</option>
                                        @foreach($categories as $category)
                                            <option
                                                value="{{$category->id??''}}" {{$category->id == $type_id ? "selected":""}}>{{$category->name??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>

                                @include('layouts.product_search',['selected_data'=>$banners])


                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Vendor</label>
                                    <select class="form-control" name="vendor_id">
                                        <option value="" selected disabled>Select Vendor</option>
                                        <?php if (!empty($vendors)){
                                        foreach ($vendors as $vendor){
                                            ?>
                                        <option
                                            value="{{$vendor->id}}" <?php if ($vendor->id == $vendor_id) echo "selected" ?>>{{$vendor->name??''}}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
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
        $(document).ready(function () {
            $('#category_show').hide();
            $('#product_show').hide();
            var type = '{{$type}}';
            if (type == 'category') {
                $('#category_show').show();
            }
            if (type == 'product') {
                $('#product_show').show();
            }
        });

        function get_type_val(val) {
            $('#category_show').hide();
            $('#product_show').hide();
            if (val == 'category') {
                $('#category_show').show();
            }
            if (val == 'product') {
                $('#product_show').show();
            }
        }
    </script>

@endsection
