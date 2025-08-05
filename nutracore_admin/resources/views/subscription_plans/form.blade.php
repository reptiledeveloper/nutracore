@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $subscription_plans_id = $subscription_plans->id ?? '';
    $name = $subscription_plans->name ?? '';
    $mrp = $subscription_plans->mrp ?? '';
    $price = $subscription_plans->price ?? '';
    $duration = $subscription_plans->duration ?? '';
    $discount_upto = $subscription_plans->discount_upto ?? '';
    $discount_type = $subscription_plans->discount_type ?? '';
    $min_cart_val = $subscription_plans->min_cart_val ?? '';
    $terms = $subscription_plans->terms ?? '';
    $max_discount = $subscription_plans->max_discount ?? '';
    $vendor_id = $subscription_plans->vendor_id ?? '';



    $status = $subscription_plans->status ?? '0';

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
                            <input type="hidden" id="id" value="{{ $subscription_plans_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name"
                                           value="{{ old('name', $name) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">MRP</label>
                                    <input type="text" class="form-control" name="mrp"
                                           value="{{ old('mrp', $mrp) }}">
                                    @include('snippets.errors_first', ['param' => 'mrp'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Price</label>
                                    <input type="text" class="form-control" name="price"
                                           value="{{ old('price', $price) }}">
                                    @include('snippets.errors_first', ['param' => 'price'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Duration (in Months)</label>
                                    <input type="number" class="form-control" name="duration"
                                           value="{{ old('duration', $duration) }}">
                                    @include('snippets.errors_first', ['param' => 'duration'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Discount Upto</label>
                                    <input type="number" class="form-control" name="discount_upto"
                                           value="{{ old('discount_upto', $discount_upto) }}">
                                    @include('snippets.errors_first', ['param' => 'discount_upto'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Discount Type</label>
                                    <select class="form-control" name="discount_type">
                                        <option value="" selected>Select Type</option>
                                        <option value="FIXED" {{$discount_type == 'FIXED' ?"selected":""}}>FIXED
                                        </option>
                                        <option value="PERCENTAGE" {{$discount_type == 'PERCENTAGE' ?"selected":""}}>
                                            PERCENTAGE
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'discount_type'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Min Cart Value</label>
                                    <input type="text" class="form-control" name="min_cart_val"
                                           value="{{ old('min_cart_val', $min_cart_val) }}">
                                    @include('snippets.errors_first', ['param' => 'min_cart_val'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Terms</label>
                                    <input type="text" class="form-control" name="terms"
                                           value="{{ old('terms', $terms) }}">
                                    @include('snippets.errors_first', ['param' => 'terms'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Max Discount</label>
                                    <input type="text" class="form-control" name="max_discount"
                                           value="{{ old('max_discount', $max_discount) }}">
                                    @include('snippets.errors_first', ['param' => 'max_discount'])
                                </div>

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

@endsection
