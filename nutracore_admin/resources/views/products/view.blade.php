@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
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
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>

        <form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-md-flex gap-4 align-items-center">
                                <h4 class="d-none d-md-flex">{{$products->name??''}}</h4>
                                <div class="dropdown ms-auto">
                                    <?php if (request()->has('back_url')){
                                        $back_url = request('back_url'); ?>
                                    <div class="dropdown ms-auto">
                                        <a href="{{ url($back_url) }}" class="btn btn-primary"><i
                                                class="fa fa-arrow-left"></i></a>
                                    </div>
                                    <?php } ?>
                                    <button type="submit" title="Bulk Update"
                                            onclick="return confirm('Are You Want To Update?')" class="btn btn-primary">
                                        <i class="fa fa-upload" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>


                    @foreach($vendors as $vendor)
                        @php
                            $varients = \App\Helpers\CustomHelper::getProductVarients($products->id);
                        @endphp
                        <div class="card mt-3">
                            <div class="card-body">
                                <h3>{{$vendor->name??''}}</h3>
                                <div class="table-responsive">
                                    <table class="table table-custom table-lg mb-0" id="">
                                        <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Flavour</th>
                                            <th>MRP</th>
                                            <th>Selling Price</th>
                                            <th>Is Subscribed</th>
                                            <th>Subscription Price</th>
                                            <th>Is Stock</th>
                                            <th>Avail Stock</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($varients as $varient)
                                            @php
                                                $mrp = $varient->mrp??'';
                                                $selling_price = $varient->selling_price??'';
                                                $subscription_price = $varient->subscription_price??'';
                                                $status = 0;
                                                $is_subscribed_product = 0;
                                                $is_stock = 0;
                                                $stock_avail = '';
                                                $checkVendorPrice = \App\Helpers\CustomHelper::checkVendorPrice($vendor->id,$products->id,$varient->id);
                                                if(!empty($checkVendorPrice)){
                                                    $mrp = $checkVendorPrice->mrp??'';
                                                    $selling_price = $checkVendorPrice->selling_price??'';
                                                    $subscription_price = $checkVendorPrice->subscription_price??'';
                                                    $status = $checkVendorPrice->status??0;
                                                    $is_subscribed_product = $checkVendorPrice->is_subscribed_product??0;
                                                    $is_stock = $checkVendorPrice->is_stock??0;
                                                    $stock_avail = $checkVendorPrice->stock_avail??'';
                                                }
                                            @endphp
                                            <input type="hidden" name="vendor_id[]" value="{{$vendor->id}}">
                                            <input type="hidden" name="product_id[]" value="{{$products->id}}">
                                            <input type="hidden" name="unit[]" value="{{$varient->unit??''}}">
                                            <input type="hidden" name="unit_value[]"
                                                   value="{{$varient->unit_value??''}}">
                                            <input type="hidden" name="varient_id[]"
                                                   value="{{$varient->id??''}}">

                                            <tr>
                                                <td>{{$varient->unit??''}}</td>
                                                <td>{{$varient->unit_value??''}}</td>
                                                <td><input type="number" class="form-control" name="mrp[]"
                                                           value="{{$mrp??''}}"></td>
                                                <td><input type="number" class="form-control" name="selling_price[]"
                                                           value="{{$selling_price??''}}"></td>
                                                <td>
                                                    <select class="form-control" name="is_subscribed_product[]">
                                                        <option value="" selected>Select Status</option>
                                                        <option value="1" {{$is_subscribed_product==1?"selected":""}}>Yes
                                                        </option>
                                                        <option value="0" {{$is_subscribed_product==0?"selected":""}}>No
                                                        </option>
                                                    </select>

                                                </td>
                                                <td><input type="number" class="form-control"
                                                           name="subscription_price[]"
                                                           value="{{$subscription_price??''}}"></td>
                                                <td>
                                                    <select class="form-control" name="is_stock[]">
                                                        <option value="1" {{$is_stock == 1?"selected":""}}>In Stock</option>
                                                        <option value="0" {{$is_stock == 0?"selected":""}}>Out Of Stock</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                           name="stock_avail[]"
                                                           value="{{$stock_avail??''}}">
                                                </td>
                                                <td>
                                                    <select class="form-control" name="status[]">
                                                        <option value="" selected>Select Status</option>
                                                        <option value="1" {{$status==1?"selected":""}}>Active
                                                        </option>
                                                        <option value="0" {{$status==0?"selected":""}}>InActive
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-success" type="submit"><i
                                                            class="fa fa-check"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </form>
    </div>

@endsection
