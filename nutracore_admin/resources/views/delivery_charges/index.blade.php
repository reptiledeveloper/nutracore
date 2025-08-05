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
                    <li class="breadcrumb-item active" aria-current="page">Delivery Charges</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Delivery Charges</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('delivery_charges.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Vendor Name</th>
                            <th>From Order Amount</th>
{{--                            <th>From Sign</th>--}}
                            <th>To Order Amount</th>
{{--                            <th>To Sign</th>--}}
                            <th>Delivery Charge</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($delivery_charges)){
                            $i = 1;
                        foreach ($delivery_charges as $delivery_charge) {
                            ?>
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getVendorName($delivery_charge->vendor_id??'')??'' }}</td>
                            <td>{{$delivery_charge->order_amount??''}}</td>
{{--                            <td>{{$delivery_charge->sign??''}}</td>--}}
                            <td>{{$delivery_charge->order_amount2??''}}</td>
{{--                            <td>{{$delivery_charge->sign2??''}}</td>--}}
                            <td>{{$delivery_charge->delivery_charge??''}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($delivery_charge->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('delivery_charges.edit',$delivery_charge->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('delivery_charges.delete',$delivery_charge->id.'?back_url='.$BackUrl)}}"
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

                    {{ $delivery_charges->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
