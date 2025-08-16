<?php
$BackUrl = \App\Helpers\CustomHelper::BackUrl();
$BackUrl = 'admin/users';
$routeName = \App\Helpers\CustomHelper::getAdminRouteName();
$current_route = Route::currentRouteName();
$image = \App\Helpers\CustomHelper::getImageUrl('users', $users->image ?? '');
?>


<div class="modal fade" id="walletModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('users.update_wallet')}}" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{$users->id}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Wallet Type</label>
                            <select class="form-control" name="wallet_type" onchange="get_wallet_type(this.value)">
                                <option value="" selected>Select</option>
{{--                                <option value="wallet">Wallet</option>--}}
                                <option value="cashback_wallet">NC Cash</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" placeholder="Enter Amount" name="amount" value="">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type">
                                <option value="" selected>Select</option>
                                <option value="CREDIT">CREDIT</option>
                                <option value="DEBIT">DEBIT</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Reamrks</label>
                            <input type="text" class="form-control" placeholder="Enter Reamrks" name="remarks" value="">
                        </div>
{{--                        <div class="col-md-12 mt-3" id="expire_date" style="display: none;">--}}
{{--                            <label class="form-label">Expire Date</label>--}}
{{--                            <input type="date" class="form-control" placeholder="Enter Amount" name="expire_date" value="">--}}
{{--                        </div>--}}

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
{{--                <h5 class="mb-1">Wallet : ₹ {{$users->wallet??0}}</h5>--}}
                <h5 class="mb-1">NC Cash : ₹ {{$users->cashback_wallet??0}}</h5>
                <small>User</small>
            </div>

            <div class="ms-md-auto">

                <div class="dropdown ms-auto">
                    <a class="btn btn-success" title="Wallet" data-bs-toggle="modal" data-bs-target="#walletModal"><i
                                class="fa fa-plus"></i></a>
                </div>

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
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'users.view' ? "active":""}}"
                       href="{{route('users.view',$users->id.'?back_url='.$BackUrl)}}">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'users.orders' ? "active":""}}"
                       href="{{route('users.orders',$users->id.'?back_url='.$BackUrl)}}">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{$current_route == 'users.transactions' ? "active":""}}"
                       href="{{route('users.transactions',$users->id.'?back_url='.$BackUrl)}}">Transactions</a>
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


