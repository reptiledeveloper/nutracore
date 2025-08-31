@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $current_url = url()->current();
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
                    <li class="breadcrumb-item active" aria-current="page">Import Products</li>
                </ol>
            </nav>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <form action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Import File</label>
                                    <input type="file" class="form-control" placeholder="Search..." name="file"
                                           value="">
                                </div>
                                <div class="col-md-4" style="margin-top: 27px">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <a href="{{$current_url}}" class="btn btn-danger">Reset</a>
                                    <a href="{{route('products.sample')}}" class="btn btn-danger"><i
                                            class="fa fa-download"></i> Sample</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function submit_form() {
            $('#product_save_form').submit();
        }
    </script>
@endsection
