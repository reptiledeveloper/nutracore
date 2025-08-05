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
                    <li class="breadcrumb-item active" aria-current="page">Orders</li>
                </ol>
            </nav>
        </div>

        @include('layouts.filter',['search_show'=>'search_show','vendor_show'=>'vendor_show','date_show'=>'date_show','delivery_agents_show'=>'delivery_agents_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Orders</div>

                            <div class="dropdown ms-auto">
                                {{--                                <a href="{{ route('banners.add', ['back_url' => $BackUrl]) }}"--}}
                                {{--                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>--}}
                            </div>
                        </div>
                    </div>
                </div>
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

                            $count_order_items = count($order_items);
                            ?>
                        <tr>
                            <td># {{ $order->id ?? '' }}</td>

                            <td>{{\App\Helpers\CustomHelper::getVendorName($order->vendor_id??'')}}</td>
                            <td class="text-wrap">
                                @foreach($order_items as $key => $item)
                                    @php
                                        $product_name = \App\Helpers\CustomHelper::getProductName($item->product_id??'');
                                    @endphp
                                    {{$key+1}} . {{$product_name??''}} <br>
                                @endforeach
                            </td>
                            <td>
                                <strong>{{$order->customer_name??''}}</strong><br>
                                {{$order->contact_no??''}}
                            </td>
                            <td class="text-wrap">{{$order->house_no??''}} {{$order->land_mark??''}} {{$order->apartment??''}} {{$order->location??''}}</td>
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
                                            <a href="{{route('orders.edit',$order->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('orders.view',$order->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>
                                            {{--                                            <a href="{{route('orders.delete',$order->id.'?back_url='.$BackUrl)}}"--}}
                                            {{--                                               onclick="return confirm('Are you Want To Delete?')"--}}
                                            {{--                                               class="dropdown-item">Delete</a>--}}
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
