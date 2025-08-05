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
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>SubCategory</th>
                                <th>Image</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($abandoned_carts)) {
        foreach ($abandoned_carts as $abandoned_cart) {
            $product = \App\Models\Products::find($abandoned_cart->product_id);
            $user = \App\Models\User::find($abandoned_cart->user_id);
            $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image);
                                ?>
                            <tr>
                                <td class="text-wrap">{{ $user->name ?? '' }} <br>{{ $user->phone ?? '' }}</td>
                                <td class="text-wrap">{{ $product->name ?? '' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->category_id ?? '') ?? '' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->subcategory_id ?? '') ?? '' }}
                                </td>
                                <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                            alt="" /></a>
                                </td>
                                <td>{{ \App\Helpers\CustomHelper::getStatusStr($product->status) }}</td>
                            </tr>
                            <?php    }
    } ?>

                        </tbody>
                    </table>

                    {{ $abandoned_carts->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection