@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    ?>
    @include('nc_partners.common',['nc_partners'=>$nc_partners])


    @include('snippets.errors')
    @include('snippets.flash')
    <div class="card mt-3">
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-custom table-lg mb-0" id="commissions">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Order Amount</th>
                                <th>Total Sales (Month)</th>
                                <th>Commission %</th>
                                <th>Commission Earned</th>
                                <th>Settlement Status</th>
                                <th>Date Recorded</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Assuming $commissions is the collection of partner_commissions records
                            if (!empty($commissions)) {
                            foreach ($commissions as $commission) {
                                // Fetch related data (replace with eager loading or efficient queries if needed)
                                ?>
                            <tr>
                                <td># {{ $commission->id ?? '' }}</td>

                                {{-- Order ID (linking back to the original order) --}}
                                <td>
                                    @if(!empty($commission->order_id))
                                        <a href="{{ route('orders.view', $commission->order_id) }}?back_url={{ $BackUrl ?? '' }}">
                                            {{ $commission->order->unique_id??'' }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                {{-- Date associated with the order --}}
                                <td>{{ date('d M Y', strtotime($commission->date ?? '')) }}</td>

                                {{-- Order Amount --}}
                                <td>₹ {{ number_format($commission->order_amount ?? 0, 2) }}</td>

                                {{-- Total Sales (for Tier Determination) --}}
                                <td>₹ {{ number_format($commission->total_order_amount_till_date ?? 0, 2) }}</td>

                                {{-- Commission Percentage --}}
                                <td>{{ $commission->commission_percent ?? 0 }}%</td>

                                {{-- Commission Amount --}}
                                <td><strong>₹ {{ number_format($commission->commission ?? 0, 2) }}</strong></td>

                                {{-- Settlement Status --}}
                                <td>
                                    @if($commission->is_setteled == 1)
                                        <span class="badge bg-success">Settled</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>

                                {{-- Commission Record Creation Date --}}
                                <td>{{ date('d M Y h:i A', strtotime($commission->created_at)) }}</td>


                            </tr>
                            <?php }
                            } ?>
                            </tbody>
                        </table>

                        {{-- Ensure your pagination variable is correct --}}
                        {{ $commissions->appends(request()->input())->links('pagination') }}


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection
