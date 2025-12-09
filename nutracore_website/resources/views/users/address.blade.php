@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    ?>
    <style>

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

                                <a type="button" class="btn btn-primary deliver-btn address-list w-100">Deliver Here</a>
                            </div>
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
