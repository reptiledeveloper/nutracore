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
                    <li class="breadcrumb-item active" aria-current="page">Brands</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Roles</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('brands.add', ['back_url' => $BackUrl]) }}" class="btn btn-primary"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Vendor Name</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($brands as $brand)
                            @php
                                $image = \App\Helpers\CustomHelper::getImageUrl('brands',$brand->brand_img);
                            @endphp
                            <tr>
                                <td>{{ $brand->id ?? '' }}</td>
                                <td>{{ $brand->brand_name ?? '' }}</td>
                                <td><a href="{{$image}}" target="_blank"><img height="100px" width="200px"
                                                                              src="{{$image}}"
                                                                              alt=""/></a>
                                </td>
                                <td>{{ \App\Helpers\CustomHelper::getVendorName($brand->vendor_id) }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getStatusStr($brand->status) }}</td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                               aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{route('brands.edit',$brand->id.'?back_url='.$BackUrl)}}"
                                                   class="dropdown-item">Edit</a>
                                                <a href="{{route('brands.delete',$brand->id.'?back_url='.$BackUrl)}}"
                                                   onclick="return confirm('Are you Want To Delete?')"
                                                   class="dropdown-item">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $brands->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
