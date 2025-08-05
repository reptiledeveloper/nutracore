@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $agents_id = $agents->id ?? '';
    $name = $agents->name ?? '';
    $vendor_id = $agents->vendor_id ?? '';
    $phone = $agents->phone ?? '';
    $address = $agents->address ?? '';
    $email = $agents->email ?? '';
    $status = $agents->status ?? '';
    $image = $agents->image ?? '';
    $alternate_phone = $agents->alternate_phone ?? '';
    $vehicle_no = $agents->vehicle_no ?? '';
    $per_order_type = $agents->per_order_type ?? '';
    $vehicle_document = $agents->vehicle_document ?? '';
    $adhar_front = $agents->adhar_front ?? '';
    $adhar_back = $agents->adhar_back ?? '';
    $work_status = $agents->work_status ?? '';
    $per_order_value = $agents->per_order_value ?? '';
    $driving_licence = $agents->driving_licence ?? '';
    $dl_no = $agents->dl_no ?? '';
    $adhar_card = $agents->adhar_card ?? '';
    $vehicle_type = $agents->vehicle_type ?? '';
    $vehicle_name = $agents->vehicle_name ?? '';
    $account_no = $agents->account_no ?? '';
    $bank_name = $agents->bank_name ?? '';
    $ifsc_code = $agents->ifsc_code ?? '';
    $pan_no = $agents->pan_no ?? '';
    if (!empty($image)) {
        $image = \App\Helpers\CustomHelper::getImageUrl('agents', $image);
    } else {
        $image = url('public/assets/img/products/vender-upload-preview.jpg');
    }
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
                            <input type="hidden" id="id" value="{{ $agents_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Name" name="image"
                                           value="">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Name</label>
                                    <input type="text" class="form-control"

                                           name="name" value="{{ old('name', $name) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
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
                                    <label for="inputEmail4" class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email"
                                           value="{{ old('email', $email) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                           value="{{ old('phone', $phone) }}">
                                    @include('snippets.errors_first', ['param' => 'phone'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Alt Phone</label>
                                    <input type="text" class="form-control" name="alternate_phone"
                                           value="{{ old('alternate_phone', $alternate_phone) }}">
                                    @include('snippets.errors_first', ['param' => 'alternate_phone'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Password</label>
                                    <input type="text" class="form-control" name="password" value="">
                                    @include('snippets.errors_first', ['param' => 'password'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Per Order Type</label>
                                    <select class="form-control" name="per_order_type">
                                        <option value="" selected>Select</option>
                                        <option value="fixed" {{$per_order_type == 'fixed'?"selected":""}}>Fixed
                                        </option>
                                        <option value="percentage" {{$per_order_type == 'percentage'?"selected":""}}>
                                            Percentage
                                        </option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'per_order_type'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Per Order Value</label>
                                    <input type="number" class="form-control" name="per_order_value" value="{{old('per_order_value',$per_order_value)}}">
                                    @include('snippets.errors_first', ['param' => 'per_order_value'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Adhar Card No</label>
                                    <input type="text" class="form-control" name="adhar_card"
                                           value="{{ old('adhar_card', $adhar_card) }}">
                                    @include('snippets.errors_first', ['param' => 'adhar_card'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Vehicle Type</label>
                                    <input type="text" class="form-control" name="vehicle_type"
                                           value="{{ old('vehicle_type', $vehicle_type) }}">
                                    @include('snippets.errors_first', ['param' => 'vehicle_type'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Vehicle Name</label>
                                    <input type="text" class="form-control" name="vehicle_name"
                                           value="{{ old('vehicle_name', $vehicle_name) }}">
                                    @include('snippets.errors_first', ['param' => 'vehicle_name'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Vehicle No</label>
                                    <input type="text" class="form-control" name="vehicle_no"
                                           value="{{ old('vehicle_no', $vehicle_no) }}">
                                    @include('snippets.errors_first', ['param' => 'vehicle_no'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Account No</label>
                                    <input type="text" class="form-control" name="account_no"
                                           value="{{ old('account_no', $account_no) }}">
                                    @include('snippets.errors_first', ['param' => 'account_no'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name"
                                           value="{{ old('bank_name', $bank_name) }}">
                                    @include('snippets.errors_first', ['param' => 'bank_name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" name="ifsc_code"
                                           value="{{ old('ifsc_code', $ifsc_code) }}">
                                    @include('snippets.errors_first', ['param' => 'ifsc_code'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">PAN Code</label>
                                    <input type="text" class="form-control" name="pan_no"
                                           value="{{ old('pan_no', $pan_no) }}">
                                    @include('snippets.errors_first', ['param' => 'pan_no'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">DL No</label>
                                    <input type="text" class="form-control" name="dl_no"
                                           value="{{ old('dl_no', $dl_no) }}">
                                    @include('snippets.errors_first', ['param' => 'pan_no'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Work Status</label>
                                    <select class="form-control" name="work_status">
                                        <option value="" selected>Select</option>
                                        <option value="1" {{$work_status == 1 ?"selected":""}}>Active</option>
                                        <option value="0" {{$work_status == 1 ?"selected":""}}>InActive</option>
                                    </select>
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Adhar Card</label>
                                    <input type="file" class="form-control" placeholder="" name="adhar_front"
                                           value="">

                                    <div class="mt-3">
                                        <a href="{{\App\Helpers\CustomHelper::getImageUrl('agents',$adhar_front)}}"
                                           target="_blank"><img
                                                src="{{\App\Helpers\CustomHelper::getImageUrl('agents',$adhar_front)}}" height="50px" width="50px"></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Adhar Card Back</label>
                                    <input type="file" class="form-control" placeholder="" name="adhar_back"
                                           value="">

                                    <div class="mt-3">
                                        <a href="{{\App\Helpers\CustomHelper::getImageUrl('agents',$adhar_back)}}"
                                           target="_blank"><img
                                                src="{{\App\Helpers\CustomHelper::getImageUrl('agents',$adhar_back)}}" height="50px" width="50px"></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Vehicle Document</label>
                                    <input type="file" class="form-control" placeholder="" name="vehicle_document"
                                           value="">
                                    <div class="mt-3">
                                        <a href="{{\App\Helpers\CustomHelper::getImageUrl('agents',$vehicle_document)}}"
                                           target="_blank"><img
                                                src="{{\App\Helpers\CustomHelper::getImageUrl('agents',$vehicle_document)}}" height="50px" width="50px"></a>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Driving Licence</label>
                                    <input type="file" class="form-control" placeholder="" name="driving_licence"
                                           value="">
                                    <div class="mt-3">
                                        <a href="{{\App\Helpers\CustomHelper::getImageUrl('agents',$driving_licence)}}"
                                           target="_blank"><img
                                                src="{{\App\Helpers\CustomHelper::getImageUrl('agents',$driving_licence)}}" height="50px" width="50px"></a>
                                    </div>
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
