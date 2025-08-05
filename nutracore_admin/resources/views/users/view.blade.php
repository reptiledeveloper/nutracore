@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $users_id = $users->id ?? '';
    $name = $users->name ?? '';
    $email = $users->email ?? '';
    $phone = $users->phone ?? '';
    $status = $users->status ?? 1;

    $image = \App\Helpers\CustomHelper::getImageUrl('users', $users->image??'');
    ?>
    @include('users.common',['users'=>$users])

    @include('snippets.errors')
    @include('snippets.flash')

    <div class="card mt-3">
        <div class="card-body pt-0">
            <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                  enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <input type="hidden" id="id" value="{{ $users_id }}">
                <div class="row">

                    <div class="form-group col-md-6 mt-3">
                        <label for="inputEmail4" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $name) }}">
                        @include('snippets.errors_first', ['param' => 'name'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="inputEmail4" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email', $email) }}">
                        @include('snippets.errors_first', ['param' => 'email'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="inputEmail4" class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $phone) }}">
                        @include('snippets.errors_first', ['param' => 'phone'])
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="validationCustom01" class="form-label">Image</label>
                        <input type="file" class="form-control" placeholder="Name" name="image"
                               value="">
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label for="userName" class="form-label">Status<span
                                class="text-danger">*</span></label>

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

                <div class="form-group mb-0 mt-3 justify-content-end">
                    <div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    </div>

@endsection
