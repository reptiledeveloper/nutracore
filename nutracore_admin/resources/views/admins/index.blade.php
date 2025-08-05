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
                    <li class="breadcrumb-item active" aria-current="page">Admins</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Admins</div>
                            <div class="dropdown ms-auto">
                                <a href="{{ route('admins.add', ['back_url' => $BackUrl]) }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role Name</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>

                        <?php
                        if (!empty($admins)){
                            $i = 1;
                        foreach ($admins as $admin){
                            $role = \App\Helpers\CustomHelper::getRoleName($admin->role_id);
                            ?>
                        <tr>
                            <td>
                                {{$i++}}
                            </td>
                            <td>
                                {{ $admin->name ?? '' }}
                            </td>
                            <td>{{$role??''}}</td>
                            <td>
                                {{ $admin->email ?? '' }}
                            </td>

                            <td>{!! \App\Helpers\CustomHelper::getStatusHTML($admin->status) !!}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('admins.edit',$admin->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('admins.delete',$admin->id.'?back_url='.$BackUrl)}}"
                                               onclick="return confirm('Are you Want To Delete?')"
                                               class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        } ?>
                    </table>

                    {{ $admins->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
