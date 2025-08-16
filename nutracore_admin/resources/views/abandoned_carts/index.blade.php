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
                    <li class="breadcrumb-item active" aria-current="page">Abandoned Cart</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Abandoned Cart</div>
                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    @forelse($abandonedCarts as $userId => $items)
                        @php
                            $user = $items->first();
                            $totalAmount = $items->sum('line_total');
                            $lastAddedAt = $items->max('created_at');
                        @endphp

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between">
                                <div>
                                    <strong>{{ $user->user_name }}</strong> ({{ $user->user_email }})
                                    <br>
                                    Phone: {{ $user->user_phone }}
                                </div>
                                <div>
                                    <span class="badge bg-info">Total: ₹{{ number_format($totalAmount, 2) }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Line Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>₹{{ number_format($item->selling_price, 2) }}</td>
                                            <td>₹{{ number_format($item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <small class="text-muted">
                                    Last added at: {{ \Carbon\Carbon::parse($lastAddedAt)->format('d M Y H:i') }}
                                </small>

                                <div class="mt-2 text-end">
                                    <a href="{{ route('admin.purchase.from.cart', $userId) }}" class="btn btn-sm btn-primary">
                                        Purchase
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No abandoned carts found.</p>
                    @endforelse

                    {{ $abandonedCarts->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
