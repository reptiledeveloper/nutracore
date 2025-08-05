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
                        <th>OrderID</th>
                        <th>Total amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($transactions)){
                    foreach ($transactions as $order) {
                        ?>
                    <tr>
                        <td># {{ $order->id ?? '' }}</td>

                        <td>{{$order->order_id??''}}</td>
                        <td>{{$order->net_amount??''}}</td>
                        <td>{!! \App\Helpers\CustomHelper::getStatusStr($order->status) !!}</td>
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
{{--                                        <a target="_blank"--}}
{{--                                           href="{{route('orders.view',$order->id.'?back_url='.$BackUrl)}}"--}}
{{--                                           class="dropdown-item">View</a>--}}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php }
                    } ?>

                    </tbody>
                </table>

                {{ $transactions->appends(request()->input())->links('pagination') }}


            </div>
        </div>
    </div>


    </div>

@endsection
