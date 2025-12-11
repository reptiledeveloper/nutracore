@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $nc_partners_id = isset($nc_partners->id) ? $nc_partners->id : '';
    $full_name = $nc_partners->full_name ?? '';
    $mobile_number = $nc_partners->mobile_number ?? '';
    $email = $nc_partners->email ?? '';
    $full_address = $nc_partners->full_address ?? '';
    $city = $nc_partners->city ?? '';
    $role = $nc_partners->role ?? '';
    $brand_name = $nc_partners->brand_name ?? '';
    $active_clients = $nc_partners->active_clients ?? '';
    $bank_name = $nc_partners->bank_name ?? '';
    $ifsc_code = $nc_partners->ifsc_code ?? '';
    $account_number = $nc_partners->account_number ?? '';
    $promotion_plan = $nc_partners->promotion_plan ?? '';
    $social_links = $nc_partners->social_links ?? '';
    $active_followers = $nc_partners->active_followers ?? '';
    $contact_method = $nc_partners->contact_method ?? '';
    $agree_terms = $nc_partners->agree_terms ?? 0;
    $status = $nc_partners->status ?? 'Pending Review';
    $coupon_code = $nc_partners->coupon_code ?? '';
    $remarks = $nc_partners->remarks ?? '';


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
                        <form class="card-body" action="" method="post" accept-charset="UTF-8"
                              enctype="multipart/form-data" role="form">

                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $nc_partners_id }}">

                            <div class="row">

                                <!-- Full Name -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name"
                                           value="{{ old('full_name', $full_name) }}">
                                    @include('snippets.errors_first', ['param' => 'full_name'])
                                </div>

                                <!-- Mobile Number -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" maxlength="10" class="form-control" name="mobile_number"
                                           value="{{ old('mobile_number', $mobile_number) }}">
                                    @include('snippets.errors_first', ['param' => 'mobile_number'])
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email"
                                           value="{{ old('email', $email) }}">
                                    @include('snippets.errors_first', ['param' => 'email'])
                                </div>

                                <!-- Role -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-control" name="role">
                                        <option value="">Select</option>
                                        <option value="Trainer" {{ $role=='Trainer'?'selected':'' }}>Trainer</option>
                                        <option value="Gym Owner" {{ $role=='Gym Owner'?'selected':'' }}>Gym Owner
                                        </option>
                                        <option value="Coach" {{ $role=='Coach'?'selected':'' }}>Coach</option>
                                        <option value="Influencer" {{ $role=='Influencer'?'selected':'' }}>Influencer
                                        </option>
                                        <option value="Nutritionist" {{ $role=='Nutritionist'?'selected':'' }}>
                                            Nutritionist
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'role'])
                                </div>

                                <!-- Full Address -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Full Address</label>
                                    <textarea class="form-control" name="full_address"
                                              rows="2">{{ old('full_address', $full_address) }}</textarea>
                                    @include('snippets.errors_first', ['param' => 'full_address'])
                                </div>

                                <!-- City -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city"
                                           value="{{ old('city', $city) }}">
                                    @include('snippets.errors_first', ['param' => 'city'])
                                </div>

                                <!-- Brand Name -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" name="brand_name"
                                           value="{{ old('brand_name', $brand_name) }}">
                                    @include('snippets.errors_first', ['param' => 'brand_name'])
                                </div>

                                <!-- Active Clients -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Active Clients</label>
                                    <input type="text" class="form-control" name="active_clients"
                                           value="{{ old('active_clients', $active_clients) }}">
                                    @include('snippets.errors_first', ['param' => 'active_clients'])
                                </div>

                                <!-- Bank Name -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name"
                                           value="{{ old('bank_name', $bank_name) }}">
                                    @include('snippets.errors_first', ['param' => 'bank_name'])
                                </div>

                                <!-- IFSC Code -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" name="ifsc_code"
                                           value="{{ old('ifsc_code', $ifsc_code) }}">
                                    @include('snippets.errors_first', ['param' => 'ifsc_code'])
                                </div>

                                <!-- Account Number -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control" name="account_number"
                                           value="{{ old('account_number', $account_number) }}">
                                    @include('snippets.errors_first', ['param' => 'account_number'])
                                </div>

                                <!-- Promotion Plan -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Promotion Plan</label>
                                    <textarea class="form-control" name="promotion_plan"
                                              rows="2">{{ old('promotion_plan', $promotion_plan) }}</textarea>
                                    @include('snippets.errors_first', ['param' => 'promotion_plan'])
                                </div>

                                <!-- Social Links -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Social Links</label>
                                    <input type="text" class="form-control" name="social_links"
                                           value="{{ old('social_links', $social_links) }}">
                                    @include('snippets.errors_first', ['param' => 'social_links'])
                                </div>

                                <!-- Active Followers -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Active Followers</label>
                                    <input type="text" class="form-control" name="active_followers"
                                           value="{{ old('active_followers', $active_followers) }}">
                                    @include('snippets.errors_first', ['param' => 'active_followers'])
                                </div>

                                <!-- Preferred Contact Method -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Preferred Contact Method</label>
                                    <select class="form-control" name="contact_method">
                                        <option value="">Select</option>
                                        <option value="WhatsApp" {{ $contact_method=='WhatsApp'?'selected':'' }}>
                                            WhatsApp
                                        </option>
                                        <option value="Call" {{ $contact_method=='Call'?'selected':'' }}>Call</option>
                                        <option value="Email" {{ $contact_method=='Email'?'selected':'' }}>Email
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'contact_method'])
                                </div>

                                <!-- Coupon Code -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Coupon Code</label>
                                    <input type="text" class="form-control" name="coupon_code"
                                           value="{{ old('coupon_code', $coupon_code) }}">
                                </div>

                                <!-- Agree Terms -->
                                <div class="form-group col-md-4 mt-4">
                                    <label class="form-label">Agree Terms</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="agree_terms" value="1"
                                            {{ $agree_terms==1?'checked':'' }}>
                                        <label class="form-check-label">Yes, I agree</label>
                                    </div>
                                    @include('snippets.errors_first', ['param' => 'agree_terms'])
                                </div>

                                <!-- Status -->
                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Status</label>

                                    <div class="form-check custom-checkbox mb-2">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="Approved" {{ $status=='Approved'?'checked':'' }}>
                                        <label class="form-check-label">Approved</label>
                                    </div>

                                    <div class="form-check custom-checkbox mb-2">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="Rejected" {{ $status=='Rejected'?'checked':'' }}>
                                        <label class="form-check-label">Rejected</label>
                                    </div>

                                    <div class="form-check custom-checkbox">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="Pending Review" {{ $status=='Pending Review'?'checked':'' }}>
                                        <label class="form-check-label">Pending Review</label>
                                    </div>
                                    <div class="form-check custom-checkbox">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="Ban" {{ $status=='Ban'?'checked':'' }}>
                                        <label class="form-check-label">Ban</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4 mt-3">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" class="form-control" name="remarks"
                                           value="{{ old('remarks', $remarks) }}">
                                </div>

                            </div>

                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>

                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
