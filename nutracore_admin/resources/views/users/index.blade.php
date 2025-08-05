@extends('layouts.layout')
@section('content')
<style>
    .dropdown-menu.show{
        overflow: hidden !important;
    }
</style>
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
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>

        @include('layouts.filter',['search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Users</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('users.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                <a href="{{ route('reports.users', ['back_url' => $BackUrl]) }}"
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
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Wallet</th>
                            <th>Status</th>
                            <th>Join By</th>
                            <th>Join On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users)){
                        foreach ($users as $user) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('users', $user->image??'');
                            ?>
                        <tr>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt=""/></a>
                            </td>
                            <td>{{$user->name??''}}</td>
                            <td>{{$user->email??''}}</td>
                            <td>{{$user->phone??''}}</td>
                            <td>{{$user->wallet??0}} .00<br></td>
                            <td>{{\App\Helpers\CustomHelper::getStatusStr($user->status)}}</td>
                            <td>
                                @if($user->referral_userID)
                                    {{\App\Helpers\CustomHelper::getUserDetails($user->referral_userID??'')->name??''}}
                                @endif
                            </td>
                            <td>{{date('d M Y',strtotime($user->created_at))??''}}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('users.edit',$user->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('users.view',$user->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>
                                            <a href="{{route('users.delete',$user->id.'?back_url='.$BackUrl)}}"
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

                    {{ $users->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
