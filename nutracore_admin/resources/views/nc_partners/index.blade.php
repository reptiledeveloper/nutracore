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
                    <li class="breadcrumb-item active" aria-current="page">NC Partners</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All NC Partners</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('nc_partners.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>

                            <th>Full Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Role</th>
                            <th>Brand Name</th>
                            <th>Active Clients</th>
                            <th>Coupon Code</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>

                        </tr>
                        </thead>
                        <tbody>
                        @if (!empty($nc_partners))
                            @foreach ($nc_partners as $p)
                                <tr>
                                    <td>{{ $p->full_name ??'' }}</td>
                                    <td>{{ $p->mobile_number ??'' }}</td>
                                    <td>{{ $p->email ??'' }}</td>
                                    <td>{{ $p->city ??'' }}</td>
                                    <td>{{ $p->role ??'' }}</td>
                                    <td>{{ $p->brand_name ??'' }}</td>
                                    <td>{{ $p->active_clients ??'' }}</td>
                                    <td>{{ $p->coupon_code ??'' }}</td>

                                    <td>
                                        @if($p->status == 'Pending Review')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($p->status == 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{route('nc_partners.edit',$p->id.'?back_url='.$BackUrl)}}"
                                                       class="dropdown-item">Edit</a>
                                                    <a href="{{route('nc_partners.view',$p->id.'?back_url='.$BackUrl)}}"
                                                       class="dropdown-item">View</a>
{{--                                                    <a href="{{route('nc_partners.delete',$p->id.'?back_url='.$BackUrl)}}"--}}
{{--                                                       onclick="return confirm('Are you Want To Delete?')"--}}
{{--                                                       class="dropdown-item">Delete</a>--}}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    {{ $nc_partners->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
