<?php
$BackUrl = \App\Helpers\CustomHelper::BackUrl();
$BackUrl = 'admin/delivery_agents';
$routeName = \App\Helpers\CustomHelper::getAdminRouteName();
$current_route = Route::currentRouteName();
$image = \App\Helpers\CustomHelper::getImageUrl('agents', $users->image ?? '');
?>

<div class="content ">
    <div class="profile-cover bg-image mb-4" data-image="{{url('public')}}/assets/images/profile-bg.jpg"
         style="height: 0%">
        <div
                class="container d-flex align-items-center justify-content-center h-100 flex-column flex-md-row text-center text-md-start">
            <div class="avatar avatar-xl me-3">
                <img src="{{$image}}" class="rounded-circle img-fluid" alt="...">
            </div>
            <div class="my-4 my-md-0">
                <h3 class="mb-1">{{$users->name??'Guest User'}}</h3>
                <h5 class="mb-1">{{$users->phone??''}}</h5>
                <h5 class="mb-1">{{$users->email??''}}</h5>
                <h5 class="mb-1">{{$users->address??''}}</h5>

                <small>Delivery Agents</small>
            </div>

            <div class="ms-md-auto">

                <?php if (request()->has('back_url')){
                    $back_url = request('back_url'); ?>
                <div class="dropdown ms-auto">
                    <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                </div>
                <?php } ?>


            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7 col-md-12">
            <ul class="nav nav-pills mb-4">
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link {{$current_route == 'delivery_agents.view' ? "active":""}}"--}}
{{--                       href="{{route('delivery_agents.view',$users->id.'?back_url='.$BackUrl)}}">Profile</a>--}}
{{--                </li>--}}
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'delivery_agents.view' ? "active":""}}"
                       href="{{route('delivery_agents.view',$users->id.'?back_url='.$BackUrl)}}">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'delivery_agents.transactions' ? "active":""}}"
                       href="{{route('delivery_agents.transactions',$users->id.'?back_url='.$BackUrl)}}">Transactions</a>
                </li>
            </ul>
        </div>
    </div>




    <script>
        function get_wallet_type(value){
            $('#expire_date').hide();
            if(value == 'cashback_wallet'){
                $('#expire_date').show();
            }
        }
    </script>


