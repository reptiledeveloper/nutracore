@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();


    $categories_id = isset($categories->id) ? $categories->id : '';
    $name = isset($categories->name) ? $categories->name : '';
    $parent_id = isset($categories->parent_id) ? $categories->parent_id : '';

    $vendor_id = isset($categories->vendor_id) ? $categories->vendor_id : '';
    $phone = isset($categories->phone) ? $categories->phone : '';
    $address = isset($categories->address) ? $categories->address : '';
    $status = isset($categories->status) ? $categories->status : '';
    $image = isset($categories->image) ? $categories->image : '';
    $is_subscribe = isset($categories->is_subscribe) ? $categories->is_subscribe : 0;
    $image = \App\Helpers\CustomHelper::getImageUrl('categories', $image);
    $categories = \App\Helpers\CustomHelper::getCategory();
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
                            <input type="hidden" id="id" value="{{ $categories_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Category</label>
                                    <select class="form-control" name="parent_id">
                                        <option value="" selected disabled>Select Category</option>
                                        <?php if (!empty($categories)){
                                        foreach ($categories as $cat){
                                            ?>
                                        <option value="{{$cat->id}}" <?php if ($cat->id == $parent_id) echo "selected" ?>>{{$cat->name??''}}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'banner_name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name="name"
                                           value="{{old('name',$name)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Name" name="image"
                                           value="">
                                    @include('layouts.show_image',['type'=>'single','image'=>$image])
                                </div>


{{--                                <div class="form-group col-md-6 mt-3 mt-3">--}}
{{--                                    <label for="inputEmail4" class="form-label">Choose Vendor</label>--}}
{{--                                    <select class="form-control" name="vendor_id">--}}
{{--                                        <option value="" selected disabled>Select Vendor</option>--}}
{{--                                        <?php if (!empty($vendors)){--}}
{{--                                        foreach ($vendors as $vendor){--}}
{{--                                            ?>--}}
{{--                                        <option--}}
{{--                                                value="{{$vendor->id}}" <?php if ($vendor->id == $vendor_id) echo "selected" ?>>{{$vendor->name??''}}</option>--}}
{{--                                        <?php }--}}
{{--                                        } ?>--}}
{{--                                    </select>--}}
{{--                                    @include('snippets.errors_first', ['param' => 'banner_name'])--}}
{{--                                </div>--}}

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Is Subscribe</label>
                                    <select class="form-control" name="is_subscribe">
                                        <option value="" selected>Select</option>
                                        <option value="1" {{$is_subscribe==0?"selected":""}}>Yes</option>
                                        <option value="0" {{$is_subscribe==0?"selected":""}}>No</option>
                                    </select>
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
