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

    <input type="hidden" name="selected_addressID" id="selected_addressID" value="{{$selectedAddress->id??''}}">

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
    </script>


@endsection
