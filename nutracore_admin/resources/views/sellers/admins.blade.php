@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $attributes = \App\Helpers\CustomHelper::getAttributes();
    $roles = \App\Helpers\CustomHelper::getSellerRoles($seller->id);
    ?>
    @include('sellers.common',['seller'=>$seller])
    @include('snippets.errors')
    @include('snippets.flash')




    <div class="modal fade" id="addAdmin"
         tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Admin</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <form action="{{ route('admins.add', ['back_url' => $BackUrl]) }}" method="post">
                    @csrf
                    <input type="hidden" name="vendor_id" value="{{$seller->id}}">
                    <input type="hidden" name="back_url" value="{{$BackUrl}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{old('name')}}" placeholder="Name">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control"
                                       value="{{old('email')}}" placeholder="Email">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{old('phone')}}" placeholder="Phone">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Roles</label>
                                <select class="form-control " name="role_id">
                                    <option value="" selected>Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id??''}}">{{$role->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Password</label>
                                <input type="text" name="password" class="form-control"
                                       value="" placeholder="Password">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Status</label>
                                <select class="form-control " name="status">
                                    <option value="" selected>Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">InActive</option>

                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                                data-bs-dismiss="modal">Close
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="d-md-flex gap-4 align-items-center">
                <div class="d-none d-md-flex">All Admins</div>
                <div class="dropdown ms-auto">
                    <a data-bs-toggle="modal" data-bs-target="#addAdmin" class="btn btn-primary"><i
                            class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body pt-0">

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-custom table-lg mb-0" id="products">
                            <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Name</th>
                                <th>Role Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($admins)){
                                $i = 1;
                            foreach ($admins as $admin){
                                $role = \App\Helpers\CustomHelper::getSellerRoleName($admin->role_id);
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
                                                <a  data-bs-toggle="modal" data-bs-target="#editAdmin{{$admin->id}}"
                                                   class="dropdown-item">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>


                            <div class="modal fade" id="editAdmin{{$admin->id}}"
                                 tabindex="-1" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Update Admin</h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admins.add', ['back_url' => $BackUrl]) }}" method="post">
                                            @csrf
                                            <input type="hidden" name="vendor_id" value="{{$seller->id}}">
                                            <input type="hidden" name="back_url" value="{{$BackUrl}}">
                                            <input type="hidden" name="id" value="{{$admin->id}}">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{$admin->name??''}}" placeholder="Name">
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Email</label>
                                                        <input type="text" name="email" class="form-control"
                                                               value="{{$admin->email??''}}" placeholder="Email">
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Phone</label>
                                                        <input type="text" name="phone" class="form-control"
                                                               value="{{$admin->phone??''}}" placeholder="Phone">
                                                    </div>

                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Roles</label>
                                                        <select class="form-control " name="role_id">
                                                            <option value="" selected>Select Role</option>
                                                            @foreach($roles as $role)
                                                                <option value="{{$role->id??''}}" {{$admin->role_id == $role->id?"selected":""}}>{{$role->name??''}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Password</label>
                                                        <input type="text" name="password" class="form-control"
                                                               value="" placeholder="Password">
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <label for="userName" class="form-label">Status</label>
                                                        <select class="form-control " name="status">
                                                            <option value="" selected>Select Status</option>
                                                            <option value="1" {{$admin->status==1?"selected":""}}>Active</option>
                                                            <option value="0" {{$admin->status==0?"selected":""}}>InActive</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <?php }
                            } ?>

                            </tbody>


                        </table>
                        {{ $admins->appends(request()->input())->links('pagination') }}

                    </div>
                </div>
            </div>
        </div>
    </div>




















    </div>

@endsection
