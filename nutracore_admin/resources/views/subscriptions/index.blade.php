@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $subscription_plans = \App\Helpers\CustomHelper::getSubscriptionPlans();
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
                    <li class="breadcrumb-item active" aria-current="page">Subscriptions</li>
                </ol>
            </nav>
        </div>

        <div class="modal fade" id="addsubscription" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Subscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('subscriptions.add_subscription')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label>Choose User</label>
                                    <select class="form-control select2user" name="user_id" id="user_id">
                                        <option value="" selected>Select User</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label>Choose Subscription</label>
                                    <select class="form-control " name="subscription_id" id="subscription_id">
                                        <option value="" selected>Select Subscription</option>
                                        @foreach($subscription_plans as $subscription_plan)
                                            <option
                                                value="{{$subscription_plan->id??''}}">{{$subscription_plan->name??''}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Subscriptions</div>

                            <div class="dropdown ms-auto">
                                <a data-bs-toggle="modal" data-bs-target="#addsubscription"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User Image</th>
                            <th>User Details</th>
                            <th>Subscription</th>
                            <th>Days Left</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>TXN ID</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($subscriptions)){
                            $i = 1;
                        foreach ($subscriptions as $subscription) {
                            $user_data = \App\Helpers\CustomHelper::getUserDetails($subscription->user_id);
                            $user_image = \App\Helpers\CustomHelper::getImageUrl('users', $user_data->image);
                            $days_left = \App\Helpers\CustomHelper::getDaysLeft($subscription->start_date ?? '', $subscription->end_date ?? '');
                            ?>
                        <tr>
                            <td>{{$i++}}</td>
                            <td><a href="{{$user_image}}" target="_blank"><img src="{{$user_image}}" height="50px"
                                                                               width="50px"></a></td>
                            <td>
                                {{ $user_data->name ?? '' }}<br>
                                {{ $user_data->email ?? '' }}<br>
                                {{ $user_data->phone ?? '' }}<br>

                            </td>
                            <td>

                            </td>
                            <td>
                                @if($days_left >0)
                                    <button class="btn btn-success">{{$days_left}} Days Left
                                        <br>{{$subscription->end_date??''}}</button>
                                @else
                                    <button class="btn btn-danger">Expired<br>{{$subscription->end_date??''}}</button>

                                @endif

                            </td>
                            <td>{{ $subscription->start_date ?? '' }}</td>
                            <td>{{ $subscription->end_date ?? '' }}</td>
                            <td>{{ $subscription->txn_id ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($subscription->status) }}</td>

                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $subscriptions->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
