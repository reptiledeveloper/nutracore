@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $attributes = \App\Helpers\CustomHelper::getAttributes();
    ?>
    @include('sellers.common',['seller'=>$seller])

    @include('layouts.filter',['search_show'=>'search_show','categories_show'=>'categories_show','subcategory_show'=>'subcategory_show'])

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
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>SubCategory</th>
                                    <th>Image</th>
                                    <th>Varients</th>
                                    <th>Status</th>
{{--                                    <th class="text-end">Actions</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($products)){
                                foreach ($products as $product) {
                                    $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image);
                                    $varients = \App\Helpers\CustomHelper::getVendorProductVarients($seller->id, $product->id);
                                    ?>
                                <tr>
                                    <td class="text-wrap">{{ $product->name ?? '' }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->category_id??'') ?? '' }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->subcategory_id??'') ?? '' }}</td>

                                    <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px"
                                                                                  src="{{$image}}"
                                                                                  alt=""/></a>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @foreach($varients as $varient)
                                                <div class="col-md-12">
                                                    <div class="card mt-3 w-100 border-primary mb-3">
                                                        <div class="card-header text-end">
                                                            <button class="btn btn-sm btn-primary" type="button"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#updateVarientModal{{$varient->id}}">
                                                                <i
                                                                    class="fa fa-edit"></i></button>
                                                        </div>
                                                        <div class="card-body " style="padding: 10px">
                                                            <span>Unit : {{$varient->unit??''}}</span><br>
                                                            <span>Unit Value : {{$varient->unit_value??''}}</span><br>
                                                            <span>MRP : ₹ {{$varient->mrp??''}}</span><br>
                                                            <span>Selling Price : ₹ {{$varient->selling_price??''}}</span><br>
                                                            <span>Subscription Price : ₹ {{$varient->subscription_price??''}}</span><br>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="updateVarientModal{{$varient->id}}"
                                                     tabindex="-1" aria-labelledby="exampleModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Update
                                                                    Varient</h5>
                                                                <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                            </div>
                                                            <form action="" method="post">
                                                                @csrf
                                                                <input type="hidden" name="varient_id" value="{{$varient->id}}">
                                                                <input type="hidden" name="seller_id" value="{{$seller->id}}">
                                                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12 mt-2">
                                                                            <label class="form-label">Unit</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="unit"
                                                                                   value="{{$varient->unit??''}}"
                                                                                   placeholder="Enter Unit">
                                                                        </div>
                                                                        <div class="col-md-12 mt-2">
                                                                            <label class="form-label">Unit Value</label>
                                                                            <select class="form-control" name="unit_value">
                                                                                    <?php if (!empty($attributes)){
                                                                                foreach ($attributes as $att){
                                                                                    ?>
                                                                                <option
                                                                                    value="{{ $att->name }}" <?php if ($att->name == $varient->unit_value) echo "selected" ?>>
                                                                                    {{ $att->name ?? '' }}</option>
                                                                                <?php }
                                                                                } ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-12 mt-2">
                                                                            <label class="form-label">MRP</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="mrp" value="{{$varient->mrp??''}}"
                                                                                   placeholder="Enter MRP">
                                                                        </div>
                                                                        <div class="col-md-12 mt-2">
                                                                            <label class="form-label">Selling Price</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="selling_price"
                                                                                   value="{{$varient->selling_price??''}}"
                                                                                   placeholder="Enter Selling Price">
                                                                        </div>
                                                                        <div class="col-md-12 mt-2">
                                                                            <label class="form-label">Subscription
                                                                                Price</label>
                                                                            <input type="text" class="form-control"
                                                                                   name="subscription_price"
                                                                                   value="{{$varient->subscription_price??''}}"
                                                                                   placeholder="Enter Subscription Price">
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

                                        </div>

                                    </td>
                                    <td>{!!  \App\Helpers\CustomHelper::getStatusStr($product->status)== 'Active' ? '<span class="badge bg-success">Active</span>':'<span class="badge bg-danger">InActive</span>' !!}</td>
{{--                                    <td class="text-end">--}}
{{--                                        <div class="d-flex">--}}
{{--                                            <div class="dropdown ms-auto">--}}
{{--                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"--}}
{{--                                                   aria-haspopup="true" aria-expanded="false">--}}
{{--                                                    <i class="bi bi-three-dots"></i>--}}
{{--                                                </a>--}}
{{--                                                <div class="dropdown-menu dropdown-menu-end">--}}
{{--                                                    <a href="" class="dropdown-item">Edit</a>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
                                </tr>
                                <?php }
                                } ?>

                                </tbody>
                            </table>
                            {{ $products->appends(request()->input())->links('pagination') }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>




















    </div>

@endsection
