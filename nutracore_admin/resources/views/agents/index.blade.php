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
                    <li class="breadcrumb-item active" aria-current="page">Delivery Agents</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Delivery Agents</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('delivery_agents.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>

                                <a href="{{ route('reports.delivery_agent', ['back_url' => $BackUrl]) }}"
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Seller Name</th>
                            <th>Status</th>

                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($agents)){
                        foreach ($agents as $cat) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('agents', $cat->image);
                            ?>
                        <tr>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt="" /></a></td>
                            <td>{{ $cat->name ?? '' }} </td>
                            <td>{{$cat->email??''}}</td>
                            <td>{{$cat->phone??''}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getVendorName($cat->vendor_id) }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($cat->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('delivery_agents.edit',$cat->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('delivery_agents.view',$cat->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>
                                            <a href="{{route('delivery_agents.delete',$cat->id.'?back_url='.$BackUrl)}}"
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

                    {{ $agents->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
