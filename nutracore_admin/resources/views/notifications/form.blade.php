@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $campaign_id = $campaign->id ?? '';
    $type = $campaign->type ?? '';
    $title = $campaign->title ?? '';
    $description = $campaign->description ?? '';
    $status = $campaign->status ?? 1;
    $image = $campaign->image ?? '';
    $user_type = $campaign->user_type ?? 'all';

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
                            <input type="hidden" id="id" value="{{ $campaign_id }}">

                            <div class="row">
                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName">Title</label>
                                    <input type="text" class="form-control" name="title"
                                           value="{{old('title',$title)}}">
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName">Description</label>
                                    <input type="description" class="form-control" name="description"
                                           value="{{old('description',$description)}}">
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName">Image</label>
                                    <input type="file" name="image" class="form-control"
                                           value="" placeholder="" accept="image/*">
                                    <?php if (!empty($image)){
                                        $image = \App\Helpers\CustomHelper::getImageUrl('notification', $image);
                                        ?>
                                    <div class="mt-3 mt-3">
                                        <a href="{{$image}}" target="_blank">
                                            <img src="{{$image}}" height="100px" width="200px">
                                        </a>
                                    </div>
                                    <?php } ?>

                                </div>
            <input type="hidden" name="user_type" value="user">
{{--                                <div class="form-group col-md-6 mt-3">--}}
{{--                                    <label for="userName">Target User Type</label>--}}
{{--                                    <select class="form-control" name="user_type">--}}

{{--                                        <option--}}
{{--                                            value="seller" <?php if ($user_type == 'seller') echo "selected" ?>>--}}
{{--                                            Seller--}}
{{--                                        </option>--}}
{{--                                        <option--}}
{{--                                            value="user" <?php if ($user_type == 'user') echo "selected" ?>>--}}
{{--                                            User--}}
{{--                                        </option>--}}

{{--                                    </select>--}}
{{--                                </div>--}}

{{--                                <div class="form-group col-md-6 mt-3">--}}
{{--                                    <label for="userName">Type</label>--}}
{{--                                    <select class="form-control" name="type">--}}
{{--                                        <option value="" selected>Select</option>--}}
{{--                                        <option value="offer" <?php if ($type == 'offer') echo "selected" ?>>Offer--}}
{{--                                        </option>--}}
{{--                                        <option value="promo" <?php if ($type == 'promo') echo "selected" ?>>--}}
{{--                                            Promo--}}
{{--                                        </option>--}}
{{--                                        <option value="basic" <?php if ($type == 'basic') echo "selected" ?>>--}}
{{--                                            Basic--}}
{{--                                        </option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}

                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName">Status<span
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
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#category_show').hide();
            $('#product_show').hide();
            var type = '{{$type}}';
            if (type == 'category') {
                $('#category_show').show();
            }
            if (type == 'product') {
                $('#product_show').show();
            }
        });

        function get_type_val(val) {
            $('#category_show').hide();
            $('#product_show').hide();
            if (val == 'category') {
                $('#category_show').show();
            }
            if (val == 'product') {
                $('#product_show').show();
            }
        }
    </script>

@endsection
