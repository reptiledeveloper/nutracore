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
                    <li class="breadcrumb-item active" aria-current="page">Notification</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Notification</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('notifications.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Target User</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php if (!empty($campaigns)){
                            $i = 1;
                        foreach ($campaigns as $campaign){
                            $file = \App\Helpers\CustomHelper::getImageUrl('notification', $campaign->image);
                            ?>
                        <tr>
                            <td>
                                {{$i++}}
                            </td>
                            <td class="text-wrap">
                                {{$campaign->title??''}}
                            </td>
                            <td class="text-wrap">
                                {{$campaign->description??''}}
                            </td>
                            <td>
                                @if($campaign->user_type == 'seller')
                                    Seller
                                @elseif($campaign->user_type == 'user')
                                   User
                                @else

                                @endif
                            </td>
                            <td>
                                <a href="{{$file}}" target="_blank"><img src="{{$file}}" height="50px"
                                                                         width="100px"></a>
                            </td>
                            <td>{{\App\Helpers\CustomHelper::getStatusStr($campaign->status)}}
                            </td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('notifications.send',$campaign->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Send</a>
                                            <a href="{{route('notifications.edit',$campaign->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('notifications.delete',$campaign->id.'?back_url='.$BackUrl)}}"
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

                    {{ $campaigns->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
