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
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Abandoned Cart</div>
                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>

                @include('layouts.filter',['search_show'=>'search_show'])


                <div class="table-responsive">
                    @forelse($abandonedCarts as $carts)
                        @php
                            $user = \App\Models\User::where('id',$carts->userID)->first();
                            $cart_items = \App\Models\OrderItems::where('order_id',$carts->id)->get();
                        @endphp
                        <div class="card mb-3 mt-3">
                            <div class="card-header d-flex justify-content-between">
                                <div>
                                    <strong>{{ $user->name ??"Guest"}}</strong> ({{ $user->email }})<br>
                                    Phone: {{ $user->phone }}
                                </div>
                                <div>
                                    <span
                                        class="badge bg-info">Total: ₹{{ number_format($carts->total_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th> Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cart_items as $item)
                                        @php
                                            $product = \App\Models\Products::where('id',$item->product_id)->first();
                                            $varients = \App\Models\ProductVarient::where('id',$item->variant_id)->first();

                                        @endphp
                                        <tr>
                                            <td>{{ $product->name ??''}}</td>
                                            <td>{{ $varients->unit??'' }}</td>
                                            <td>{{ $item->qty ??0}}</td>
                                            <td>₹{{ number_format($item->price, 2) }}</td>
                                            <td>₹{{ number_format($item->net_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <small class="text-muted">
                                    Last added
                                    at: {{ \Carbon\Carbon::parse($carts->updated_at)->format('d M Y H:i') }}
                                </small>

                                <div class="mt-2 text-end">
                                    <a href="" class="btn btn-sm btn-primary">
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
