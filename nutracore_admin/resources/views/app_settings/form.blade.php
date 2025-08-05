@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $banners_id = isset($banners->id) ? $banners->id : '';

    $app_settings_id = $app_settings->id ?? '';
    $title = $app_settings->title ?? '';
    $type = $app_settings->type ?? '';
    $product_ids = $app_settings->product_ids ?? '';
    $category_ids = $app_settings->category_ids ?? '';
    $status = $app_settings->status ?? 1;
    $priority = $app_settings->priority ?? 0;

    $category_ids = explode(",", $category_ids);
    //$product_ids = explode(",", $product_ids);


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
                            <input type="hidden" id="id" value="{{ $app_settings_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title"
                                           value="{{ old('title', $title) }}">
                                    @include('snippets.errors_first', ['param' => 'title'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Priority</label>
                                    <input type="number" class="form-control" name="priority"
                                           value="{{ old('priority', $priority) }}">
                                    @include('snippets.errors_first', ['param' => 'priority'])
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
                                        <option value="banners" <?php if ($type == 'banners') echo "selected" ?>>
                                            Banners
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'type'])
                                </div>

                                <div class="form-group col-md-6 mt-3" id="category_show" style="display: none">
                                    <label for="inputEmail4" class="form-label">Choose Category</label>
                                    <select class="form-control select2" name="category_ids[]" multiple>
                                        @foreach($categories as $category)
                                            <option
                                                value="{{$category->id??''}}" {{in_array($category->id,$category_ids) ? "selected":""}}>{{$category->name??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>

                                @include('layouts.product_search',['selected_data'=>$app_settings,'multiple'=>'multiple'])

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
