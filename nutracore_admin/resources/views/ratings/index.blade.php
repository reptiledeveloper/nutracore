@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

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
                    <li class="breadcrumb-item active" aria-current="page">Ratings</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Ratings</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>User</th>
                            <th>OrderID</th>
                            <th>Items</th>
                            <th>Rating</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($ratings)){
                            $i = 1;
                        foreach ($ratings as $rating) {
                            $user = \App\Helpers\CustomHelper::getUserDetails($rating->user_id);
                            $product = [];
                            if (!empty($rating->item_id)) {
                                $order_items = \App\Models\OrderItems::where('id', $rating->item_id)->first();
                                $product = \App\Models\Products::where('id', $order_items->product_id)->first();
                            }

                            ?>
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$user->name??''}} <br>{{$user->phone??''}} </td>
                            <td class="text-wrap"><a
                                    href="{{route('orders.view',$rating->order_id.'?back_url='.$BackUrl)}}">#{{$rating->order_id??''}}</a>
                            </td>
                            <td>
                                {{$product->name??''}}
                            </td>
                            <td>{{$rating->rating??''}}</td>
                            <td>{{$rating->remarks??''}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($rating->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a data-bs-toggle="modal"
                                               data-bs-target="#updateRating{{$rating->id}}"
                                               class="dropdown-item">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>


                        <div class="modal fade" id="updateRating{{$rating->id}}" tabindex="-1"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('ratings.update_status',['id'=>$rating->id]) }}"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$rating->id}}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control" name="status">
                                                        <option value="1" {{$rating->status == 1 ?"selected":""}}>
                                                            Active
                                                        </option>
                                                        <option value="0" {{$rating->status == 0 ?"selected":""}}>InActive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close
                                            </button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $ratings->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
