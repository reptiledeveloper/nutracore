@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $suppliments_id = isset($suppliments->id) ? $suppliments->id : '';
    $activity = isset($suppliments->activity) ? $suppliments->activity : '';
    $category_id = isset($suppliments->category_id) ? $suppliments->category_id : '';
    $supliment_1 = isset($suppliments->supliment_1) ? $suppliments->supliment_1 : '';
    $supliment_2 = isset($suppliments->supliment_2) ? $suppliments->supliment_2 : '';
    $supliment_3 = isset($suppliments->supliment_3) ? $suppliments->supliment_3 : '';
    $supliment_4 = isset($suppliments->supliment_4) ? $suppliments->supliment_4 : '';
    $supliment_5 = isset($suppliments->supliment_5) ? $suppliments->supliment_5 : '';
    $status = isset($suppliments->status) ? $suppliments->status : '';





    $categories = \App\Helpers\CustomHelper::getCategories();
    $goal_catgories = \App\Helpers\CustomHelper::getGoalCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $brands = \App\Helpers\CustomHelper::getBrands();
    $products = \App\Helpers\CustomHelper::getProducts();
    $activity_array = [
        "Walking",
        "Running",
        "Sports",
        "Gym (Beginner)",
        "Gym (Intermediate/Advance)",
        "Yoga",
        "No Activity"
    ];
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
                            <input type="hidden" id="id" value="{{ $suppliments_id }}">

                            <div class="row">
                                <div class="form-group col-md-4 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Goal Category</label>
                                    <select class="form-control" name="category_id">
                                        <option value="" selected>Select</option>
                                        @foreach($goal_catgories as $category)
                                            <option
                                                value="{{$category->id??''}}" {{$category->id == $category_id ? "selected":""}}>{{$category->name??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'category_id'])
                                </div>

                                <div class="form-group col-md-4 mt-3">
                                    <label for="inputEmail4" class="form-label">Activity</label>
                                    <select class="form-control" name="activity">
                                        <option value="" selected>Select</option>
                                        @foreach($activity_array as $key =>$value)
                                            <option
                                                value="{{$value??''}}" {{$value == $activity ? "selected":""}}>{{$value??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>


                                <div class="form-group col-md-4 mt-3">
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
                            <div class="row mt-3">
                                @for($i=1; $i<=5; $i++)
                                    @php
                                        $categoryField = "supliment_".$i;
                                        $productsField = "supliment_".$i."_products";

                                        // Get selected category
                                        $selectedCategory = old($categoryField, $suppliments->$categoryField ?? '');

                                        // Get selected products (comma-separated string → array)
                                        $selectedProductsRaw = old($productsField, $suppliments->$productsField ?? '');
                                        $selectedProducts = is_array($selectedProductsRaw)
                                            ? $selectedProductsRaw
                                            : array_filter(explode(',', $selectedProductsRaw));
                                    @endphp

                                    <div class="form-group col-md-6 mt-3">
                                        <label class="form-label">Choose Category</label>
                                        <select class="form-control" name="{{ $categoryField }}">
                                            <option value="" selected>Select</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $category->id == $selectedCategory ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @include('snippets.errors_first', ['param' => $categoryField])
                                    </div>

                                    <div class="form-group col-md-6 mt-3">
                                        <label class="form-label">Choose Products</label>
                                        <select class="form-control select2" multiple name="{{ $productsField }}[]">
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ in_array($product->id, $selectedProducts) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @include('snippets.errors_first', ['param' => $productsField])
                                    </div>
                                @endfor


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
