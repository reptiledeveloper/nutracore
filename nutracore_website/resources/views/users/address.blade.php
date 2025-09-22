@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    ?>

    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Addresses
                </div>
            </div>
        </div>
        <div class="page-content pb-150">
            <div class="container">
                <div class="row">
                    <div class="list-group mb-4">
                        @foreach($user->addresses ?? [] as $address)
                            @php
                                $selected = ($address->id == $user->addressID) ? "selected" : "";
                            @endphp
                            <label class="list-group-item address-item d-flex justify-content-between align-items-start  mt-3 {{ $selected }}" data-id="{{ $address->id }}">
                                <div>
                                    <strong>{{ $address->address_type ?? "Others" }}</strong><br>
                                    <small>{{ $address->flat_no ?? '' }}, {{ $address->building_name ?? '' }}, {{ $address->landmark ?? '' }} - {{ $address->pincode ?? '' }}</small><br>
                                    <small>{{ $address->location ?? '' }}</small>
                                </div>
                                <input class="form-check-input d-none" type="radio" name="selected_address" value="{{ $address->id }}" {{ $selected ? 'checked' : '' }}>
                            </label>
                        @endforeach


                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">Add New Address</button>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Addresses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
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
                       location.reload();
                    },
                    error: function(xhr) {
                        alert("Error saving address.");
                    }
                });
            });
        });
    </script>
@endsection
