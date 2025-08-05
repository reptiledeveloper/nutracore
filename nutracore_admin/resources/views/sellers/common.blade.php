<?php
$BackUrl = \App\Helpers\CustomHelper::BackUrl();
$BackUrl = 'admin/sellers';
$routeName = \App\Helpers\CustomHelper::getAdminRouteName();
$current_route = Route::currentRouteName();
$image = \App\Helpers\CustomHelper::getImageUrl('sellers',$seller->image??'');
?>


<div class="content ">
    <div class="profile-cover bg-image mb-4" data-image="{{url('public')}}/assets/images/profile-bg.jpg" style="height: 0%">
        <div
                class="container d-flex align-items-center justify-content-center h-100 flex-column flex-md-row text-center text-md-start">
            <div class="avatar avatar-xl me-3">
                <img src="{{$image}}" class="rounded-circle" alt="...">
            </div>
            <div class="my-4 my-md-0">
                <h3 class="mb-1">{{$seller->name??''}}</h3>
                <small>Seller</small>
            </div>

            <div class="ms-md-auto">
{{--                <a href="" class="btn btn-primary btn-lg btn-icon">--}}
{{--                    <i class="bi bi-pencil small"></i> Edit Profile--}}
{{--                </a>--}}
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12 col-md-12">
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.view' ? "active":""}}"
                       href="{{route('sellers.view',$seller->id.'?back_url='.$BackUrl)}}">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.roles' ? "active":""}}"
                       href="{{route('sellers.roles',$seller->id.'?back_url='.$BackUrl)}}">Roles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.permission' ? "active":""}}"
                       href="{{route('sellers.permission',$seller->id.'?back_url='.$BackUrl)}}">Permission</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.admins' ? "active":""}}"
                       href="{{route('sellers.admins',$seller->id.'?back_url='.$BackUrl)}}">Admins</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.commission' ? "active":""}}"
                       href="{{route('sellers.commission',$seller->id.'?back_url='.$BackUrl)}}">Commissions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.products' ? "active":""}}"
                       href="{{route('sellers.products',$seller->id.'?back_url='.$BackUrl)}}">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'sellers.orders' ? "active":""}}"
                       href="{{route('sellers.orders',$seller->id.'?back_url='.$BackUrl)}}">Orders</a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link " href="">Transaction</a>--}}
{{--                </li>--}}
            </ul>
        </div>
    </div>

