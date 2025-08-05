@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $users_id = $agents->id ?? '';
    $name = $agents->name ?? '';
    $email = $agents->email ?? '';
    $phone = $agents->phone ?? '';
    $status = $agents->status ?? 1;

    $image = \App\Helpers\CustomHelper::getImageUrl('agents', $agents->image ?? '');
    ?>
    @include('agents.common',['users'=>$agents])

    @include('snippets.errors')
    @include('snippets.flash')

    <div class="card mt-3">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="orders">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Seller Name</th>
                        <th>Item</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Items</th>
                        <th>Total amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>

                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($orders)){
                    foreach ($orders as $order) {
                        $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($order->id);
                        $product = [];
                        if(!empty($order_items[0])){
                            $product = \App\Models\Products::where('id',$order_items[0]->product_id)->first();
                        }
                        $count_order_items = count($order_items);
                        ?>
                    <tr>
                        <td># {{ $order->id ?? '' }}</td>

                        <td>{{\App\Helpers\CustomHelper::getVendorName($order->vendor_id??'')}}</td>
                        <td>{{$product->name??''}}
                            <br>
                                <?php if ($count_order_items > 1){ ?>
                            & {{$count_order_items-1}} More.
                            <?php } ?></td>
                        <td>
                            <strong>{{$order->customer_name??''}}</strong><br>
                            {{$order->contact_no??''}}
                        </td>
                        <td>{{$order->address??''}}</td>
                        <td>{{$count_order_items??''}}</td>
                        <td>₹ {{$order->total_amount??''}}</td>
                        <td>{{$order->payment_method??''}}</td>
                        <td>{!! \App\Helpers\CustomHelper::getOrderStatus($order->id) !!}</td>
                        <td>{{ date('d M Y h:i A',strtotime($order->created_at)) }}</td>
                        <td class="text-end">
                            <div class="d-flex">
                                <div class="dropdown ms-auto">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                       aria-haspopup="true" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
{{--                                        <a target="_blank"--}}
{{--                                           href="{{route('orders.edit',$order->id.'?back_url='.$BackUrl)}}"--}}
{{--                                           class="dropdown-item">Edit</a>--}}
                                        <a target="_blank"
                                           href="{{route('orders.view',$order->id.'?back_url='.$BackUrl)}}"
                                           class="dropdown-item">View</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
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
