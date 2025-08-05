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
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show','vendor_show'=>'vendor_show','categories_show'=>'categories_show','subcategory_show'=>'subcategory_show'])

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Products</div>
                            <div class="dropdown ms-auto">
                                <a href="{{ route('products.import', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary" title="Import Product"> <i class="bi bi-file-text"></i></a>
                                <a href="{{ route('products.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary" title="Add Product"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>SubCategory</th>
                            <th>Image</th>
                            <th>Vendor Name</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($products)){
                        foreach ($products as $product) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image);
                            ?>
                        <tr>
                            <td class="text-wrap">{{ $product->name ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->category_id??'') ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->subcategory_id??'') ?? '' }}</td>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt=""/></a>
                            </td>
                            <td>{{ \App\Helpers\CustomHelper::getVendorName($product->vendor_id) }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($product->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('products.edit',$product->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <!-- <a href="{{route('products.view',$product->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a> -->
                                            <a href="{{route('products.delete',$product->id.'?back_url='.$BackUrl)}}"
                                               onclick="return confirm('Are you Want To Delete?')"
                                               class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $products->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
