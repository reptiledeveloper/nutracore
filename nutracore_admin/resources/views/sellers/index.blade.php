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
                    <li class="breadcrumb-item active" aria-current="page">Sellers</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Sellers</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('sellers.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                <a href="{{ route('reports.sellers', ['back_url' => $BackUrl]) }}"
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
                            <th>Name</th>
                            <th>Store Name</th>
                            <th>Email</th>
                            <th>Mobile No</th>
                            <th>Logo</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($sellers)){
                        foreach ($sellers as $seller) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('sellers', $seller->image);
                            ?>
                        <tr>
                            <td>{{ $seller->user_name ?? '' }}</td>
                            <td>{{ $seller->name ?? '' }}</td>
                            <td>{{ $seller->user_email ?? '' }}</td>
                            <td>{{ $seller->user_phone ?? '' }}</td>
                            <td>
                                <a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt=""/></a>
                            </td>
                            <td>
                                {{\App\Helpers\CustomHelper::getStatusStr($seller->status)}}
                            </td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('sellers.edit',$seller->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('sellers.view',$seller->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>
                                            <a href="{{route('sellers.delete',$seller->id.'?back_url='.$BackUrl)}}"
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

                    {{ $sellers->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
