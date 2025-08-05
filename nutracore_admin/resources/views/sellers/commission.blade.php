@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    ?>
    @include('sellers.common',['seller'=>$seller])
    @include('snippets.errors')
    @include('snippets.flash')
    <div class="card mt-3">
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <form action="" method="post">
                            @csrf
                            <input type="hidden" name="seller_id" value="{{$seller->id}}">
                            <table class="table table-custom table-lg mb-0" id="products">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Commission Type</th>
                                    <th>Commission</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php if (!empty($categories)){
                                foreach ($categories as $cat) {
                                    $image = \App\Helpers\CustomHelper::getImageUrl('categories', $cat->image);
                                    $subcategory_count = \App\Helpers\CustomHelper::getSubCategory($cat->id);
                                    $getCommission = \App\Helpers\CustomHelper::getCategoryCommission($seller->id, $cat->id);
                                    ?>
                                <input type="hidden" name="category_id[]" value="{{$cat->id}}">

                                <tr>
                                    <td>{{ $cat->name ?? '' }} ({{count($subcategory_count)??''}})</td>
                                    <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px"
                                                                                  src="{{$image}}"
                                                                                  alt=""/></a>
                                    </td>
                                    <td>
                                        <select class="form-control" name="commisssion_type[]">
                                            <option value="" selected>Select Type</option>
                                            <option
                                                value="fixed" {{!empty($getCommission) && $getCommission->commisssion_type == 'fixed' ? "selected":""}}>
                                                Fixed
                                            </option>
                                            <option
                                                value="percentage" {{!empty($getCommission) && $getCommission->commisssion_type == 'percentage' ? "selected":""}}>
                                                Percentage
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="commisssion[]" class="form-control"
                                               placeholder="Enter Commission Percent"
                                               value="{{!empty($getCommission) && $getCommission->commisssion ? $getCommission->commisssion : ""}}">
                                    </td>
                                    <td>{{ \App\Helpers\CustomHelper::getStatusStr($cat->status) }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i></button>
{{--                                        <a class="btn btn-danger" onclick=""><i class="fa fa-times"></i></a>--}}
                                    </td>
                                </tr>
                                <?php }
                                } ?>

                                </tbody>

                            </table>

                            <div class="mt-3">
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>




















    </div>

@endsection
