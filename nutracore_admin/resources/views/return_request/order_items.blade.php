@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    ?>
    <style>
        .bg-primary {
            background-color: #ff6e40 !important;
        }
    </style>
    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Return Orders Items</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Return Orders Items</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="orders">
                        <thead>
                        <tr>
                            <th>OrderID</th>
                            <th>Seller Name</th>
                            <th>Item</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Total amount</th>
                            <th>Payment</th>
                            <th>Return Status</th>
                            <th>Return Remarks</th>
                            <th>Date</th>

                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($orders)){
                        foreach ($orders as $order) {
                            $order_data = \App\Models\Order::where('id',$order->order_id)->first();
                            $return_images = $order->return_images ?? '';
                            $return_images = explode(",", $return_images);
                            $product = \App\Models\Products::where('id', $order->product_id)->first();
                            ?>
                        <tr>
                            <td><a target="_blank"
                                   href="{{route('orders.view',$order->order_id.'?back_url='.$BackUrl)}}"># {{ $order->order_id ?? '' }}</a>
                            </td>

                            <td>{{\App\Helpers\CustomHelper::getVendorName($order_data->vendor_id??'')}}</td>
                            <td class="text-wrap">
                                {{$product->name??''}}
                            </td>
                            <td>
                                <strong>{{$order_data->customer_name??''}}</strong><br>
                                {{$order_data->contact_no??''}}
                            </td>
                            <td class="text-wrap">{{$order_data->house_no??''}} {{$order_data->land_mark??''}} {{$order_data->apartment??''}} {{$order_data->location??''}}</td>
                            <td>₹ {{$order->net_price??''}}</td>
                            <td>{{$order_data->payment_method??''}}</td>
                            <td>{!! \App\Helpers\CustomHelper::getReturnOrderItemStatus($order->id) !!}</td>
                            <td>{{$order->admin_remarks??''}}</td>
                            <td>{{ date('d M Y h:i A',strtotime($order->created_at)) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a data-bs-toggle="modal"
                                               data-bs-target="#updateReturnOrder{{$order->id}}"
                                               class="dropdown-item">Edit</a>
                                            <a target="_blank"
                                               href="{{route('orders.view',$order->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="updateReturnOrder{{$order->id}}" tabindex="-1"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update Return Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('return_request.update_item_status',['id'=>$order->id]) }}"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$order->id}}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Return Reasons
                                                        : {{$order->return_reasons??''}}</label>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Return Remarks
                                                        : {{$order->return_remarks??''}}</label>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Return Images : </label>

                                                    @foreach($return_images as $img)
                                                        <div class="col-md-2">
                                                            <img
                                                                src="{{\App\Helpers\CustomHelper::getImageUrl('return_order',$img)}}"
                                                                height="50px" width="50px">
                                                        </div>
                                                    @endforeach
                                                </div>


                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Return Status</label>
                                                    <select class="form-control" name="return_status">
                                                        <option
                                                            value="pending" {{$order->return_status == 'pending' ?"selected":""}}>
                                                            Pending
                                                        </option>
                                                        <option
                                                            value="approved" {{$order->return_status == 'approved' ?"selected":""}}>
                                                            Approved
                                                        </option>
                                                        <option
                                                            value="rejected" {{$order->return_status == 'rejected' ?"selected":""}}>
                                                            Rejected
                                                        </option>

                                                    </select>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Return Status</label>
                                                    <input type="text" class="form-control" name="admin_remarks"
                                                           value="{{$order->admin_remarks??''}}">
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

                    {{ $orders->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
