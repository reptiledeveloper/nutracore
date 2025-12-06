@extends('layouts.layout')
@section('content')
    <style>
        .dropdown-menu.show {
            overflow: hidden !important;
        }
    </style>
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
                    <li class="breadcrumb-item active" aria-current="page">NC Consult</li>
                </ol>
            </nav>
        </div>


        @include('layouts.filter',['search_show'=>'search_show','register_by_show'=>'register_by_show','is_ban_show'=>'is_ban_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All NC Consult</div>

                            <div class="dropdown ms-auto">
{{--                                <a href=""--}}
{{--                                   class="btn btn-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i>--}}
{{--                                </a>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Primary Goal</th>
                            <th>Current Weight</th>
                            <th>Target Weight</th>
                            <th>Diet Preference</th>
                            <th>Activity Level</th>
                            <th>Health Conditions</th>
                            <th>Consultation Mode</th>
                            <th>Preferred Date</th>
                            <th>Time Slot</th>
                            <th>Terms Agreed</th>
{{--                            <th class="text-end">Actions</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $item)
                            <tr>
                                <td>{{ $item->full_name }}</td>
                                <td>{{ $item->age }}</td>
                                <td>{{ ucfirst($item->gender) }}</td>
                                <td>{{ $item->mobile }}</td>
                                <td>{{ $item->email ?? '-' }}</td>

                                <td>{{ str_replace('_',' ', ucfirst($item->primary_goal)) }}</td>

                                <td>{{ $item->current_weight }} kg</td>
                                <td>{{ $item->target_weight }} kg</td>

                                <td>{{ str_replace('_',' ', ucfirst($item->diet_preference)) }}</td>

                                <td>{{ ucfirst($item->activity_level) }}</td>

                                <td>{{ $item->health_conditions ?? '-' }}</td>

                                <td>{{ ucfirst($item->consultation_mode) }}</td>

                                <td>{{ date('Y-m-d', strtotime($item->preferred_date)) }}</td>

                                <td>{{ $item->time_slot }}</td>

                                <td>
                                    @if($item->terms_agreed)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>

{{--                                <td class="text-end">--}}
{{--                                    <div class="dropdown ms-auto">--}}
{{--                                        <a href="#" data-bs-toggle="dropdown"--}}
{{--                                           class="btn btn-floating" aria-haspopup="true" aria-expanded="false">--}}
{{--                                            <i class="bi bi-three-dots"></i>--}}
{{--                                        </a>--}}
{{--                                        <div class="dropdown-menu dropdown-menu-end">--}}
{{--                                            <a href="#" data-bs-toggle="modal"--}}
{{--                                               class="dropdown-item">Update</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                            </tr>





                        @endforeach
                        </tbody>
                    </table>

                    {{ $users->appends(request()->input())->links('pagination') }}

                </div>
            </div>
        </div>
    </div>

@endsection
