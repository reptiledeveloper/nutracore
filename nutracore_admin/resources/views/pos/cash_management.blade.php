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
                    <li class="breadcrumb-item active" aria-current="page">Cash management</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Cash management List</div>

                            <div class="dropdown ms-auto">
{{--                                <a href="{{ route('pos.add', ['back_url' => $BackUrl]) }}"--}}
{{--                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Date</th>
                            <th>Store Name</th>
                            <th>Opening Balance</th>
                            <th>Closing Balance</th>
                            <th>Closing Note</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($pos_daily_cash) && count($pos_daily_cash) > 0)
                            @foreach($pos_daily_cash as $po)
                                <tr>
                                    <td>{{ $po->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->date)->format('d-m-Y') }}</td>
                                    <td>{{ \App\Helpers\CustomHelper::getVendorName($po->store_id??'') }}</td>
                                    <td>{{ $po->today_balance??0 }}</td>
                                    <td>{{ $po->today_last_balance??0 }}</td>
                                    <td>{{ $po->closing_note??'' }}</td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="15" class="text-center text-muted">No records found</td>
                            </tr>
                        @endif
                        </tbody>

                    </table>

                    {{ $pos_daily_cash->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
