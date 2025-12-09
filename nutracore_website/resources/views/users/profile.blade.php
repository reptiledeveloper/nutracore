@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $dob = date('Y-m-d', strtotime($user->dob));
    $subscription_data = CustomHelper::subscriptionsData($user);
    $active_loyality = $subscription_data['active_loyalty'] ?? '';

    ?>
    <style>
        .card-section {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 12px;
        }

        .icon img {
            width: 40px;
            height: 40px;
        }

        .text {
            margin-left: 12px;
        }

        .text .title {
            font-weight: bold;
            color: #d4a200;
            font-size: 16px;
        }

        .text .subtitle {
            color: #8c6d1f;
            font-size: 12px;
        }

        .divider {
            width: 1px;
            height: 50px;
            background-color: #e0d4b8;
        }

        .arrow {
            margin-left: auto;
            font-size: 20px;
            color: #d4a200;
        }
        .membership-card {
            display: flex;
            background: #fff7e1;
            border-radius: 10px;
            padding: 12px;
            align-items: center;
            justify-content: space-between;
        }

        .membership-card a {
            width: 50%; /* 🔥 EXACT HALF */
            text-decoration: none;
        }

        .card-section {
            display: flex;
            align-items: center;
        }

        .icon img {
            width: 40px;
            height: 40px;
        }

        .text {
            margin-left: 12px;
        }

        .text .title {
            font-weight: bold;
            color: #d4a200;
            font-size: 16px;
        }

        .text .subtitle {
            color: #8c6d1f;
            font-size: 12px;
            line-height: 16px;
        }

        .divider {
            width: 1px;
            height: 50px;
            background-color: #e0d4b8;
        }

    </style>

    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> My Account
                </div>
            </div>
        </div>
        <div class="page-content pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="userForm" action="" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="text-center mb-4 position-relative"
                                         style="width: 150px; margin: 0 auto;">
                                        <!-- Profile Image -->
                                        <img src="{{CustomHelper::getImageUrl('users',$user->image)}}"
                                             alt="Profile Image"
                                             class="rounded-circle img-fluid"
                                             style="width: 150px; height: 150px; object-fit: cover;">

                                        <!-- Edit Icon -->
                                        <label for="profileImageInput" class="position-absolute"
                                               style="bottom: 0; right: 0; background: #fff; border-radius: 50%; padding: 8px; cursor: pointer;">
                                            <i class="fa fa-edit" style="font-size: 18px; color: #000;"></i>
                                        </label>

                                        <!-- Hidden File Input -->
                                        <input type="file" id="profileImageInput" name="image" accept="image/*"
                                               class="d-none">
                                    </div>

                                    <div class="row mt-10">
                                        <div class="membership-card">

                                            <a href="{{route('nutrapass')}}">
                                                <div class="card-section">
                                                    <div class="icon">
                                                        <img src="{{url('public/assets/member.svg')}}" alt="Gold Icon">
                                                    </div>
                                                    <div class="text">
                                                        <div class="title">{{$active_loyality->title??''}}</div>
                                                        <div class="subtitle">
                                                            Valid till<br>
                                                            {{date('d M Y',strtotime($user->subscription_end))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="divider"></div>

                                            <a href="{{route('nc_cash')}}">
                                                <div class="card-section">
                                                    <div class="icon">
                                                        <img src="{{url('public/assets/coin.svg')}}" alt="Points Icon">
                                                    </div>
                                                    <div class="text">
                                                        <div class="title">{{$user->cashback_wallet??0}}</div>
                                                        <div class="subtitle">NC Cash</div>
                                                    </div>
                                                </div>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="row mt-10">
                                        <div class="col-md-6 mb-2">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Name"
                                                   value="{{ $user->name ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="Email"
                                                   value="{{ $user->email ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control" disabled
                                                   placeholder="Phone" value="{{ $user->phone ?? '' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Date of Birth</label>
                                            <input type="date" name="dob" class="form-control" value="{{ $dob ?? '' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Anniversary</label>
                                            <input type="date" name="anniversary" class="form-control"
                                                   value="{{ $user->anniversary ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>

                            </div>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileInput = document.getElementById('profileImageInput');
            const profileImg = document.querySelector('.rounded-circle');

            profileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        profileImg.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>

@endsection
