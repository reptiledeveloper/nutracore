@php
    use App\Helpers\CustomHelper;
   $quoteData = CustomHelper::getQuotePorter($orders);
   $exist = DB::table('order_courier')->where("order_id",$orders->id)->first();
$couriers = DB::table('couriers')->get();
@endphp


<div class="row">
    <div class="col-md-12">
        @foreach($couriers as $courier)
            @php
                $ship_data = CustomHelper::getquoteEnvia($orders, $courier->name);
                $cdata = [];
                if(!empty($ship_data->data)){
                    $cdata = $ship_data->data;
                }
            @endphp

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ $courier->description ?? '' }}</label>
                </div>
                <div class="col-md-6">
                    @foreach($cdata as $da)
                        <label>Courier : {{ $da->carrierDescription ?? '' }}</label><br>
                        <label>Delivery Date : {{ $da->deliveryDate->date ?? '' }}</label><br>
                        <label>Total Price : {{ $da->totalPrice ?? '' }}</label><br>
                    @endforeach
                </div>

            </div>
        @endforeach
    </div>
</div>

