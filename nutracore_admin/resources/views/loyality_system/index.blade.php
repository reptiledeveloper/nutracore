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
                    <li class="breadcrumb-item active" aria-current="page">LoyalitySystem</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter', ['search_show' => 'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All LoyalitySystem</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('loyality_system.add', ['back_url' => $BackUrl]) }}"
                                    class="btn btn-primary"><i class="fa fa-plus"></i></a>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>From Amount</th>
                                <th>To Amount</th>
                                <th>Cashback(in %)</th>
                                <th>Additional Perk</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($loyality_system)) {
        foreach ($loyality_system as $cat) {
                                ?>
                            <tr>
                                <td>{{$cat->id ?? ''}}</td>
                                <td>{{ $cat->title ?? '' }}</td>
                                <td>{{ $cat->type == "not_subscribe" ? "NonMember" : "Member" }}</td>
                                <td>{{ $cat->from_amount ?? '' }}</td>
                                <td>{{ $cat->to_amount ?? '' }}</td>
                                <td>{{ $cat->cashback ?? '' }}</td>
                                <td>{{ $cat->additional_perk ?? '' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getStatusStr($cat->status) }}</td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{route('loyality_system.edit', $cat->id . '?back_url=' . $BackUrl)}}"
                                                    class="dropdown-item">Edit</a>
                                                <a href="{{route('loyality_system.delete', $cat->id . '?back_url=' . $BackUrl)}}"
                                                    onclick="return confirm('Are you Want To Delete?')"
                                                    class="dropdown-item">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php    }
    }?>

                        </tbody>
                    </table>

                    {{ $loyality_system->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection