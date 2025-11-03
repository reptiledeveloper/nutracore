@extends('layouts.layout')
@section('content')
    <style>
        .dropdown-menu.show {
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
                    <li class="breadcrumb-item active" aria-current="page">Consultation Enquiry</li>
                </ol>
            </nav>
        </div>



        @include('layouts.filter',['search_show'=>'search_show','register_by_show'=>'register_by_show','is_ban_show'=>'is_ban_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Consultation Enquiry</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('reports.consultation', ['back_url' => $BackUrl]) }}"
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
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Health Profile</th>
                            <th>Daily Activity</th>
                            <th>Food</th>
                            <th>Lead Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users)){
                        foreach ($users as $user) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('users', $user->image ?? '');
                            $customer_subs_data = \App\Helpers\CustomHelper::getUserSubsData($user);
                            ?>
                        <tr>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt=""/></a>
                                <br>
                                Name : {{$user->name??''}} <br>
                                Email : {{$user->email??''}} <br>
                                Phone : {{$user->phone??''}} <br>
                            </td>
                            <td>{{($user->dob)?date('Y-m-d',strtotime($user->dob)):''}}</td>
                            <td>{{$user->gender??''}}</td>
                            <td>{{$user->height??''}}</td>
                            <td>{{$user->weight??''}}</td>
                            <td>{{$user->health_profile??''}}</td>
                            <td>{{$user->activity??''}}</td>
                            <td>{{$user->food_choice??''}}</td>
                            <td>{{$user->lead_status??''}}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a data-bs-toggle="modal" data-bs-target="#updateUserModal{{$user->id}}"
                                               class="dropdown-item">Update</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>




                        <div class="modal fade" id="updateUserModal{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('consultation.update_user') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{$user->id}}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control" name="lead_status">
                                                        <option value="" selected>Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
