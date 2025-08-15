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
                    <li class="breadcrumb-item active" aria-current="page">Invoices</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show'])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Invoices</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('invoices.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Items</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($invoices as $inv)
                            <tr>
                                <td>{{ $inv->id }}</td>
                                <td>{{ $inv->invoice_number }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($inv->invoice_date)->format('d-M-Y') }}</td>
                                <td>{{ $inv->supplier?->name }}</td>
                                <td>{{ \App\Models\Stock::where('invoice_id',$inv->id)->count() }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getStatusStr($inv->status) }}</td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                               aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{route('invoices.show',$invoice.'?back_url='.$BackUrl)}}"
                                                   class="dropdown-item">Show</a>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $invoices->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
