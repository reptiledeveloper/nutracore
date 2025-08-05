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
                    <li class="breadcrumb-item active" aria-current="page">Collections</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Collections</div>

                            <div class="dropdown ms-auto">
                             
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($collections)){
                        foreach ($collections as $collection) {
                            $product_ids_string = $collection->product_ids??''; // from DB column
                            $product_ids_array = explode(',', $product_ids_string);
                            $product_names = \App\Models\Products::whereIn('id', $product_ids_array)->pluck('name')->toArray();
                            ?>
                        <tr>
                            <td>{{ $collection->type ?? '' }}</td>
                       <td>{!! implode('<br>', $product_names) !!}</td>

                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($collection->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('collections.edit',$collection->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $collections->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
