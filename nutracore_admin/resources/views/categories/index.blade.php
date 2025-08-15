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
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Categories ({{$categories->total()}})</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('categories.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>

                                <a href="{{ route('reports.categories', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            {{--                            <th>Vendor Name</th>--}}

                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($categories)){
                        foreach ($categories as $cat) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('categories', $cat->image);
                            $subcategory_count = \App\Helpers\CustomHelper::getSubCategory($cat->id);
                            ?>
                        <tr>
                            <td>{{$cat->id??''}}</td>
                            <td>{{ $cat->name ?? '' }} ({{count($subcategory_count)??''}})</td>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt=""/></a>
                            </td>
                            {{--                            <td>{{ \App\Helpers\CustomHelper::getVendorName($cat->vendor_id) }}</td>--}}
                            <td>{{$cat->priority??''}}</td>

                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($cat->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('categories.edit',$cat->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('categories.delete',$cat->id.'?back_url='.$BackUrl)}}"
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

                    {{ $categories->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
