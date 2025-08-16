@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
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
                    <li class="breadcrumb-item active" aria-current="page">Abandoned Cart</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Abandoned Cart</div>
                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Products in Cart</th>
                            <th>Total Amount</th>
                            <th>Last Added At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($abandonedCarts as $cart)
                            <tr>
                                <td>{{ $cart->user_name }}</td>
                                <td>{{ $cart->user_email }}</td>
                                <td>{{ $cart->product_list }}</td>
                                <td>₹{{ number_format($cart->total_amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($cart->last_added_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <a href=""
                                       class="btn btn-sm btn-primary">
                                        Purchase
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No abandoned carts found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $abandonedCarts->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
