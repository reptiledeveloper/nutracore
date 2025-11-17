@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $user_address = $user->addresses ?? '';
    $default_address = \App\Models\UserAddress::where('id', $user->addressID)->first();

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
            white-space: normal; /* ✅ allow wrapping */
            word-wrap: break-word; /* ✅ wrap long words if needed */
            max-width: 70%; /* ✅ prevent text from pushing button */
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

        .address-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 15px;
        }

        .address-card {
            background: #f0fbfc;
            border: 1px solid #00b3b3;
            border-radius: 10px;
            padding: 15px;
            transition: 0.3s ease;
            position: relative;
        }

        .address-card.active {
            box-shadow: 0 0 10px rgba(0, 180, 180, 0.4);
            border: 2px solid #00b3b3;
        }

        .address-text {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }

        .address-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .address-type {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .address-type input[type="radio"] {
            accent-color: #00b3b3;
            transform: scale(1.2);
        }

        .edit-icon {
            color: #00b3b3;
            cursor: pointer;
            font-size: 16px;
        }

        .deliver-btn {
            width: 100%;
            background-color: #00b3b3;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 0;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .deliver-btn:hover {
            background-color: #009999;
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
        <input type="hidden" name="selected_addressID" id="selected_addressID" value="{{$user->addressID??''}}">
        <input type="hidden" name="subscription_id" id="subscription_id" value="">
        <input type="hidden" name="coupon_code" id="coupon_code" value="">
        <input type="hidden" name="freebees_id" id="freebees_id" value="">
        <input type="hidden" name="delivery_type" id="delivery_type" value="">
        <input type="hidden" name="apply_cashback" id="apply_cashback" value="">
        <input type="hidden" name="applied_cashback" id="applied_cashback" value="">
        <input type="hidden" id="selected_payment" name="payment_method">
    </form>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delivery Address</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addNewAddressBtn">
                            <i class="fa fa-plus me-1"></i> Add New Address
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Saved Addresses -->
                    <div class="address-list" id="address-list">
                        @foreach($user->addresses ?? [] as $address)
                            @php
                                $selected = ($address->id == $user->addressID);
                            @endphp
                            <div class="address-card {{ $selected ? 'active' : '' }}" data-id="{{ $address->id }}">
                                <div class="address-details">
                                    <p class="address-text mb-2">
                                        {{ $address->flat_no ?? '' }} {{ $address->building_name ?? '' }},
                                        {{ $address->landmark ?? '' }}<br>
                                        {{ $address->location ?? '' }}<br>
                                        {{ $address->city ?? '' }}, {{ $address->state ?? '' }}
                                        , {{ $address->pincode ?? '' }}<br>
                                        <strong>+91 {{ $address->contact_person_mobile ?? $user->phone??'' }}</strong>
                                    </p>
                                </div>

                                <div class="address-footer d-flex justify-content-between align-items-center mb-2">
                                    <div class="address-type">
                                        <label><strong>{{ ucfirst($address->address_type ?? 'Home') }}</strong></label>
                                    </div>
                                    <i class="fa fa-pencil edit-icon text-muted" style="cursor:pointer;"></i>
                                </div>

                                <button type="button" class="btn btn-primary deliver-btn w-100">Deliver Here</button>
                            </div>
                        @endforeach
                    </div>


                    <!-- Add New Address Form -->

                    <form id="addressForm" class="d-none" style="margin-top: 10px;">

                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <input type="text" name="location" id="add_address_search" class="form-control" placeholder="Location">
                            </div>
                            <input type="hidden" name="latitude" id="address_lat" value="">
                            <input type="hidden" name="longitude" id="address_long" value="">
                            <div class="col-md-6 mb-2">
                                <select name="address_type" class="form-control">
                                    <option value="">Select Address Label</option>
                                    <option value="Home">Home</option>
                                    <option value="Work">Work</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="flat_no" class="form-control" placeholder="Building Name/ Flat No">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="building_name" class="form-control"
                                       placeholder="Street Address">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="landmark" class="form-control" placeholder="Landmark">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="pincode" id="pincode" maxlength="6"
                                       class="form-control" placeholder="Pincode">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="state" id="state" class="form-control" placeholder="State" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="city" id="city" class="form-control" placeholder="City" readonly>
                            </div>



                            <div class="col-md-4 mb-2">
                                <input type="text" name="contact_person_name" class="form-control" placeholder="Contact Person Name" >
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="contact_person_mobile" class="form-control" placeholder="Contact Person Phone" >
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" id="cancelAddressForm" class="btn btn-danger">Cancel</button>
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
            const deliverButtons = document.querySelectorAll('.deliver-btn');

            deliverButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const addressCard = this.closest('.address-card');
                    if (!addressCard) return;

                    const addressID = addressCard.dataset.id;
                    const token = '{{ csrf_token() }}';

                    // Add loading state
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = 'Saving...';

                    // Remove active class from all
                    document.querySelectorAll('.address-card').forEach(c => c.classList.remove('active'));
                    addressCard.classList.add('active');
                    $('#selected_addressID').val(addressID);
                    // AJAX request
                    $.ajax({
                        url: "{{ url('update_selected_address') }}",
                        type: "POST",
                        data: {addressID: addressID},
                        dataType: "JSON",
                        headers: {'X-CSRF-TOKEN': token},
                        cache: false,
                        success: function (resp) {
                            const addr = resp.address;
                            const summaryHtml = `
    <span>Delivery to <strong>${addr.pincode || ''}, ${resp.stateName}</strong></span>
    <a href="#" style="color: #00A8A8; text-decoration: none; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addressModal">Change</a>
`;
                            $('.card-body').html(summaryHtml);
                            $('#addressModal').modal('hide');
                            getCartHtml();

                        },
                        complete: function () {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                });
            });
        });


        document.addEventListener('DOMContentLoaded', function () {
            const addNewAddressBtn = document.getElementById('addNewAddressBtn');
            const addressList = document.getElementById('address-list');
            const addressForm = document.getElementById('addressForm');
            const cancelAddressForm = document.getElementById('cancelAddressForm');

            // Show form, hide list
            addNewAddressBtn.addEventListener('click', function () {
                addressList.classList.add('d-none');
                addressForm.classList.remove('d-none');
            });

            // Cancel → show list
            cancelAddressForm.addEventListener('click', function () {
                addressForm.classList.add('d-none');
                addressList.classList.remove('d-none');
            });
        });


    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#addressForm').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize(); // Serialize form fields
                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ url('save_address') }}",  // Your route to save the address
                    type: "POST",
                    data: formData,
                    dataType: "JSON",
                    headers: {'X-CSRF-TOKEN': _token},
                    cache: false,
                    success: function (resp) {
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

                            const addressList = document.getElementById('address-list');
                            const addressForm = document.getElementById('addressForm');
                            addressForm.classList.add('d-none');
                            addressList.classList.remove('d-none');
                        }
                    },
                    error: function (xhr) {
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

        async function selectNCCash() {
            let cashbackHidden = $("#apply_cashback");
            let cashbackBtn = $("#cashbackApplyBtn");
            console.log("sadasdasd", cashbackHidden.val());
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

        async function setSubscription() {
            let prevSelectedID = $("#subscription_id").val();

            if (prevSelectedID) {
                $(`.subscription-btn[data-id="${prevSelectedID}"]`)
                    .addClass("remove")
                    .text("Remove");
            }

        }

    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCENCD7Uzd2YK0IJsUPgFI1gMNiHHPAuRA&libraries=places"></script>


    <script>
        function initAutocomplete() {
            const input = document.getElementById("add_address_search");

            // Initialize Google Places Autocomplete
            const autocomplete = new google.maps.places.Autocomplete(input, {
                // types: ["geocode"], // only addresses
                componentRestrictions: { country: "in" } // restrict to India (optional)
            });

            // When the user selects an address
            autocomplete.addListener("place_changed", function () {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    alert("Please select an address from the suggestions.");
                    return;
                }
                let lat = place.geometry.location.lat();
                let lng = place.geometry.location.lng();
                // Set values to hidden inputs
                document.getElementById("address_lat").value = lat;
                document.getElementById("address_long").value = lng;
                console.log("Selected Address:", input.value);
                console.log("Lat:", lat, "Lng:", lng);
            });
        }

        // Initialize the autocomplete after page load
        google.maps.event.addDomListener(window, 'load', initAutocomplete);
    </script>

    <script>
        document.getElementById('pincode').addEventListener('input', function () {
            let pincode = this.value.replace(/\D/g, ''); // allow only numbers
            this.value = pincode;

            if (pincode.length === 6) {
                fetchPincodeDetails(pincode);
            }
        });

        function fetchPincodeDetails(pin) {
            fetch(`{{ url('getPincodeDetails') }}/${pin}`)
                .then(res => res.json())
                .then(data => {
                    if (data.city) {
                        document.getElementById('city').value = data.city;
                        document.getElementById('state').value = data.state;
                    } else {
                        alert("Invalid Pincode");
                        document.getElementById('city').value = "";
                        document.getElementById('state').value = "";
                    }
                })
                .catch(() => {
                    alert("Something went wrong");
                });
        }

    </script>


@endsection
