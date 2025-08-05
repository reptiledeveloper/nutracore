@extends('home.layout')
@section('content')
    <?php 

use App\Helpers\CustomHelper;
$user = Auth::user();

?>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='index.html' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Shop
                    <span></span> Cart
                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50" id="cart_html">
           
           
        </div>
    </main>



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
                            <label class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $address->label }}</strong><br>
                                    <small>{{ $address->line1 }}, {{ $address->city }}, {{ $address->state }} -
                                        {{ $address->pincode }}</small>
                                </div>
                                <input class="form-check-input mt-1" type="radio" name="selected_address"
                                    value="{{ $address->id }}">
                            </label>
                        @endforeach
                    </div>

                    <!-- Add New Address Form -->
                    <h6>Add New Address</h6>
                    <form id="addAddressForm" method="POST" action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <input type="text" name="label" class="form-control"
                                    placeholder="Address Label (e.g. Home)">
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="line1" class="form-control" placeholder="Address Line">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="city" class="form-control" placeholder="City">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="state" class="form-control" placeholder="State">
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
@endsection