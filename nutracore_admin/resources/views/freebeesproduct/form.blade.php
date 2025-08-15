@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();


    $freebeesproduct_id = $freebeesproduct->id ?? '';
    $product_name = $freebeesproduct->product_name ?? '';
    $from_amount = $freebeesproduct->from_amount ?? '';
    $to_amount = $freebeesproduct->to_amount ?? '';
    $image = $freebeesproduct->image ?? '';
    $description = $freebeesproduct->description ?? '';
    $amount = $freebeesproduct->amount ?? '';
    $product_id = $freebeesproduct->product_id ?? '';
    $status = $freebeesproduct->status ?? '1';
    $mandate_subscription = $freebeesproduct->mandate_subscription ?? '';
    $image = \App\Helpers\CustomHelper::getImageUrl('freebeesproduct', $image);
    $products = \App\Helpers\CustomHelper::getProducts();
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
                            <input type="hidden" id="id" value="{{ $freebeesproduct_id }}">

                            <div class="row">
                                @if(!empty($freebeesproduct_id))
                                    <div class="form-group col-md-6">
                                        <label for="validationCustom01" class="form-label">Product Name</label>
                                        <select class="form-control select2" name="product_id">
                                            <option>Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{$product->id??''}}" {{$product_id == $product->id?"selected":""}}>{{$product->product_name??''}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group col-md-6">
                                        <label for="validationCustom01" class="form-label">Product Name</label>
                                        <select class="form-control select2" name="product_id[]" multiple>
                                            <option>Select Products</option>
                                            @foreach($products as $product)
                                                <option value="{{$product->id??''}}" >{{$product->product_name??''}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif


                                <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">From Amount</label>
                                    <input type="text" class="form-control" placeholder="" name="from_amount"
                                           value="{{old('from_amount',$from_amount)}}">
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">To Amount</label>
                                    <input type="text" class="form-control" placeholder="" name="to_amount"
                                           value="{{old('to_amount',$to_amount)}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">Description</label>
                                    <input type="text" class="form-control" placeholder="" name="description"
                                           value="{{old('description',$description)}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">Price</label>
                                    <input type="text" class="form-control" placeholder="" name="amount"
                                           value="{{old('amount',$amount)}}">
                                </div>

                                <div class="form-group col-md-6">
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
