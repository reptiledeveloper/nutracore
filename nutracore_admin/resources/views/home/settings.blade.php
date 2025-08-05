@extends('layouts.layout')
@section('content')
    <?php
    $user = Auth::guard('admin')->user();
    $name = $user->name ?? '';
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();

    $privacy_policy = $settings->privacy_policy ?? '';
    $terms = $settings->terms ?? '';
    $about_us = $settings->about_us ?? '';
    $refund_policy = $settings->refund_policy ?? '';
    $contact_address = $settings->contact_address ?? '';
    $contact_phone = $settings->contact_phone ?? '';
    $contact_email = $settings->contact_email ?? '';
    $contact_us = $settings->contact_us ?? '';
    $admin_commission = $settings->admin_commission ?? '';
    $user_commission = $settings->user_commission ?? '';
    $razorpay_key_test = $settings->razorpay_key_test ?? '';
    $razorpay_secret_test = $settings->razorpay_secret_test ?? '';
    $razorpay_key_live = $settings->razorpay_key_live ?? '';
    $google_map_key = $settings->google_map_key ?? '';
    $razorpay_secret_live = $settings->razorpay_secret_live ?? '';
    $is_live = $settings->is_live ?? 0;
    $refer_amount = $settings->refer_amount ?? '';
    $contact_whatsapp = $settings->contact_whatsapp ?? 0;
    $subscription_month = $settings->subscription_month ?? 3;

    $is_handling_charges = $settings->is_handling_charges ?? 0;
    $handling_charges = $settings->handling_charges ?? '';

    $is_surge_fee = $settings->is_surge_fee ?? 0;
    $surge_fee = $settings->surge_fee ?? '';

    $is_platform_fee = $settings->is_platform_fee ?? 0;
    $platform_fee = $settings->platform_fee ?? '';

    $is_small_cart_fee = $settings->is_small_cart_fee ?? 0;
    $small_cart_fee = $settings->small_cart_fee ?? '';
    $cashback_wallet_use = $settings->cashback_wallet_use ?? '';
    $delhivery_key = $settings->delhivery_key ?? '';
    $delhivery_url = $settings->delhivery_url ?? '';
    
    ?>
    <style>
        #fileInput {
            display: none; /* Hide the file input */
        }
    </style>

    <div class="content ">
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row flex-column-reverse flex-md-row">
            <div class="col-md-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="mb-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title mb-4">Settings</h6>
                                    <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                                          enctype="multipart/form-data" role="form">
                                        {{ csrf_field() }}

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Privacy Policy</label>
                                                    <textarea name="privacy_policy"
                                                              class="form-control editor">{{old('privacy_policy',$privacy_policy)}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">About US</label>
                                                    <textarea name="about_us"
                                                              class="form-control editor">{{old('about_us',$about_us)}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Terms & Condition</label>
                                                    <textarea name="terms"
                                                              class="form-control editor">{{old('terms',$terms)}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="inputEmail4" class="form-label">Refund Policy</label>
                                                <textarea name="refund_policy"
                                                          class="editor">{{ old('refund_policy', $refund_policy) }}</textarea>
                                            </div>

                                            <div class="col-md-12">
                                                <label for="inputEmail4" class="form-label">Contact US</label>
                                                <textarea name="contact_us"
                                                          class="editor">{{ old('contact_us', $contact_us) }}</textarea>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="form-label">Contact Address</label>
                                                <input type="text" class="form-control" placeholder="Contact Address"
                                                       name="contact_address"
                                                       value="{{ old('contact_address', $contact_address) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="form-label">Contact Phone</label>
                                                <input type="text" class="form-control" placeholder="Contact Phone"
                                                       name="contact_phone"
                                                       value="{{ old('contact_phone', $contact_phone) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Contact Email</label>
                                                <input type="text" class="form-control" placeholder="Contact Email"
                                                       name="contact_email"
                                                       value="{{ old('contact_email', $contact_email) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Contact Whatsapp</label>
                                                <input type="text" class="form-control" placeholder="Contact Whatsapp"
                                                       name="contact_whatsapp"
                                                       value="{{ old('contact_whatsapp', $contact_whatsapp) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Razorpay Test Key</label>
                                                <input type="text" class="form-control" placeholder="Razorpay Test Key"
                                                       name="razorpay_key_test"
                                                       value="{{ old('razorpay_key_test', $razorpay_key_test) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Razorpay Test Secret</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Razorpay Test Secret"
                                                       name="razorpay_secret_test"
                                                       value="{{ old('razorpay_secret_test', $razorpay_secret_test) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Razorpay Live Key</label>
                                                <input type="text" class="form-control" placeholder="Razorpay Live Key"
                                                       name="razorpay_key_live"
                                                       value="{{ old('razorpay_key_live', $razorpay_key_live) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Razorpay Live Secret</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Razorpay Live Secret"
                                                       name="razorpay_secret_live"
                                                       value="{{ old('razorpay_secret_live', $razorpay_secret_live) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Is Razorpay Live</label>
                                                <select class="form-control" name="is_live">
                                                    <option value="" selected>Select</option>
                                                    <option value="1" <?php if ($is_live == 1) echo "selected" ?>>Yes
                                                    </option>
                                                    <option value="0" <?php if ($is_live == 0) echo "selected" ?>>No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Google Map Key</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Google Map Key"
                                                       name="google_map_key"
                                                       value="{{ old('google_map_key', $google_map_key) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Subscription Calender</label>
                                                <input type="number" class="form-control"
                                                       placeholder="Subscription Calender"
                                                       name="subscription_month"
                                                       value="{{ old('subscription_month', $subscription_month) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Referal Amount</label>
                                                <input type="number" class="form-control"
                                                       placeholder="Referal Amount"
                                                       name="refer_amount"
                                                       value="{{ old('refer_amount', $refer_amount) }}">
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Cashback Wallet Apply (in %)</label>
                                                <input type="number" class="form-control"
                                                       placeholder="Cashback Wallet Apply (in %)"
                                                       name="cashback_wallet_use"
                                                       value="{{ old('cashback_wallet_use', $cashback_wallet_use) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Delhivery Key</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Delhivery Key"
                                                       name="delhivery_key"
                                                       value="{{ old('delhivery_key', $delhivery_key) }}">
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="" class="form-label">Delhivery URL</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Delhivery URL"
                                                       name="delhivery_url"
                                                       value="{{ old('delhivery_url', $delhivery_url) }}">
                                            </div>
                                        </div>


                                        <h3>Extra Charges</h3>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <h4>Handling Charges</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="is_handling_charges">
                                                    <option value="1" {{$is_handling_charges==1?"selected":""}}>Yes
                                                    </option>
                                                    <option value="0" {{$is_handling_charges==0?"selected":""}}>No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" value="{{$handling_charges??''}}"
                                                       class="form-control" placeholder="Enter Charge"
                                                       name="handling_charges">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <h4>SurgeFee</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="is_surge_fee">
                                                    <option value="1" {{$is_surge_fee==1?"selected":""}}>Yes
                                                    </option>
                                                    <option value="0" {{$is_surge_fee==0?"selected":""}}>No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" value="{{$surge_fee??''}}"
                                                       class="form-control" placeholder="Enter Charge"
                                                       name="surge_fee">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <h4>Platform Charges</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="is_platform_fee">
                                                    <option value="1" {{$is_platform_fee==1?"selected":""}}>Yes
                                                    </option>
                                                    <option value="0" {{$is_platform_fee==0?"selected":""}}>No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" value="{{$platform_fee??''}}"
                                                       class="form-control" placeholder="Enter Charge"
                                                       name="platform_fee">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <h4>SmallCart Fee</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="is_small_cart_fee">
                                                    <option value="1" {{$is_small_cart_fee==1?"selected":""}}>Yes
                                                    </option>
                                                    <option value="0" {{$is_small_cart_fee==0?"selected":""}}>No
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" value="{{$small_cart_fee??''}}"
                                                       class="form-control" placeholder="Enter Charge"
                                                       name="small_cart_fee">
                                            </div>
                                        </div>


                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <button class="btn btn-primary" type="submit">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
