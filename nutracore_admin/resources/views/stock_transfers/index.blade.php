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
                    <li class="breadcrumb-item active" aria-current="page">Attributes</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Attributes</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('stock_transfers.add', ['back_url' => $BackUrl]) }}"
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
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Batch</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($transfers as $i => $t)
                            <tr>
                                <td>{{ $transfers->firstItem() + $i }}</td>
                                <td>{{ $t->stock?->product->name??'' }}</td>
                                <td>{{ $t->stock?->varient->unit??'' }}</td>
                                <td>{{ $t->stock?->batch_no }}</td>
                                <td>{{ $t->from_location }}</td>
                                <td>{{ $t->to_location }}</td>
                                <td>{{ $t->quantity }}</td>
                                <td><span class="badge bg-{{ $t->status=='pending'?'warning':'success' }}">{{ ucfirst($t->status) }}</span></td>
                                <td class="text-nowrap">
                                    @if($t->status=='pending')
                                        <form class="d-inline" method="POST" action="{{ route('stock_transfers.approve',$t->id) }}">
                                            @csrf <button class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form class="d-inline" method="POST" action="{{ route('stock_transfers.reject',$t->id) }}">
                                            @csrf <button class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $transfers->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
