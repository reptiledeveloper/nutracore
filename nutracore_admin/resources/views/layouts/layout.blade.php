<?php
$user = Auth::guard('admin')->user();
$BackUrl = \App\Helpers\CustomHelper::BackUrl();
$routeName = \App\Helpers\CustomHelper::getAdminRouteName();
$name = $user->name ?? '';
$ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
$role_id = $user->role_id ?? '';
$role_name = \App\Helpers\CustomHelper::getRoleName($role_id);
$url = url()->current();
$baseurl = url('/');
$image = \App\Helpers\CustomHelper::getImageUrl('admin', $user->image);
$notifications = \App\Models\Notification::where('role', 'admin')->where('user_id', $user->id)->limit(4)->get();

$current_route = Route::currentRouteName();

?>

    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Nutracore Admin -Dashboard </title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{favicon()}}">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    {{--    <link href="{{url('public/assets')}}/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">--}}

    <!-- Bootstrap icons -->
    {{--    <link rel="stylesheet" href="{{url('public/assets')}}/dist/icons/bootstrap-icons-1.4.0/bootstrap-icons.min.css"--}}
    {{--          type="text/css">--}}
    <!-- Bootstrap Docs -->
    {{--    <link rel="stylesheet" href="{{url('public/assets')}}/dist/css/bootstrap-docs.css" type="text/css">--}}

    <!-- Slick -->
    <link rel="stylesheet" href="{{url('public/assets')}}/libs/slick/slick.css" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.min.css"/>
    <!-- Main style file -->
    <link rel="stylesheet" href="{{url('public/assets')}}/libs/dropzone/dropzone.css" type="text/css">


    <link rel="stylesheet" href="{{url('public/assets')}}/libs/select2/css/select2.min.css" type="text/css">

    <link rel="stylesheet" href="{{url('public/assets')}}/dist/css/app.min.css" type="text/css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


</head>
<body>

<!-- preloader -->
<div class="preloader">
    <img src="{{logo()}}" alt="logo">
    <div class="preloader-icon"></div>
</div>
<!-- ./ preloader -->
<style>
    .pager {
        padding-left: 0;
        margin: 20px 0;
        text-align: center;
        list-style: none;
    }

    .pager li {
        display: inline;
    }

    .pager li > a,
    .pager li > span {
        display: inline-block;
        padding: 5px 14px;
        background-color: #fff;
        border: 1px solid #86875d;
        border-radius: 15px;
        color: black;
    }

    .pager li > span {
        background: #3d5bf6;
    }

    .cke_notification_warning {
        /*display: none;*/
    }

</style>
<!-- sidebars -->

<!-- notifications sidebar -->
<div class="sidebar" id="notifications">
    <div class="sidebar-header d-block align-items-end">
        <div class="align-items-center d-flex justify-content-between py-4">
            Notifications
            <button data-sidebar-close="">
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active nav-link-notify" data-bs-toggle="tab" href="#activities">Activities</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#notes">Notes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#alerts">Alerts</a>
            </li>
        </ul>
    </div>
    <div class="sidebar-content">
        <div class="tab-content">
            <div class="tab-pane active" id="activities">
                <div class="tab-pane-body">
                    <ul class="list-group list-group-flush">
                        <li class="px-0 list-group-item">
                            <a href="#" class="d-flex">
                                <div class="flex-shrink-0">
                                    <figure class="avatar avatar-info me-3">
                                            <span class="avatar-text rounded-circle">
                                                <i class="bi bi-person"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold d-flex justify-content-between">
                                        You joined a group
                                    </p>
                                    <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Today
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="px-0 list-group-item">
                            <a href="#" class="d-flex">
                                <div class="flex-shrink-0">
                                    <figure class="avatar avatar-warning me-3">
                                            <span class="avatar-text rounded-circle">
                                                <i class="bi bi-hdd"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold d-flex justify-content-between">
                                        Storage is running low!
                                    </p>
                                    <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Today
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="px-0 list-group-item">
                            <a href="#" class="d-flex">
                                <div class="flex-shrink-0">
                                    <figure class="avatar avatar-secondary me-3">
                                            <span class="avatar-text rounded-circle">
                                                <i class="bi bi-file-text"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 d-flex justify-content-between">
                                        1 person sent a file
                                    </p>
                                    <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Yesterday
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="px-0 list-group-item">
                            <a href="#" class="d-flex">
                                <div class="flex-shrink-0">
                                    <figure class="avatar avatar-success me-3">
                                            <span class="avatar-text rounded-circle">
                                                <i class="bi bi-download"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 d-flex justify-content-between">
                                        Reports ready to download
                                    </p>
                                    <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Yesterday
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="px-0 list-group-item">
                            <a href="#" class="d-flex">
                                <div class="flex-shrink-0">
                                    <figure class="avatar avatar-info me-3">
                                            <span class="avatar-text rounded-circle">
                                                <i class="bi bi-lock"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 d-flex justify-content-between">
                                        2 steps verification
                                    </p>
                                    <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> 20 min ago
                                    </span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane-footer">
                    <a href="#" class="btn btn-success">
                        <i class="bi bi-check2 me-2"></i> Make All Read
                    </a>
                    <a href="#" class="btn btn-danger ms-2">
                        <i class="bi bi-trash me-2"></i> Delete all
                    </a>
                </div>
            </div>
            <div class="tab-pane" id="notes">
                <div class="tab-pane-body">
                    <ul class="list-group list-group-flush">
                        <li class="px-0 list-group-item">
                            <p class="mb-0 fw-bold text-success d-flex justify-content-between">
                                This month's report will be prepared.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Today
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                        <li class="px-0 list-group-item">
                            <p class="mb-0 fw-bold text-success d-flex justify-content-between">
                                An email will be sent to the customer.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Today
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                        <li class="px-0 list-group-item">
                            <p class="mb-0 d-flex justify-content-between">
                                The meeting will be held.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Yesterday
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                        <li class="px-0 list-group-item">
                            <p class="mb-0 fw-bold text-success d-flex justify-content-between">
                                Conversation with users.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Yesterday
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                        <li class="px-0 list-group-item">
                            <p class="mb-0 fw-bold text-warning d-flex justify-content-between">
                                Payment refund will be made to the customer.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> 20 min ago
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                        <li class="px-0 list-group-item">
                            <p class="mb-0 d-flex justify-content-between">
                                Payment form will be activated.
                            </p>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> 20 min ago
                            </span>
                            <div class="mt-2">
                                <a href="#">Edit</a>
                                <a href="#" class="text-danger ms-2">Delete</a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane-footer">
                    <a href="#" class="btn btn-primary btn-block">
                        <i class="bi bi-plus me-2"></i> Add Notes
                    </a>
                </div>
            </div>
            <div class="tab-pane" id="alerts">
                <div class="tab-pane-body">
                    <ul class="list-group list-group-flush">
                        <li class="px-0 list-group-item d-flex">
                            <div class="flex-shrink-0">
                                <figure class="avatar avatar-warning me-3">
                                        <span class="avatar-text rounded-circle">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                </figure>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-bold d-flex justify-content-between">
                                    Signed in with a different device.
                                </p>
                                <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Yesterday
                                    </span>
                            </div>
                        </li>
                        <li class="px-0 list-group-item d-flex">
                            <div class="flex-shrink-0">
                                <figure class="avatar avatar-warning me-3">
                                        <span class="avatar-text fw-bold rounded-circle">
                                            <i class="bi bi-file-text"></i>
                                        </span>
                                </figure>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-bold d-flex justify-content-between">
                                    Your billing information is not active.
                                </p>
                                <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Yesterday
                                    </span>
                            </div>
                        </li>
                        <li class="px-0 list-group-item d-flex">
                            <div class="flex-shrink-0">
                                <figure class="avatar avatar-warning me-3">
                                        <span class="avatar-text rounded-circle">
                                            <i class="bi bi-person"></i>
                                        </span>
                                </figure>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 d-flex justify-content-between">
                                    Your subscription has expired.
                                </p>
                                <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Today
                                    </span>
                            </div>
                        </li>
                        <li class="px-0 list-group-item d-flex">
                            <div class="flex-shrink-0">
                                <figure class="avatar avatar-warning me-3">
                                        <span class="avatar-text rounded-circle">
                                            <i class="bi bi-hdd"></i>
                                        </span>
                                </figure>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 d-flex justify-content-between">
                                    Your storage space is running low
                                </p>
                                <span class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> Today
                                    </span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane-footer">
                    <a href="#" class="btn btn-success">
                        <i class="bi bi-check2 me-2"></i> Make All Read
                    </a>
                    <a href="#" class="btn btn-danger ms-2">
                        <i class="bi bi-trash me-2"></i> Delete all
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./ notifications sidebar -->

<!-- settings sidebar -->
<div class="sidebar" id="settings">
    <div class="sidebar-header">
        <div>
            <i class="bi bi-gear me-2"></i>
            Settings
        </div>
        <button data-sidebar-close="">
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
    <div class="sidebar-content">
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1" checked="">
                    <label class="form-check-label" for="flexCheckDefault1">
                        Remember next visits
                    </label>
                </div>
            </li>
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2" checked="">
                    <label class="form-check-label" for="flexCheckDefault2">
                        Enable report generation.
                    </label>
                </div>
            </li>
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3" checked="">
                    <label class="form-check-label" for="flexCheckDefault3">
                        Allow notifications.
                    </label>
                </div>
            </li>
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault4">
                    <label class="form-check-label" for="flexCheckDefault4">
                        Hide user requests
                    </label>
                </div>
            </li>
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault5" checked="">
                    <label class="form-check-label" for="flexCheckDefault5">
                        Speed up demands
                    </label>
                </div>
            </li>
            <li class="list-group-item px-0 border-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        Hide menus
                    </label>
                </div>
            </li>
        </ul>
    </div>
    <div class="sidebar-action">
        <a href="#" class="btn btn-primary">All Settings</a>
    </div>
</div>
<!-- ./ settings sidebar -->

<!-- search sidebar -->
<div class="sidebar" id="search">
    <div class="sidebar-header">
        Search
        <button data-sidebar-close="">
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
    <div class="sidebar-content">
        <form class="mb-4">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search" aria-describedby="button-search-addon">
                <button class="btn btn-outline-light" type="button" id="button-search-addon">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <h6 class="mb-3">Last searched</h6>
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-search"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Reports for 2021</a>
                <a href="#" class="btn text-danger btn-sm" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-search"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Current users</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-search"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Meeting notes</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </div>
        <h6 class="mb-3">Recently viewed</h6>
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-secondary avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-check-circle"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Todo list</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-warning avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-wallet2"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Pricing table</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-info avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-gear"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Settings</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            <div class="d-flex align-items-center mb-3">
                <a href="#" class="avatar avatar-success avatar-sm me-3">
                        <span class="avatar-text rounded-circle">
                            <i class="bi bi-person-circle"></i>
                        </span>
                </a>
                <a href="#" class="flex-fill">Users</a>
                <a href="#" class="btn" data-bs-toggle="tooltip" title="Remove">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="sidebar-action">
        <a href="#" class="btn btn-danger">All Clear</a>
    </div>
</div>
<!-- ./ search sidebar -->

<!-- ./ sidebars -->

<!-- menu -->
<div class="menu">
    <div class="menu-header">
        <a href="{{url('/admin')}}" class="menu-header-logo">
            <img src="{{logo()}}" alt="logo">
        </a>
        <a href="{{url('/admin')}}" class="btn btn-sm menu-close-btn">
            <i class="bi bi-x"></i>
        </a>
    </div>
    <div class="menu-body">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center" data-bs-toggle="dropdown">
                <div class="avatar me-3">
                    <img src="{{$image}}" class="rounded-circle" alt="image">
                </div>
                <div>
                    <div class="fw-bold">{{$user->name??''}}</div>
                    <small class="text-muted">{{$role_name??''}}</small>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="{{route('admin.profile')}}" class="dropdown-item d-flex align-items-center">
                    <i class="bi bi-person dropdown-item-icon"></i> Profile
                </a>
                <a href="{{route('admin.settings')}}" class="dropdown-item d-flex align-items-center">
                    <i class="bi bi-gear dropdown-item-icon"></i> Settings
                </a>
                <a href="{{route('admin.logout')}}" class="dropdown-item d-flex align-items-center text-danger"
                   target="_blank">
                    <i class="bi bi-box-arrow-right dropdown-item-icon"></i> Logout
                </a>
            </div>
        </div>
        <ul>
            <li>
                <a class="{{$current_route == 'home' ? "active":""}}" href="{{url('/admin')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Role/Permission</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'roles.index' ? "active":""}}" href="{{route('roles.index')}}">Roles</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'permission.index' ? "active":""}}"
                           href="{{route('permission.index')}}">Permission</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'admins.index' ? "active":""}}" href="{{route('admins.index')}}">Admins</a>
                    </li>
                </ul>
            </li>


            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-check-circle"></i>
                    </span>
                    <span>Master</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'banners.index' ? "active":""}}"
                           href="{{route('banners.index')}}">Banners</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'categories.index' ? "active":""}}"
                           href="{{route('categories.index')}}">Categories</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'subcategories.index' ? "active":""}}"
                           href="{{route('subcategories.index')}}">Sub Categories</a>
                    </li>
                    {{--                    <li>--}}
                    {{--                        <a class="{{$current_route == 'child_categories.index' ? "active":""}}"--}}
                    {{--                           href="{{route('child_categories.index')}}">Child Categories</a>--}}
                    {{--                    </li>--}}
                    <li>
                        <a class="{{$current_route == 'brands.index' ? "active":""}}" href="{{route('brands.index')}}">Brand</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'attributes.index' ? "active":""}}"
                           href="{{route('attributes.index')}}">Attributes</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'tags.index' ? "active":""}}"
                           href="{{route('tags.index')}}">Tags</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'loyality_system.index' ? "active":""}}"
                           href="{{route('loyality_system.index')}}">Loyality System</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'free_product.index' ? "active":""}}"
                           href="{{route('free_product.index')}}">Free Product</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'new_updates.index' ? "active":""}}"
                           href="{{route('new_updates.index')}}">Wellness Series</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'testimonial.index' ? "active":""}}"
                           href="{{route('testimonial.index')}}">Happy Customers</a>
                    </li>
                    {{-- <li>
                        <a class="{{$current_route == 'manufacturer.index' ? "active":""}}"
                           href="{{route('manufacturer.index')}}">Manufacturer</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'gallery.index' ? "active":""}}"
                           href="{{route('gallery.index')}}">Gallery</a>
                    </li> --}}
                    {{--                    <li>--}}
                    {{--                        <a class="{{$current_route == 'app_settings.index' ? "active":""}}"--}}
                    {{--                           href="{{route('app_settings.index')}}">App Settings</a>--}}
                    {{--                    </li>--}}
                    <li>
                        <a class="{{$current_route == 'delivery_charges.index' ? "active":""}}"
                           href="{{route('delivery_charges.index')}}">Delivery Charges</a>
                    </li>
                    {{-- <li>
                        <a class="{{$current_route == 'tax.index' ? "active":""}}"
                           href="{{route('tax.index')}}">TAX</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'featured_section.index' ? "active":""}}"
                           href="{{route('featured_section.index')}}">Featured Section Home</a>
                    </li> --}}
                </ul>
            </li>
            <li>
                <a class="{{$current_route == 'sellers.index' ? "active":""}}" href="{{route('sellers.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Stores</span>
                </a>
            </li>
            @if(\App\Helpers\CustomHelper::isAllowedModule('slots'))
                {{-- <li>
                    <a class="{{$current_route == 'slots.index' ? "active":""}}" href="{{route('slots.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-clock"></i>
                    </span>
                        <span>Slots</span>
                    </a>
                </li> --}}
            @endif
            {{-- <li>
                <a class="{{$current_route == 'delivery_agents.index' ? "active":""}}"
                   href="{{route('delivery_agents.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-truck"></i>
                    </span>
                    <span>Delivery Agents</span>
                </a>
            </li> --}}

            <li>
                <a class="{{$current_route == 'offers.index' ? "active":""}}" href="{{route('offers.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-gift"></i>
                    </span>
                    <span>Offers / Promo Code</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-map"></i>
                    </span>
                    <span>Locations</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'cities.index' ? "active":""}}" href="{{route('cities.index')}}
                        ">City</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'pincode.index' ? "active":""}}" href="{{route('pincode.index')}}
                        ">Pincode</a>
                    </li>

                </ul>
            </li>


            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-map"></i>
                    </span>
                    <span>Subscriptions</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'subscription_plans.index' ? "active":""}}" href="{{route('subscription_plans.index')}}
                        ">Subscription Plans</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'subscriptions.index' ? "active":""}}" href="{{route('subscriptions.index')}}
                      ">Subscriptions</a>
                    </li>
                    <!--
                    <li>
                        <a class="{{$current_route == 'subscription_orders.index' ? "active":""}}" href="{{route('subscription_orders.index')}}
                    ">Subscription Orders</a>
                </li> -->


                </ul>
            </li>

            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>
                    <span>Products</span>
                </a>
                <ul>


                    <li>
                        <a class="{{$current_route == 'products.index' ? "active":""}}" href="{{route('products.index')}}
                        ">Products</a>
                    </li>

                    {{-- <li>
                        <a class="{{$current_route == 'products.approve_product' ? "active":""}}"
                           href="{{ route('products.approve_product', ['back_url' => 'admin/approve_product']) }}">Approve
                            Product</a>
                    </li> --}}
                    <li>
                        <a class="{{$current_route == 'products.assign_product' ? "active":""}}"
                           href="{{ route('products.assign_product', ['back_url' => 'admin/assign_product']) }}">Assign
                            Product</a>
                    </li>

                </ul>
            </li>

            <li>
                <a class="{{$current_route == 'collections.index' ? "active":""}}"
                   href="{{route('collections.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>Collections</span>
                </a>
            </li>

            <li>
                <a class="{{$current_route == 'gift_card.index' ? "active":""}}" href="{{route('gift_card.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>GiftCard</span>
                </a>
            </li>
            {{--            <li>--}}
            {{--                <a class="{{$current_route == 'inventory_management.index' ? "active":""}}" href="{{route('inventory_management.index')}}">--}}
            {{--                    <span class="nav-link-icon">--}}
            {{--                        <i class="bi bi-shop"></i>--}}
            {{--                    </span>--}}
            {{--                    <span>Inventory Management</span>--}}
            {{--                </a>--}}
            {{--            </li>--}}

            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>
                    <span>Inventory Management</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'suppliers.index' ? "active":""}}" href="{{route('suppliers.index')}}
                        ">Suppliers</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'invoices.index' ? "active":""}}" href="{{route('invoices.index')}}
                        ">Invoices</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'stocks.index' ? "active":""}}" href="{{route('stocks.index')}}
                        ">Stocks & Expiry</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'stocks.closingStockList' ? "active":""}}" href="{{route('stocks.closingStockList')}}
                        ">Closing Stock</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'stocks.stockLogs' ? "active":""}}" href="{{route('stocks.stockLogs')}}
                        ">StockLogs</a>
                    </li>
                    <li>
                        <a class="{{$current_route == 'stock_transfers.index' ? "active":""}}" href="{{route('stock_transfers.index')}}
                        ">Stock Transfers</a>
                    </li>


                </ul>
            </li>


            <li>
                <a class="{{$current_route == 'abandoned_cart.index' ? "active":""}}"
                   href="{{route('abandoned_cart.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>Abandoned Cart</span>
                </a>
            </li>

            <li>
                <a class="{{$current_route == 'orders.index' ? "active":""}}" href="{{route('orders.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>Orders</span>
                </a>
            </li>

            <li>
                <a class="{{$current_route == 'transaction.index' ? "active":""}}"
                   href="{{route('transaction.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>Transaction</span>
                </a>
            </li>
            <li>
                <a class="{{$current_route == 'return_request.index' ? "active":""}}"
                   href="{{route('return_request.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-shop"></i>
                    </span>
                    <span>Return Request</span>
                </a>
            </li>

            <li>
                <a class="{{$current_route == 'users.index' ? "active":""}}" href="{{route('users.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a class="{{$current_route == 'faqs.index' ? "active":""}}" href="{{route('faqs.index')}}">
                    <span class="nav-link-icon">
                        <i class="bi bi-question"></i>
                    </span>
                    <span>FAQ</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-map"></i>
                    </span>
                    <span>Support Tickets</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'support_tickets.index' ? "active":""}}" href="{{route('support_tickets.index')}}
                        ">Support Ticket</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-map"></i>
                    </span>
                    <span>Notifications</span>
                </a>
                <ul>
                    <li>
                        <a class="{{$current_route == 'notifications.index' ? "active":""}}" href="{{route('notifications.index')}}
                        ">Notifications</a>
                    </li>
                </ul>
            </li>

            {{--            <li>--}}
            {{--                <a href="#">--}}
            {{--                    <span class="nav-link-icon">--}}
            {{--                        <i class="bi bi-map"></i>--}}
            {{--                    </span>--}}
            {{--                    <span>Reports</span>--}}
            {{--                </a>--}}
            {{--                <ul>--}}
            {{--                    <li>--}}
            {{--                        <a class="{{$current_route == 'reports.index' ? "active":""}}" href="{{route('reports.index')}}--}}
            {{--                        ">Reports</a>--}}
            {{--                    </li>--}}
            {{--                </ul>--}}
            {{--            </li>--}}

            <li>
                <a class="" href="{{route('admin.logout')}}">
                    <span class="nav-link-icon">
                        <i class="fa fa-sign-out"></i>
                    </span>
                    <span>Logout</span>
                </a>
            </li>


            <?php /*
    <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Invoices</span>
                </a>
                <ul>
                    <li>
                        <a href="invoices.html">List</a>
                    </li>
                    <li>
                        <a href="invoice-detail.html">Detail</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="chats.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-chat-square"></i>
                    </span>
                    <span>Chats</span>
                    <span class="badge bg-success rounded-circle ms-auto">2</span>
                </a>
            </li>
            <li>
                <a href="email.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <span>Email</span>
                </a>
                <ul>
                    <li>
                        <a href="email.html">
                            <span>Inbox</span>
                        </a>
                    </li>
                    <li>
                        <a href="email-detail.html">
                            <span>Detail</span>
                        </a>
                    </li>
                    <li>
                        <a target="_blank" href="email-template.html">
                            <span>Email Template</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="todo-list.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-check-circle"></i>
                    </span>
                    <span>Todo App</span>
                </a>
                <ul>
                    <li>
                        <a href="todo-list.html">
                            <span>List</span>
                        </a>
                    </li>
                    <li>
                        <a href="todo-detail.html">
                            <span>Details</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-divider">Pages</li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <span>Profile</span>
                </a>
                <ul>
                    <li>
                        <a href="profile-posts.html">Post</a>
                    </li>
                    <li>
                        <a href="profile-connections.html">Connections</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    <span>Users</span>
                </a>
                <ul>
                    <li><a href="user-list.html">List View</a></li>
                    <li><a href="user-grid.html">Grid View</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <span>Authentication</span>
                </a>
                <ul>
                    <li>
                        <a href="login-1.html" target="_blank">Login</a>
                    </li>
                    <li>
                        <a href="register.html" target="_blank">Register</a>
                    </li>
                    <li>
                        <a href="reset-password.html" target="_blank">Reset Password</a>
                    </li>
                    <li>
                        <a href="lock-screen.html" target="_blank">Lock Screen</a>
                    </li>
                    <li>
                        <a href="account-verified.html" target="_blank">Account Verified</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-exclamation-octagon"></i>
                    </span>
                    <span>Error Pages</span>
                </a>
                <ul>
                    <li>
                        <a href="404.html" target="_blank">404</a>
                    </li>
                    <li>
                        <a href="access-denied.html">Access Denied</a>
                    </li>
                    <li>
                        <a href="under-construction.html" target="_blank">Under Construction</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="settings.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-gear"></i>
                    </span>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="pricing-table.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>
                    <span>Pricing Table</span>
                    <span class="badge bg-success ms-auto">New</span>
                </a>
            </li>
            <li>
                <a href="search-page.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <span>Search Page</span>
                </a>
            </li>
            <li>
                <a href="faq.html">
                    <span class="nav-link-icon">
                        <i class="bi bi-question-circle"></i>
                    </span>
                    <span>FAQ</span>
                </a>
            </li>
            <li class="menu-divider">User Interface</li>
            <li>
                <a href="#" target="_blank">
                    <span class="nav-link-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>
                    <span>Components</span>
                </a>
                <ul>
                    <li>
                        <a href="accordion.html">Accordion</a>
                    </li>
                    <li>
                        <a href="alert.html">Alerts</a>
                    </li>
                    <li>
                        <a href="badge.html">Badge</a>
                    </li>
                    <li>
                        <a href="breadcrumb.html">Breadcrumb</a>
                    </li>
                    <li>
                        <a href="buttons.html">Buttons</a>
                    </li>
                    <li>
                        <a href="button-group.html">Button Group</a>
                    </li>
                    <li>
                        <a href="card.html">Card</a>
                    </li>
                    <li>
                        <a href="card-masonry.html">Card Masonry</a>
                    </li>
                    <li>
                        <a href="carousel.html">Carousel</a>
                    </li>
                    <li>
                        <a href="collapse.html">Collapse</a>
                    </li>
                    <li>
                        <a href="dropdown.html">Dropdowns</a>
                    </li>
                    <li>
                        <a href="list-group.html">List Group</a>
                    </li>
                    <li>
                        <a href="modal.html">Modal</a>
                    </li>
                    <li>
                        <a href="navs-tabs.html">Navs and Tabs</a>
                    </li>
                    <li>
                        <a href="pagination.html">Pagination</a>
                    </li>
                    <li>
                        <a href="popovers.html">Popovers</a>
                    </li>
                    <li>
                        <a href="progress.html">Progress</a>
                    </li>
                    <li>
                        <a href="spinners.html">Spinners</a>
                    </li>
                    <li>
                        <a href="toasts.html">Toasts</a>
                    </li>
                    <li>
                        <a href="tables.html">
                            <span>Tables</span>
                        </a>
                    </li>
                    <li>
                        <a href="tooltip.html">Tooltip</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" target="_blank">
                    <span class="nav-link-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>
                    <span>Forms</span>
                </a>
                <ul>
                    <li>
                        <a href="#">
                            <span>Form Elements</span>
                        </a>
                        <ul>
                            <li>
                                <a href="forms.html">Overview</a>
                            </li>
                            <li>
                                <a href="form-control.html">Form Controls</a>
                            </li>
                            <li>
                                <a href="select.html">Select</a>
                            </li>
                            <li>
                                <a href="checks-radios.html">Checks and Radios</a>
                            </li>
                            <li>
                                <a href="range.html">Range</a>
                            </li>
                            <li>
                                <a href="input-group.html">Input Group</a>
                            </li>
                            <li>
                                <a href="floating-label.html">Floating Label</a>
                            </li>
                            <li>
                                <a href="forms-layout.html">Form Layout</a>
                            </li>
                            <li>
                                <a href="form-validation.html">Validation</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="form-wizard.html">
                            <span>Wizard</span>
                        </a>
                    </li>
                    <li>
                        <a href="form-repeater.html">
                            <span>Repeater</span>
                        </a>
                    </li>
                    <li>
                        <a href="file-upload.html">
                            <span>File Upload</span>
                        </a>
                    </li>
                    <li>
                        <a href="ckeditor.html">
                            <span>CKEditor</span>
                        </a>
                    </li>
                    <li>
                        <a href="range-slider.html">
                            <span>Range Slider</span>
                        </a>
                    </li>
                    <li>
                        <a href="select2.html">
                            <span>Select2</span>
                        </a>
                    </li>
                    <li>
                        <a href="tags-input.html">
                            <span>Tags Input</span>
                        </a>
                    </li>
                    <li>
                        <a href="input-mask.html">
                            <span>Input Mask</span>
                        </a>
                    </li>
                    <li>
                        <a href="datepicker.html">
                            <span>Datepicker</span>
                        </a>
                    </li>
                    <li>
                        <a href="clockpicker.html">
                            <span>Clock Picker</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-heart"></i>
                    </span>
                    <span>Content</span>
                </a>
                <ul>
                    <li>
                        <a href="typography.html">
                            <span>Typography</span>
                        </a>
                    </li>
                    <li>
                        <a href="images.html">
                            <span>Images</span>
                        </a>
                    </li>
                    <li>
                        <a href="figures.html">
                            <span>Figures</span>
                        </a>
                    </li>
                    <li>
                        <a href="avatar.html">
                            <span>Avatar</span>
                        </a>
                    </li>
                    <li>
                        <a href="icons.html">
                            <span>Icons</span>
                        </a>
                    </li>
                    <li>
                        <a href="colors.html">
                            <span>Colors</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Charts</span>
                </a>
                <ul>
                    <li>
                        <a href="apexchart.html">Apex Chart</a>
                    </li>
                    <li>
                        <a href="chartjs.html">Chartjs</a>
                    </li>
                    <li>
                        <a href="justgage.html">Justgage</a>
                    </li>
                    <li>
                        <a href="morsis.html">Morsis</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-paperclip"></i>
                    </span>
                    <span>Extensions</span>
                </a>
                <ul>
                    <li>
                        <a href="vector-map.html">
                            <span>Vector Map</span>
                        </a>
                    </li>
                    <li>
                        <a href="datatable.html">
                            <span>Datatable</span>
                        </a>
                    </li>
                    <li>
                        <a href="sweet-alert.html">Sweet Alert</a>
                    </li>
                    <li>
                        <a href="lightbox.html">Lightbox</a>
                    </li>
                    <li>
                        <a href="introjs.html">Introjs</a>
                    </li>
                    <li>
                        <a href="nestable.html">Nestable</a>
                    </li>
                    <li>
                        <a href="rating.html">Rating</a>
                    </li>
                    <li>
                        <a href="code-highlighter.html">Code Highlighter</a>
                    </li>
                </ul>
            </li>
            <li class="menu-divider">Other</li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-list"></i>
                    </span>
                    <span>Menu Item</span>
                </a>
                <ul>
                    <li><a href="#">Menu Item 1</a></li>
                    <li>
                        <a href="#">Menu Item 2</a>
                        <ul>
                            <li>
                                <a href="#">Menu Item 2.1</a>
                            </li>
                            <li>
                                <a href="#">Menu Item 2.2</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="disabled">
                    <span class="nav-link-icon">
                        <i class="bi bi-hand-index-thumb"></i>
                    </span>
                    <span>Disabled</span>
                </a>
            </li>
 */ ?>
        </ul>
    </div>
</div>
<!-- ./  menu -->

<!-- layout-wrapper -->
<div class="layout-wrapper">

    <!-- header -->
    <div class="header">
        <div class="menu-toggle-btn"> <!-- Menu close button for mobile devices -->
            <a href="#">
                <i class="bi bi-list"></i>
            </a>
        </div>
        <!-- Logo -->
        <a href="index.html" class="logo">
            <img width="100" src="{{url('public')}}/assets/images/logo.svg" alt="logo">
        </a>
        <!-- ./ Logo -->
        <div class="page-title">Overview</div>
        <form class="search-form">
            <div class="input-group">
                <button class="btn btn-outline-light" type="button" id="button-addon1">
                    <i class="bi bi-search"></i>
                </button>
                <input type="text" class="form-control" placeholder="Search..."
                       aria-label="Example text with button addon" aria-describedby="button-addon1">
                <a href="#" class="btn btn-outline-light close-header-search-bar">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
        <div class="header-bar ms-auto">
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item">
                    <a href="{{route('admin.google_auth')}}" class="nav-link">
                        <i class="bi bi-google icon-lg"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link nav-link-notify" data-count="2" data-sidebar-target="#notifications">
                        <i class="bi bi-bell icon-lg"></i>
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a href="{{route('admin.logout')}}" class="btn btn-primary btn-icon">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- Header mobile buttons -->
        <div class="header-mobile-buttons">
            <a href="#" class="search-bar-btn">
                <i class="bi bi-search"></i>
            </a>
            <a href="#" class="actions-btn">
                <i class="bi bi-three-dots"></i>
            </a>
        </div>
        <!-- ./ Header mobile buttons -->
    </div>
    <!-- ./ header -->
    <!-- content -->
    @yield('content')
    <!-- ./ content -->

    <!-- content-footer -->
    <footer class="content-footer">
        <div>© {{date('Y')}} Nutracore- <a href="" target="_blank">Nutracore</a></div>
    </footer>
    <!-- ./ content-footer -->

</div>
<!-- ./ layout-wrapper -->

<!-- Bundle scripts -->
<script src="{{url('public')}}/assets/libs/bundle.js"></script>

<!-- Apex chart -->
<script src="{{url('public')}}/assets/libs/charts/apex/apexcharts.min.js"></script>

<!-- Slick -->
<script src="{{url('public')}}/assets/libs/slick/slick.min.js"></script>

<!-- Examples -->
<script src="{{url('public')}}/assets/dist/js/examples/dashboard.js"></script>

<script src="{{url('public')}}/assets/libs/dropzone/dropzone.js"></script>
<!-- Prism -->
<script src="{{url('public')}}/assets/libs/prism/prism.js"></script>

<script src="{{url('public')}}/assets/dist/js/examples/chat.js"></script>
<!-- Main Javascript file -->
<script src="{{url('public')}}/assets/dist/js/app.min.js"></script>
<script src="{{url('public')}}/assets/libs/select2/js/select2.min.js"></script>


<script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


</body>
</html>
@php
    $ajax_pro_id = "";
        if(!empty($product_id) && !is_array($product_id)){
                   $ajax_pro_id = $product_id;
               }
@endphp

<script>
    $('#category_id').change(function () {
        var _token = '{{ csrf_token() }}';
        var category_id = $('#category_id').val();
        var product_id = '{{ $ajax_pro_id }}';
        $.ajax({
            url: "{{ route('admin.get_sub_category') }}",
            type: "POST",
            data: {category_id: category_id},
            dataType: "HTML",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#subcategory_id').html(resp);
                if (product_id == "") {
                    getTags(category_id);
                }

            }
        });
    });

    function getTags(category_id) {
        var _token = '{{ csrf_token() }}';
        $.ajax({
            url: "{{ route('admin.get_tags') }}",
            type: "POST",
            data: {category_id: category_id},
            dataType: "HTML",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#tags').html(resp);

            }
        });
    }

    document.querySelectorAll('.editor').forEach(element => {
        ClassicEditor
            .create(element)
            .then(editor => {
                editor.ui.view.editable.element.style.height = '300px';
            })
            .catch(error => {
                console.error(error);
            });
    });


    $(document).ready(function () {
        $('.select2').select2({
            placeholder: 'Select'
        });

        var type_id = '{{$type_id??''}}';
        if (type_id !== '') {
            $('.select2product').val(type_id).trigger('change');
        }

        $('.select2product').select2({
            ajax: {
                url: '{{route('products.search')}}', // Replace with your API endpoint
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // Search term
                        page: params.page // Pagination if needed
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items, // Adjust according to your API response
                        pagination: {
                            more: data.pagination.more // For more results
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1, // Minimum characters to start searching
            placeholder: 'Search for products...',
            allowClear: true
        });

    });

    $(document).ready(function () {
        $('.select2user').select2({
            placeholder: 'Select User'
        });
        $('.select2user').select2({
            ajax: {
                url: '{{route('users.search')}}', // Replace with your API endpoint
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // Search term
                        page: params.page // Pagination if needed
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items, // Adjust according to your API response
                        pagination: {
                            more: data.pagination.more // For more results
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1, // Minimum characters to start searching
            placeholder: 'Search for Users...',
            allowClear: true
        });

    });


</script>
