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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Orders</div>

                            <div class="dropdown ms-auto">
                                {{--                                <a href="{{ route('banners.add', ['back_url' => $BackUrl]) }}"--}}
                                {{--                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>--}}
                                <a data-bs-toggle="modal" data-bs-target="#refreshModal"
                                                                   class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="refreshModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Subscription Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close">
                                </button>
                            </div>
                            <form action="{{route('subscription_orders.generate_subscription_order')}}" method="post">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <label>Select Date</label>
                                            <input type="date" class="form-control" name="date" value="">
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







                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="orders">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Seller Name</th>
                            <th>Item</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>QTY</th>
                            <th>Selling Price</th>
                            <th>Total amount</th>
                            <th>Subscription Details</th>
                            <th>Delivery Agent</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($orders)){
                        foreach ($orders as $order) {
                            $varients = \App\Helpers\CustomHelper::getVendorProductVarientsSingle($order->seller_id, $order->product_id, $order->varient_id) ?? '';
                            $user = \App\Helpers\CustomHelper::getUserDetails($order->user_id ?? '');
                            $address = \App\Helpers\CustomHelper::getAddressDetails($order->address_id ?? '');
//                            $agents = \App\Helpers\CustomHelper::getAgents($order->seller_id);
                            $agents = \App\Helpers\CustomHelper::getAgents();
                            $agent_data = \App\Helpers\CustomHelper::getAgentData($order->agent_id??'');
                            ?>
                        <tr>
                            <td># {{ $order->id ?? '' }}</td>

                            <td>{{\App\Helpers\CustomHelper::getVendorName($order->seller_id??'')}}</td>
                            <td>
                                {{\App\Helpers\CustomHelper::getProductName($order->product_id??'')}}<br>
                                {{$varients->unit??''}} - {{$varients->unit_value??''}}
                            </td>
                            <td>
                                <strong>{{$user->name??''}}</strong><br>
                                {{$user->phone??''}}
                            </td>
                            <td class="text-wrap">{{$address->flat_no??''}}  {{$address->building_name??''}}  {{$address->landmark??''}}  {{$address->pincode??''}}
                                <br>
                                {{$address->location??''}}

                            </td>
                            <td>{{$order->qty??''}}</td>
                            <td>{{$order->subscription_price??''}}</td>
                            <td>₹ {{$order->total_price??''}}</td>
                            <td>
                                <strong>{{strtoupper($order->type??'')}}</strong><br>
                                Start Date : {{$order->start_date??''}}<br>
                                End Date : {{$order->end_date??''}}<br>

                                @if($order->type == 'weekly')
                                    @php
                                        $week_data = json_decode($order->subscription_data);
                                        if(!empty($week_data)){
                                            foreach ($week_data as $wee){
                                                if($wee->qty > 0){
                                                    $text =  $wee->day??'';
                                                    $text.=' - ';
                                                    $text.=$wee->qty??0;
                                                    $text.='<br>';
                                                    echo $text;
                                                 }
                                            }
                                        }
                                    @endphp
                                @endif

                            </td>
                            <td>
                                {{$agent_data->name??''}}<br>
                                {{$agent_data->phone??''}}<br>

                            </td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a data-bs-toggle="modal" data-bs-target="#updateSubsOrder{{$order->id}}"
                                               class="dropdown-item">Edit</a>

                                            <a href="{{route('subscription_orders.delete',$order->id.'?back_url='.$BackUrl)}}"
                                               onclick="return confirm('Are you Want To Delete?')"
                                               class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>


                        <div class="modal fade" id="updateSubsOrder{{$order->id}}" tabindex="-1"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Subscription Order
                                            - {{$order->id??''}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <form action="{{route('subscription_orders.update_subscription',$order->id.'?back_url='.$BackUrl)}}" method="post">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label>Select Start Date</label>
                                                    <input type="date" class="form-control" name="start_date"
                                                           value="{{$order->start_date??''}}">
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label>Select End Date</label>
                                                    <input type="date" class="form-control" name="end_date"
                                                           value="{{$order->end_date??''}}">
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <label>Select Type</label>
                                                    <select class="form-control" name="type">
                                                        <option value="" selected>Select</option>

                                                        <option value="daily" {{$order->type=='daily'?"selected":""}}>
                                                            Daily
                                                        </option>
                                                        <option
                                                            value="alternative" {{$order->type=='alternative'?"selected":""}}>
                                                            Alternative
                                                        </option>
                                                        <option value="weekly" {{$order->type=='weekly'?"selected":""}}>
                                                            Weekly
                                                        </option>

                                                    </select>
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <label>Select Delivery Agent</label>
                                                    <select class="form-control" name="agent_id">
                                                        <option value="" selected>Select</option>
                                                        @foreach($agents as $agent)
                                                            <option
                                                                value="{{$agent->id}}" {{$agent->id == $order->agent_id?"selected":""}}>{{$agent->name??''}}
                                                                - {{$agent->phone??''}}</option>
                                                        @endforeach
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

                    {{ $orders->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
