@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $attributes = \App\Helpers\CustomHelper::getAttributes();

    ?>
    @include('sellers.common',['seller'=>$seller])
    @include('snippets.errors')
    @include('snippets.flash')



    <div class="modal fade" id="addRole"
         tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Role</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <form action="{{ route('sellers.add_role', ['back_url' => $BackUrl]) }}" method="post">
                    @csrf
                    <input type="hidden" name="vendor_id" value="{{$seller->id}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Role Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{old('name')}}" placeholder="Name">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="userName" class="form-label">Status</label>
                                <select class="form-control " name="status" required>
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
                <div class="d-none d-md-flex">All Roles</div>
                <div class="dropdown ms-auto">
                    <a data-bs-toggle="modal" data-bs-target="#addRole" class="btn btn-primary"><i
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
                                <th>Sl No</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$role->name??''}}</td>
                                    <td>
                                        {!! getStatusHtml($role->status) !!}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a data-bs-toggle="modal" data-bs-target="#editRole{{$role->id}}"
                                                       class="dropdown-item">Edit</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>





                                <div class="modal fade" id="editRole{{$role->id}}"
                                     tabindex="-1" aria-labelledby="exampleModalLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Update Role</h5>
                                                <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sellers.add_role', ['back_url' => $BackUrl]) }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$role->id}}">
                                                <input type="hidden" name="vendor_id" value="{{$seller->id}}">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mt-3">
                                                            <label for="userName" class="form-label">Role Name</label>
                                                            <input type="text" name="name" class="form-control"
                                                                   value="{{old('name',$role->name??'')}}" placeholder="Name">
                                                        </div>

                                                        <div class="col-md-6 mt-3">
                                                            <label for="userName" class="form-label">Status</label>
                                                            <select class="form-control " name="status" required>
                                                                <option value="" selected>Select Status</option>
                                                                <option value="1" {{$role->status==1?"selected":""}}>Active</option>
                                                                <option value="0" {{$role->status==0?"selected":""}}>InActive</option>
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
                            @endforeach

                            </tbody>
                        </table>

                        {{ $roles->appends(request()->input())->links('pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>




















    </div>

@endsection
