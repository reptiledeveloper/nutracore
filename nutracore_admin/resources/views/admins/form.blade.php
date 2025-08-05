@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $admin_id = isset($admin->id) ? $admin->id : '';
    $name = isset($admin->name) ? $admin->name : '';
    $status = isset($admin->status) ? $admin->status : '';
    $parent_id = isset($admin->parent_id) ? $admin->parent_id : 0;
    $company_id = isset($admin->company_id) ? $admin->company_id : 0;
    $role_id = isset($admin->role_id) ? $admin->role_id : 0;

    $email = isset($admin->email) ? $admin->email : '';
    $phone = isset($admin->phone) ? $admin->phone : '';
    $username = isset($admin->username) ? $admin->username : '';
    $password = isset($admin->password) ? $admin->password : '';
    $roles = \App\Helpers\CustomHelper::getRoles();
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
                    <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                </ol>
            </nav>
        </div>

        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


               <div class="card mt-3">
                   <div class="card-body pt-0">
                       <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                             enctype="multipart/form-data" role="form">
                           {{ csrf_field() }}
                           <input type="hidden" id="id" value="{{ $admin_id }}">
                           <div class="row">
                               <div class="col-md-6 mt-3">
                                   <label for="userName" class="form-label">Name</label>
                                   <input type="text" name="name" class="form-control"
                                          value="{{old('name',$name)}}" placeholder="Name">
                               </div>
                               <div class="col-md-6 mt-3">
                                   <label for="userName" class="form-label">Email</label>
                                   <input type="text" name="email" class="form-control"
                                          value="{{old('email',$email)}}" placeholder="Email">
                               </div>
                               <div class="col-md-6 mt-3">
                                   <label for="userName" class="form-label">Phone</label>
                                   <input type="text" name="phone" class="form-control"
                                          value="{{old('phone',$phone)}}" placeholder="Phone">
                               </div>

                               <div class="col-md-6 mt-3">
                                   <label for="userName" class="form-label">Roles</label>
                                   <select class="form-control select2" name="role_id">
                                       <option value="" selected>Select Role</option>
                                       @foreach($roles as $role)
                                           <option value="{{$role->id??''}}" <?php if($role->id == $role_id) echo "selected"?>>{{$role->name??''}}</option>
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

                                   <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                       <input type="radio" class="form-check-input" name="status"
                                              value="1"
                                              <?php echo $status == '1' ? 'checked' : ''; ?> checked>
                                       <label class="form-check-label"
                                              for="customRadioBox1">Active</label>
                                   </div>

                                   <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                       <input type="radio" class="form-check-input" name="status"
                                              value="0" <?php echo strlen($status) > 0 && $status == '0' ? 'checked' : ''; ?>>
                                       <label class="form-check-label"
                                              for="customRadioBox1">InActive</label>
                                   </div>
                               </div>
                           </div>

                           <div class="mb-0 mt-3 justify-content-end">
                               <div>
                                   <button type="submit" class="btn btn-primary">Save</button>
                               </div>
                           </div>
                       </form>
                   </div>
               </div>


            </div>
        </div>
    </div>

@endsection
