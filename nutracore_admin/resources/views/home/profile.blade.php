@extends('layouts.layout')
@section('content')
    <?php
    $user = Auth::guard('admin')->user();
    $name = $user->name ?? '';
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
    $role_id = $user->role_id ?? '';
    $role_name = \App\Helpers\CustomHelper::getRoleName($role_id);
    $image = \App\Helpers\CustomHelper::getImageUrl('admin', $user->image);
    ?>
    <style>
        #fileInput {
            display: none; /* Hide the file input */
        }
    </style>

    <div class="content ">
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row flex-column-reverse flex-md-row">
            <div class="col-md-8">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="mb-4">
                            <div class="d-flex flex-column flex-md-row text-center text-md-start mb-3">
                                <figure class="me-4 flex-shrink-0">
                                    <img width="100" class="rounded-pill" src="{{$image}}"
                                         alt="...">
                                </figure>
                                <div class="flex-fill">
                                    <h5 class="mb-3">{{$user->name??''}}</h5>
                                    <button class="btn btn-primary me-2" id="uploadButton">Change Avatar</button>
{{--                                    <button class="btn btn-outline-danger btn-icon" data-bs-toggle="tooltip"--}}
{{--                                            title="Remove Avatar">--}}
{{--                                        <i class="bi bi-trash me-0"></i>--}}
{{--                                    </button>--}}
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title mb-4">Basic Information</h6>
                                    <form action="" id="update_profile" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="image" id="fileInput"  accept="image/*"/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                           value="{{$user->name??''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" name="phone" class="form-control"
                                                           value="{{$user->phone??''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="text" class="form-control" name="email"
                                                           value="{{$user->email??''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" class="form-control" name="address"
                                                           value="{{$user->address??''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-primary" type="submit">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-4">Change Password</h6>
                                <form action="{{route('admin.change_password')}}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="mb-3">
                                            <label class="form-label">Old Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password Repeat</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary" type="submit">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card sticky-top mb-4 mb-md-0">
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column gap-2" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile"
                                   role="tab" aria-controls="profile" aria-selected="true">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab"
                                   aria-controls="password" aria-selected="false">
                                    <i class="bi bi-lock me-2"></i> Password
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <script>
        document.getElementById('uploadButton').addEventListener('click', function() {
            document.getElementById('fileInput').click(); // Trigger file input click
        });

        document.getElementById('fileInput').addEventListener('change', function(event) {
            const fileList = event.target.files;
            if (fileList.length > 0) {
                // alert('Selected file: ' + fileList[0].name);
                document.getElementById('update_profile').submit();
            }
        });
    </script>

@endsection
