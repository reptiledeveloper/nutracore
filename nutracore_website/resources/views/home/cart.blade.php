@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $user_address = $user->addresses ??'';
    $default_address = \App\Models\UserAddress::where('id',$user->addressID)->first();

    ?>
    <style>
        .address-item {
            cursor: pointer;
            border: 1px solid #ddd;
            transition: border-color 0.3s;
            margin-top: 10px;
        }

        .address-item.selected {
            border-color: #0d6efd; /* Bootstrap primary color */
            background-color: #e7f1ff; /* optional light background on select */
        }
        .freebies-slider-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px 0;
        }

        .freebies-slider {
            display: flex;
            gap: 12px;
            scroll-snap-type: x mandatory;
            padding-bottom: 5px;
        }

        .freebie-card {
            flex: 0 0 auto;
            width: 260px;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            gap: 12px;
            align-items: center;
            scroll-snap-align: start;
            border: 1px solid #e4e4e4;
            background: #fff;
            flex-wrap: nowrap;
        }
        .freebie-details strong {
            white-space: normal !important;
            display: block;
        }


        .freebie-img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }

        .freebie-details {
            flex-grow: 1;
            white-space: normal !important;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .btn-freebie {
            padding: 6px 14px;
            font-size: 14px;
            border-radius: 8px;
            border: none;
            background: #00c8c8;
            color: white;
        }

        .btn-freebie.remove {
            background: #ff4d4f !important; /* red */
        }
        .subscription-slider-container {
            overflow-x: auto;
            scroll-behavior: smooth;
            white-space: nowrap;
            padding-bottom: 5px;
        }

        .subscription-slider {
            display: flex;
            gap: 12px;
        }

        .subscription-card {
            min-width: 210px;
        }

        .subscription-box {
            background: #FFF8E1;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .subscription-box .text-yellow {
            color: #FFA726;
        }

        .subscription-btn.remove {
            background-color: #dc3545 !important;
            color: white !important;
        }
        .subscription-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .subscription-box small {
            display: block;
            white-space: normal;     /* ✅ allow wrapping */
            word-wrap: break-word;   /* ✅ wrap long words if needed */
            max-width: 70%;          /* ✅ prevent text from pushing button */
        }
        .delivery-option {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .delivery-option.selected {
            border-color: #00AEEF;
            background-color: #E9F8FF;
        }

    </style>
    <style>
        .delivery-option {
            border: 2px solid #e6e6e6;
            border-radius: 10px;
            padding: 14px 18px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            transition: 0.25s ease-in-out;
        }

        .delivery-option.selected {
            border-color: #00AEEF; /* highlight like screenshot */
            background-color: #ECF9FF;
        }

        .delivery-title {
            font-weight: 600;
            font-size: 16px;
            display: block;
        }

        .delivery-sub {
            font-size: 13px;
            color: #00AEEF;
            font-weight: 500;
        }

        .delivery-badge {
            border: 1px solid #111;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        /* ✅ Sticky Pay Button */
        .sticky-pay-btn {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 0;
            z-index: 999;
            border-top: 1px solid #ddd;
        }

        /* ✅ Mobile: make it stick at bottom always and full width */
        @media (max-width: 768px) {
            .sticky-pay-btn {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 60px;
                width: 100%;
                padding: 12px;
                border-radius: 0;
            }
            .sticky-pay-btn button {
                border-radius: 0;
            }
        }


    </style>
    <style>
        .payment-toggle {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background: #fff;
            font-weight: 600;
        }

        .payment-options {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px;
            margin-top: 8px;
            background: #fff;
            display: none;
        }

        .payment-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 5px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .payment-row.selected {
            background-color: #e9f8ff;
            border: 1px solid #00AEEF;
        }

        .payment-row input[type="radio"] {
            width: 16px;
            height: 16px;
            accent-color: #00AEEF; /* Makes radio button colored */
            cursor: pointer;
            margin-top: 3px;
        }

        .payment-title {
            font-size: 15px;
            font-weight: 600;
        }

        .payment-subtitle {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Shop
                    <span></span> Cart
                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50" id="cart_html">


        </div>
    </main>

   <form id="cartSubmitForm">
       <input type="hidden" name="selected_addressID" id="selected_addressID" value="{{$selectedAddress->id??''}}">
       <input type="hidden" name="subscription_id" id="subscription_id" value="">
       <input type="hidden" name="coupon_code" id="coupon_code" value="">
       <input type="hidden" name="freebees_id" id="freebees_id" value="">
       <input type="hidden" name="delivery_type" id="delivery_type" value="">
       <input type="hidden" name="apply_cashback" id="apply_cashback" value="">
       <input type="hidden" id="selected_payment" name="payment_method">
   </form>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Addresses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Saved Addresses -->
                    <h6>Saved Addresses</h6>
                    <div class="list-group mb-4">
                        @foreach($user->addresses ?? [] as $address)
                            @php
                                $selected = ($address->id == $user->addressID) ? "selected" : "";
                            @endphp
                            <label class="list-group-item address-item d-flex justify-content-between align-items-start {{ $selected }}" data-id="{{ $address->id }}">
                                <div>
                                    <strong>{{ $address->address_type ?? "Others" }}</strong><br>
                                    <small>{{ $address->flat_no ?? '' }}, {{ $address->building_name ?? '' }}, {{ $address->landmark ?? '' }} - {{ $address->pincode ?? '' }}</small><br>
                                    <small>{{ $address->location ?? '' }}</small>
                                </div>
                                <input class="form-check-input d-none" type="radio" name="selected_address" value="{{ $address->id }}" {{ $selected ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>


                    <!-- Add New Address Form -->
                    <h6>Add New Address</h6>
                    <form id="addressForm">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <input type="text" name="location" class="form-control" placeholder="Location">
                            </div>
                            <div class="col-md-6 mb-2">
                                <select name="address_type" class="form-control">
                                    <option value="">Select Address Label</option>
                                    <option value="Home">Home</option>
                                    <option value="Work">Work</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="flat_no" class="form-control" placeholder="Flat No / Street">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="building_name" class="form-control" placeholder="Building Name">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="landmark" class="form-control" placeholder="Landmark">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="pincode" class="form-control" placeholder="Pincode">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Address</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            getCartHtml();
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addressItems = document.querySelectorAll('.address-item');

            // On load, ensure the pre-selected address is reflected in the hidden input
            const selectedItem = document.querySelector('.address-item.selected');
            if (selectedItem) {
                const radio = selectedItem.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    document.getElementById('selected_addressID').value = radio.value;
                }
            }

            // Add click event to all items
            addressItems.forEach(item => {
                item.addEventListener('click', function () {
                    // Remove 'selected' from all
                    addressItems.forEach(i => i.classList.remove('selected'));

                    // Add 'selected' to clicked one
                    this.classList.add('selected');

                    // Select the hidden radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;

                        // Update hidden input value
                        document.getElementById('selected_addressID').value = radio.value;

                        var _token = '{{ csrf_token() }}';
                        $.ajax({
                            url: "{{ url('update_selected_address') }}",
                            type: "POST",
                            data: {addressID: radio.value},
                            dataType: "JSON",
                            headers: {'X-CSRF-TOKEN': _token},
                            cache: false,
                            success: function (resp) {
                                var addr = resp.address;
                                var html = '<div class="fw-bold">' + (addr.address_type || "Others") + '</div>' +
                                    '<small>' + (addr.flat_no || '') + ', ' + (addr.building_name || '') + ', ' + (addr.landmark || '') + ' - ' + (addr.pincode || '') + '</small><br>' +
                                    '<small>' + (addr.location || '') + '</small>';

                                $('#selectedAddress').html(html);
                                getCartHtml(); // refresh cart
                                $('#addressModal').modal('hide');
                            }
                        });
                    }
                });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#addressForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize(); // Serialize form fields
                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ url('save_address') }}",  // Your route to save the address
                    type: "POST",
                    data: formData,
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': _token },
                    cache: false,
                    success: function(resp) {
                        if (resp.address) {
                            var addr = resp.address;
                            var html = '<div class="fw-bold">' + (addr.address_type || "Others") + '</div>' +
                                '<small>' + (addr.flat_no || '') + ', ' + (addr.building_name || '') + ', ' + (addr.landmark || '') + ' - ' + (addr.pincode || '') + '</small><br>' +
                                '<small>' + (addr.city || '') + ', ' + (addr.state || '') + '</small><br>' +
                                '<small>' + (addr.location || '') + '</small>';

                            $('#selectedAddress').html(html);
                            getCartHtml(); // refresh cart
                            // Optionally close the modal if it's open
                            $('#addressModal').modal('hide');
                            $('#addressForm')[0].reset();
                            // Optionally update the hidden input
                            $('#selected_addressID').val(addr.id);
                        }
                    },
                    error: function(xhr) {
                        alert("Error saving address.");
                    }
                });
            });
        });

        $(document).ready(function () {
            selectFreebees();
            selectCoupon_code();
            selectNCCash();
            setSubscription();
        });
        async function selectFreebees() {
            let freebeeHidden = $("#freebees_id");
            let selectedID = freebeeHidden.val(); // get selected value on page load
                if (selectedID !== "") {
                // find the button with same ID
                let btn = $(".btn-freebie[data-id='" + selectedID + "']");
                btn.addClass("remove").text("Remove");
            }
        }
        async  function selectNCCash() {
            let cashbackHidden = $("#apply_cashback");
            let cashbackBtn = $("#cashbackApplyBtn");
            console.log("sadasdasd",cashbackHidden.val());
            if (cashbackHidden.val() === "true") {
                cashbackBtn.text("Remove")
                    .removeClass("btn-warning")
                    .addClass("btn-danger");
            } else {
                cashbackBtn.text("Apply")
                    .removeClass("btn-danger")
                    .addClass("btn-warning");
            }
        }
        async function selectCoupon_code() {

            console.log("couponCodecouponCode");

            let couponTextBox = $("#coupon_codeval");
            let couponCode = $("#coupon_code").val(); // hidden input
            let couponBtn = $('#couponApplyBtn');

            if (couponCode === "" || couponCode == null) {

                couponTextBox.val("").prop("readonly", false); // allow edit

                couponBtn.text("Apply")
                    .removeClass("btn-danger")
                    .addClass("btn-primary");

            } else {

                couponTextBox.val(couponCode).prop("readonly", true); // show applied coupon

                couponBtn.text("Remove")
                    .removeClass("btn-primary")
                    .addClass("btn-danger");
            }
        }
    async function setSubscription(){
        let prevSelectedID = $("#subscription_id").val();

        if (prevSelectedID) {
            $(`.subscription-btn[data-id="${prevSelectedID}"]`)
                .addClass("remove")
                .text("Remove");
        }

    }

    </script>


@endsection
