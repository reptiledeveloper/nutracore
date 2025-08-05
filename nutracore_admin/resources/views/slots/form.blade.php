@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $slots_id = $slots->id ?? '';
    $opening_time = $slots->opening_time ?? '';
    $vendor_id = $slots->vendor_id ?? '';
    $closing_time = $slots->closing_time ?? '';
    $time_slot = $slots->time_slot ?? '';
    $max_order = $slots->max_order ?? '';
    $status = $slots->status ?? '1';

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
                            <input type="hidden" id="id" value="{{ $slots_id }}">

                            <div class="row">


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
                                    <label for="inputEmail4" class="form-label">Open Time</label>
                                    <input type="time" class="form-control"

                                           name="opening_time" value="{{ old('opening_time', $opening_time) }}">
                                    @include('snippets.errors_first', ['param' => 'opening_time'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Close Time</label>
                                    <input type="time" class="form-control"  name="closing_time" value="{{ old('closing_time', $closing_time) }}">
                                    @include('snippets.errors_first', ['param' => 'closing_time'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Slot Time (In Min)</label>
                                    <input type="number" class="form-control"

                                           name="time_slot" value="{{ old('time_slot', $time_slot) }}">
                                    @include('snippets.errors_first', ['param' => 'time_slot'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Maximum Order</label>
                                    <input type="text" class="form-control"

                                           name="max_order" value="{{ old('max_order', $max_order) }}">
                                    @include('snippets.errors_first', ['param' => 'max_order'])
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
