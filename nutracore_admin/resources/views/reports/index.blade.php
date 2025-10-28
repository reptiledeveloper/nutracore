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
                    <li class="breadcrumb-item active" aria-current="page">Reports</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Reports</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex"> Sales-Summary-Report</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                        <form action="{{route('reports.sales')}}" method="">
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4" style="margin-top:20px">
                                    <button class="btn btn-primary" type="submit">Download</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>



        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Sales Register Tax Report</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                        <form action="{{route('reports.sales_register_tax_report')}}" method="">
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4" style="margin-top:20px">
                                    <button class="btn btn-primary" type="submit">Download</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">Supplier Bills</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                        <form action="{{route('reports.supplier_bill')}}" method="">
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="">
                                </div>
                                <div class="col-md-4" style="margin-top:20px">
                                    <button class="btn btn-primary" type="submit">Download</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
