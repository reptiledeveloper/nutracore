@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();


    $suppliers_id = isset($suppliers->id) ? $suppliers->id : '';
    $name = isset($suppliers->name) ? $suppliers->name : '';
    $contact_person = isset($suppliers->contact_person) ? $suppliers->contact_person : '';
    $phone = isset($suppliers->phone) ? $suppliers->phone : '';
    $email = isset($suppliers->email) ? $suppliers->email : '';
    $address = isset($suppliers->address) ? $suppliers->address : '';
    $status = isset($suppliers->status) ? $suppliers->status : 1;
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
                            <input type="hidden" id="id" value="{{ $suppliers_id }}">

                            <div class="row">

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $name) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" name="contact_person" value="{{ old('contact_person', $contact_person) }}">
                                    @include('snippets.errors_first', ['param' => 'contact_person'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $phone) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email" value="{{ old('email', $email) }}">
                                    @include('snippets.errors_first', ['param' => 'email'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address', $address) }}">
                                    @include('snippets.errors_first', ['param' => 'address'])
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
