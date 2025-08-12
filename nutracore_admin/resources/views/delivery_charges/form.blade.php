@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $delivery_charges_id = $delivery_charges->id ?? '';
    $vendor_id = $delivery_charges->vendor_id ?? '';
    $order_amount = $delivery_charges->order_amount ?? '';
    $order_amount2 = $delivery_charges->order_amount2 ?? '';
    $sign = $delivery_charges->sign ?? '';
    $sign2 = $delivery_charges->sign2 ?? '';
    $delivery_charge = $delivery_charges->delivery_charge ?? '';
    $type = $delivery_charges->type ?? '';
    $radius = $delivery_charges->radius ?? '';

    $status = $delivery_charges->status ?? 1;

    $vendors = \App\Helpers\CustomHelper::getVendors();
    $signes = config('custom.signs');

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
                            <input type="hidden" id="id" value="{{ $delivery_charges_id }}">

                            <div class="row">
                                <div class="form-group col-md-3 mt-3">
                                    <label for="inputEmail4" class="form-label">Type</label>

                                <select class="form-control" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="express" {{$type == "express" ?"selected":""}}>Express</option>
                                    <option value="normal" {{$type == "normal" ?"selected":""}}>Normal</option>
                                </select>
                                    @include('snippets.errors_first', ['param' => 'type'])
                                </div>
                                <div class="form-group col-md-3 mt-3">
                                    <label for="inputEmail4" class="form-label">From</label>
                                    <input type="number" name="order_amount" class="form-control"
                                           value="{{old('order_amount',$order_amount)}}">
                                    @include('snippets.errors_first', ['param' => 'order_amount'])
                                </div>

                                <div class="form-group col-md-3 mt-3">
                                    <label for="inputEmail4" class="form-label">To</label>
                                    <input type="number" name="order_amount2" class="form-control"
                                           value="{{old('order_amount2',$order_amount2)}}">
                                    @include('snippets.errors_first', ['param' => 'order_amount2'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Delivery Charge</label>
                                    <input type="number" name="delivery_charge" class="form-control"
                                           value="{{old('delivery_charge',$delivery_charge)}}">
                                    @include('snippets.errors_first', ['param' => 'delivery_charge'])
                                </div>

                                 <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Radius (In KM)</label>
                                    <input type="text" class="form-control" name="radius"
                                           value="{{ old('radius', $radius) }}">
                                    @include('snippets.errors_first', ['param' => 'radius'])
                                </div>

                                <div class="col-md-6 mt-3">
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
