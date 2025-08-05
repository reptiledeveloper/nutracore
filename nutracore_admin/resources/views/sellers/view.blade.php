@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $sellers_id = $seller->id ?? '';
    $name = $seller->name ?? '';
    $status = $seller->status ?? '';
    $user_name = $seller->user_name ?? '';
    $user_email = $seller->user_email ?? '';
    $user_phone = $seller->user_phone ?? '';
    $bank_code = $seller->bank_code ?? '';
    $bank_name = $seller->bank_name ?? '';
    $address = $seller->address ?? '';
    $commission = $seller->commission ?? '';
    $tax_name = $seller->tax_name ?? '';
    $tax_number = $seller->tax_number ?? '';
    $is_view_customer_details = $seller->is_view_customer_details ?? 0;
    $is_view_order_otp = $seller->is_view_order_otp ?? 0;
    $is_assign_delivery_boy = $seller->is_assign_delivery_boy ?? 0;
    $is_product_approval = $seller->is_product_approval ?? 0;
    $account_no = $seller->account_no ?? '';
    $pan_no = $seller->pan_no ?? '';
    $longitude = $seller->longitude ?? '';
    $latitude = $seller->latitude ?? '';
    $account_name = $seller->account_name ?? '';
    $google_address = $seller->google_address??'';
    $radius = $seller->radius??'';
    $image = \App\Helpers\CustomHelper::getImageUrl('sellers', $seller->image);
    ?>
    @include('sellers.common',['seller'=>$seller])


    @include('snippets.errors')
    @include('snippets.flash')
    <div class="card mt-3">
        <div class="card-body pt-0">
            <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                  enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <input type="hidden" id="id" value="{{ $sellers_id }}">
                <div class="row">
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">User Name</label>
                        <input type="text" class="form-control" name="user_name"
                               value="{{ old('user_name', $user_name) }}">
                        @include('snippets.errors_first', ['param' => 'user_name'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">User Mobile</label>
                        <input type="text" class="form-control" name="user_phone"
                               value="{{ old('user_phone', $user_phone) }}" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                        @include('snippets.errors_first', ['param' => 'user_phone'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">User Email</label>
                        <input type="text" class="form-control" name="user_email"
                               value="{{ old('user_email', $user_email) }}">
                        @include('snippets.errors_first', ['param' => 'user_email'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address"
                               value="{{ old('address', $address) }}">
                        @include('snippets.errors_first', ['param' => 'address'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Google Address</label>
                        <input type="text" class="form-control" name="google_address" id="google_address"
                               value="{{ old('google_address', $google_address) }}">
                        @include('snippets.errors_first', ['param' => 'google_address'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" id="latitude"
                               value="{{ old('latitude', $latitude) }}">
                        @include('snippets.errors_first', ['param' => 'latitude'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" id="longitude"
                               value="{{ old('longitude', $longitude) }}">
                        @include('snippets.errors_first', ['param' => 'longitude'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Radius (In KM)</label>
                        <input type="text" class="form-control" name="radius"
                               value="{{ old('radius', $radius) }}">
                        @include('snippets.errors_first', ['param' => 'radius'])
                    </div>





                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Address Proof</label>
                        <input type="file" class="form-control" name="address_proof" value="">
                        @include('snippets.errors_first', ['param' => 'address'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Commission(%) (Commission(%) to
                            be given to the Super Admin on order item globally.)
                        </label>
                        <input type="text" class="form-control" name="commission"
                               value="{{ old('commission', $commission) }}">
                        @include('snippets.errors_first', ['param' => 'commission'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Store Name
                        </label>
                        <input type="text" class="form-control" name="name"
                               value="{{ old('name', $name) }}">
                        @include('snippets.errors_first', ['param' => 'name'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Logo
                        </label>
                        <input type="file" class="form-control" name="image"
                               value="">
                        @include('snippets.errors_first', ['param' => 'name'])
                    </div>


                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Account Number

                        </label>
                        <input type="text" class="form-control" name="account_no"
                               value="{{ old('account_no', $account_no) }}">
                        @include('snippets.errors_first', ['param' => 'account_no'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Account Name</label>
                        <input type="text" class="form-control" name="account_name"
                               value="{{ old('account_name', $account_name) }}">
                        @include('snippets.errors_first', ['param' => 'account_name'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" name="bank_code" maxlength="11"
                               value="{{ old('bank_code', $bank_code) }}">
                        @include('snippets.errors_first', ['param' => 'bank_code'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bank_name"
                               value="{{ old('bank_name', $bank_name) }}">
                        @include('snippets.errors_first', ['param' => 'bank_name'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">National Identity Card</label>
                        <input type="file" class="form-control" name="nic_card"
                               value="">
                        @include('snippets.errors_first', ['param' => 'nic_card'])
                    </div>


                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Tax Name</label>
                        <input type="text" class="form-control" name="tax_name"
                               value="{{ old('tax_name', $tax_name) }}">
                        @include('snippets.errors_first', ['param' => 'tax_name'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Tax Number</label>
                        <input type="text" class="form-control" name="tax_number"
                               value="{{ old('tax_number', $tax_number) }}">
                        @include('snippets.errors_first', ['param' => 'tax_number'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Pan Number</label>
                        <input type="text" class="form-control" name="pan_no" maxlength="10"
                               value="{{ old('pan_no', $pan_no) }}">
                        @include('snippets.errors_first', ['param' => 'pan_no'])
                    </div>

                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Require Product's Approval?
                            *</label>
                        <select class="form-control" name="is_product_approval">
                            <option value="" selected>Select</option>
                            <option value="1" {{$is_product_approval == 1 ? "selected":""}}>Yes</option>
                            <option value="0" {{$is_product_approval == 0 ? "selected":""}}>No</option>
                        </select>
                        @include('snippets.errors_first', ['param' => 'longitude'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">View Customer's Details?
                            *</label>
                        <select class="form-control" name="is_view_customer_details">
                            <option value="" selected>Select</option>
                            <option value="1" {{$is_view_customer_details == 1 ? "selected":""}}>Yes
                            </option>
                            <option value="0" {{$is_view_customer_details == 0 ? "selected":""}}>No</option>
                        </select>
                        @include('snippets.errors_first', ['param' => 'is_view_customer_details'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">View Order's OTP? & Can change
                            deliver status?</label>
                        <select class="form-control" name="is_view_order_otp">
                            <option value="" selected>Select</option>
                            <option value="1" {{$is_view_order_otp == 1 ? "selected":""}}>Yes</option>
                            <option value="0" {{$is_view_order_otp == 0 ? "selected":""}}>No</option>
                        </select>
                        @include('snippets.errors_first', ['param' => 'is_view_order_otp'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Can assign delivery boy</label>
                        <select class="form-control" name="is_assign_delivery_boy">
                            <option value="" selected>Select</option>
                            <option value="1" {{$is_assign_delivery_boy == 1 ? "selected":""}}>Yes</option>
                            <option value="0" {{$is_assign_delivery_boy == 0 ? "selected":""}}>No</option>
                        </select>
                        @include('snippets.errors_first', ['param' => 'is_assign_delivery_boy'])
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{googleMapKey()}}&libraries=places"></script>
    <script>
        $(document).ready(function () {
            var autocomplete;
            autocomplete = new google.maps.places.Autocomplete((document.getElementById('google_address')), {
                types: ['geocode']
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var near_place = autocomplete.getPlace();
                if (near_place.geometry) {
                    const latitude = near_place.geometry.location.lat();  // Get latitude
                    const longitude = near_place.geometry.location.lng(); // Get longitude
                    console.log('Latitude:', latitude);
                    console.log('Longitude:', longitude);

                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                    // You can also set these values somewhere or use them as needed
                    // input.value = near_place.formatted_address; // Set the input value to the selected address
                    // suggestionsContainer.style.display = 'none'; // Hide suggestions
                } else {
                    console.log('No geometry available for this place');
                }

            });
        });
    </script>
@endsection
