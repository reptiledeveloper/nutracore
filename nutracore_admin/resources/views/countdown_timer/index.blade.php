@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $countdown_timer_id = $countdown_timer->id ?? '';
    $product_ids = $countdown_timer->product_ids ?? '';
    $title = $countdown_timer->title ?? '';
    $description = $countdown_timer->description ?? '';
    $start_time = $countdown_timer->start_time ?? '';
    $end_time = $countdown_timer->end_time ?? '';
    $start_date = $countdown_timer->start_date ?? '';
    $end_date = $countdown_timer->end_date ?? '';
    $status = $countdown_timer->status ?? 0;

    $products = \App\Helpers\CustomHelper::getProducts();
    $product_ids = explode(",",$product_ids);
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
                    <li class="breadcrumb-item active" aria-current="page">CountDown Timer</li>
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
                            <div class="d-none d-md-flex">CountDown Timer</div>
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
                            <input type="hidden" id="id" value="1">

                            <div class="row">
                                <!-- Title -->
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $title) }}">
                                    @include('snippets.errors_first', ['param' => 'title'])
                                </div>

                                <!-- Description -->
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Description</label>
                                    <textarea class="form-control" name="description">{{ old('description', $description) }}</textarea>
                                    @include('snippets.errors_first', ['param' => 'description'])
                                </div>

                                <!-- Start Date -->
                                <div class="form-group col-md-3 mt-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ old('start_date', $start_date) }}">
                                    @include('snippets.errors_first', ['param' => 'start_date'])
                                </div>

                                <!-- End Date -->
                                <div class="form-group col-md-3 mt-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $end_date) }}">
                                    @include('snippets.errors_first', ['param' => 'end_date'])
                                </div>

                                <!-- Start Time -->
                                <div class="form-group col-md-3 mt-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" name="start_time" value="{{ old('start_time', $start_time) }}">
                                    @include('snippets.errors_first', ['param' => 'start_time'])
                                </div>

                                <!-- End Time -->
                                <div class="form-group col-md-3 mt-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control" name="end_time" value="{{ old('end_time', $end_time) }}">
                                    @include('snippets.errors_first', ['param' => 'end_time'])
                                </div>

                                <!-- Choose Products -->
                                <div class="form-group col-md-6 mt-3">
                                    <label for="product_id" class="form-label">Choose Products</label>
                                    <select class="form-control select2" multiple name="product_ids[]">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, (array)$product_ids) ? "selected" : "" }}>
                                                {{ $product->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'product_id'])
                                </div>

                                <!-- Status -->
                                <div class="form-group col-md-6 mt-3">
                                    <label class="form-label">Status<span class="text-danger">*</span></label>
                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status" value="1"
                                            {{ $status == '1' || $status === null ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status" value="0"
                                            {{ isset($status) && $status == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label">Inactive</label>
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
