@extends('layouts.layout')
@section('content')
    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $company_id = $_GET['company_id'] ?? '';
    $role_id = $_GET['role_id'] ?? '';
    $modules = config('modules.allowed');
    $allowedwithval = config('modules.allowedwithval');
    $role_data = [];

    $role_data = \App\Models\Roles::where('status', 1)->where('is_delete', 0)->get();

    ?>
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>

    <!-- main-content -->
    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Permission</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Permission</div>
                        </div>

                        <form class="mt-3" accept="" action="" method="">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <select class="form-control select2 " name="role_id" id="role_id">
                                        <option value="" selected="" disabled="">Select Role
                                        </option>
                                        <?php if (!empty($role_data)){
                                        foreach ($role_data as $role){
                                            ?>
                                        <option value="{{ $role->id }}" <?php if ($role_id == $role->id) {
                                            echo 'selected';
                                        } ?>>
                                            {{ $role->name ?? '' }}</option>

                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-md-6" style="margin-top: 25px">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a class="btn btn-danger" href="{{route('permission.index')}}">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <?php if (!empty($role_id)){ ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-custom table-lg mb-0">
                                <thead>
                                <tr>
                                    <th>Modules</th>
                                    <th>List</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>View Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($modules)){
                                foreach ($modules as $key => $value) {
                                    $title = '';
                                    if (!empty($allowedwithval)) {
                                        foreach ($allowedwithval as $key1 => $value1) {
                                            if ($key1 == $value) {
                                                $title = $value1;
                                            }
                                        }
                                    }
                                    $add = '';
                                    $edit = '';
                                    $list = '';
                                    $delete = '';
                                    $view = '';

                                    $exist = \App\Models\Permission::where('role_id', $role_id)->where('section', $value)->first();
                                    if (!empty($exist)) {
                                        if ($exist->add == 1) {
                                            $add = 'checked';
                                        }
                                        if ($exist->list == 1) {
                                            $list = 'checked';
                                        }
                                        if ($exist->edit == 1) {
                                            $edit = 'checked';
                                        }
                                        if ($exist->delete == 1) {
                                            $delete = 'checked';
                                        }
                                        if ($exist->view == 1) {
                                            $view = 'checked';
                                        }


                                    }
                                    ?>
                                <tr>
                                    <td>{{ $title ?? '' }}</td>
                                    <td class="text-start">
                                        <div class="mb-3">
                                            <label class="switch">
                                                <input type="checkbox" {{ $list }}
                                                onclick="update_permission('{{ $value }}','{{ $role_id }}','list',this)"
                                                       id="checkboxlist{{ $value }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </td>

                                    <td class="text-start">
                                        <div class="mb-3">
                                            <label class="switch">
                                                <input type="checkbox" {{ $add }}
                                                onclick="update_permission('{{ $value }}','{{ $role_id }}','add',this)"
                                                       id="checkboxadd{{ $value }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </td>


                                    <td class="text-start">
                                        <div class="mb-3">
                                            <label class="switch">
                                                <input type="checkbox" {{ $edit }}
                                                onclick="update_permission('{{ $value }}','{{ $role_id }}','edit',this)"
                                                       id="checkboxedit{{ $value }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </td>


                                    <td class="text-start">
                                        <div class="mb-3">
                                            <label class="switch">
                                                <input type="checkbox" {{ $delete }}
                                                onclick="update_permission('{{ $value }}','{{ $role_id }}','delete',this)"
                                                       id="checkboxdelete{{ $value }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </td>

                                    <td class="text-start">
                                        <div class="mb-3">
                                            <label class="switch">
                                                <input type="checkbox" {{ $view }}
                                                onclick="update_permission('{{ $value }}','{{ $role_id }}','view',this)"
                                                       id="checkboxview{{ $value }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </td>

                                </tr>
                                <?php }
                                } ?>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>


            </div>
        </div>
    </div>





    <script type="text/javascript">
        function get_roles(company_id) {
            var _token = '{{ csrf_token() }}';
            $.ajax({
                url: "{{ route('permission.get_roles') }}",
                type: "POST",
                data: {
                    company_id: company_id
                },
                dataType: "HTML",
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                cache: false,
                success: function (resp) {
                    $('#role_id').html(resp);
                }
            });
        }
    </script>

    <script type="text/javascript">
        function update_permission(key, role_id, section, permission) {
            if (permission.checked) {
                permission = 1;
            } else {
                permission = 0;
            }

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route('permission.update_permission') }}",
                type: "POST",
                data: {
                    key: key,

                    section: section,
                    permission: permission,
                    role_id: role_id
                },
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                cache: false,
                success: function (resp) {
                }
            });
        }
    </script>
@endsection
