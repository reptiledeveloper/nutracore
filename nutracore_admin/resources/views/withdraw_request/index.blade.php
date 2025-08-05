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
                    <li class="breadcrumb-item active" aria-current="page">Withdraw Request</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Withdraw Request</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($withdraw_request)){
                        foreach ($withdraw_request as $withdraw) {
                            $agent = \App\Models\DeliveryAgents::find($withdraw->agent_id);
                            $image = \App\Helpers\CustomHelper::getImageUrl('agents', $agent->image??'');
                            ?>
                        <tr>
                            <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                                                          alt="" /></a></td>
                            <td>{{ $agent->name ?? '' }} </td>
                            <td>{{$agent->phone??''}}</td>
                            <td>{{$withdraw->net_amount??''}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getPaymentStatusStr($withdraw->status) }}</td>
                            <td>{{$withdraw->remarks??''}}</td>

                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a data-bs-toggle="modal" data-bs-target="#walletModal{{$withdraw->id}}"
                                               class="dropdown-item">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="walletModal{{$withdraw->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{route('withdraw_request.update_status')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="withdraw_id" value="{{$withdraw->id}}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control" name="status">
                                                        <option value="" selected>Select</option>
                                                        <option value="1">Paid</option>
                                                        <option value="2">Reject</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Remarks</label>
                                                    <textarea class="form-control" name="remarks"></textarea>
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


                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $withdraw_request->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
