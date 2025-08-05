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
                    <li class="breadcrumb-item active" aria-current="page">Subscription Plans</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Subscription Plans</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('subscription_plans.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>MRP</th>
                            <th>Price</th>
                            <th>Duration (In Months)</th>
                            <th>Min Cart Value</th>
                            <th>Discount Upto</th>
                            <th>Max Discount</th>
                            <th>Vendor Name</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($subscription_plans)){
                        foreach ($subscription_plans as $subscription_plan) {

                            ?>
                        <tr>
                            <td>{{ $subscription_plan->name ?? '' }}</td>
                            <td>{{ $subscription_plan->mrp ?? '' }}</td>
                            <td>{{ $subscription_plan->price ?? '' }}</td>
                            <td>{{ $subscription_plan->duration ?? '' }}</td>
                            <td>{{ $subscription_plan->min_cart_val ?? '' }}</td>
                            <td>{{ $subscription_plan->discount_upto ?? '' }} {{$subscription_plan->discount_type == 'PERCENTAGE' ?"%":""}}</td>
                            <td>{{ $subscription_plan->max_discount ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getVendorName($subscription_plan->vendor_id) }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($subscription_plan->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('subscription_plans.edit',$subscription_plan->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('subscription_plans.delete',$subscription_plan->id.'?back_url='.$BackUrl)}}"
                                               onclick="return confirm('Are you Want To Delete?')"
                                               class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $subscription_plans->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
