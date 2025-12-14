<?php
$BackUrl = \App\Helpers\CustomHelper::BackUrl();
$BackUrl = 'admin/nc_partners';
$routeName = \App\Helpers\CustomHelper::getAdminRouteName();
$current_route = Route::currentRouteName();
$image = \App\Helpers\CustomHelper::getImageUrl('nc_partners', $nc_partners->image ?? '');
?>


<div class="content ">
    <div class="profile-cover bg-image mb-4" data-image="{{url('public')}}/assets/images/profile-bg.jpg"
         style="height: 0%">
        <div
            class="container d-flex align-items-center justify-content-center h-100 flex-column flex-md-row text-center text-md-start">
            <div class="avatar avatar-xl me-3">
                <img src="{{$image}}" class="rounded-circle" alt="...">
            </div>
            <div class="my-4 my-md-0">
                <h3 class="mb-1">Name : {{$nc_partners->full_name??''}}</h3>
                <h3 class="mb-1">Phone : {{$nc_partners->mobile_number??''}}</h3>
                <h3 class="mb-1">Email : {{$nc_partners->email??''}}</h3>
                <h3 class="mb-1">Coupon Code : {{$nc_partners->coupon_code??''}}</h3>
                <h3 class="mb-1">Wallet : ₹ {{$nc_partners->wallet??0}}</h3>
                <small>NC Partner</small>
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
        <div class="col-lg-12 col-md-12">
            <ul class="nav nav-pills mb-4">
                <li class="nav-item active">
                    <a class="nav-link {{$current_route == 'nc_partners.view' ? "active":""}}"
                       href="{{route('nc_partners.view',$nc_partners->id.'?back_url='.$BackUrl)}}">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'nc_partners.commission' ? "active":""}}"
                       href="{{route('nc_partners.commission',$nc_partners->id.'?back_url='.$BackUrl)}}">Commissions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'nc_partners.partner_withdrawal' ? "active":""}}"
                       href="{{route('nc_partners.partner_withdrawal',$nc_partners->id.'?back_url='.$BackUrl)}}">Withdrawal</a>
                </li>
            </ul>
        </div>
    </div>

