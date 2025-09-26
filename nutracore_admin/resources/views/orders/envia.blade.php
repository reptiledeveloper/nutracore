@php
    use App\Helpers\CustomHelper;
   $quoteData = CustomHelper::getQuotePorter($orders);
   $exist = DB::table('order_courier')->where("order_id",$orders->id)->where('envia_data','!=',null)->first();
$order_details_envia = [];
if(!empty($exist)){
       $order_details_envia = json_decode($exist->envia_data)??'';

       $order_details_envia = $order_details_envia->data[0] ?? [];

}

$couriers = DB::table('couriers')->get();
@endphp

@if(empty($exist))

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="orders">
                    <thead>
                    <tr>
                        <th>Courier Name</th>
                        <th>Details</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($couriers as $courier)
                        @php
                            $ship_data = CustomHelper::getquoteEnvia($orders, $courier->name);
//                            echo "<pre>";
//                            print_r($ship_data);
                            $cdata = [];
                            if(!empty($ship_data->data)){
                                $cdata = $ship_data->data;
                            }
                        @endphp

                        <tr>
                            <td>{{ $courier->description ?? '' }}</td>
                            <td colspan="2"> {{-- Merge details + actions into one column --}}
                                @foreach($cdata as $da)
                                    <div class="mb-3 p-2 border rounded">
                                        <label><strong>Courier:</strong> {{ $da->serviceDescription ?? '' }}</label><br>
                                        <label><strong>Delivery Date:</strong> {{ $da->deliveryDate->date ?? '' }}
                                        </label><br>
                                        <label><strong>Total Price:</strong> {{ $da->totalPrice ?? '' }}</label><br>

                                        {{-- Book button for this specific service --}}
                                        <form action="{{route('orders.book_envia_shipment',['id'=>$orders->id])}}"
                                              method="POST">
                                            @csrf
                                            <input type="hidden" name="service" value="{{ $da->service }}">
                                            <input type="hidden" name="price" value="{{ $da->totalPrice }}">
                                            <input type="hidden" name="carrier" value="{{ $da->carrierDescription }}">
                                            <input type="hidden" name="courier"
                                                   value="{{ $da->serviceDescription ?? '' }}">
                                            <input type="hidden" name="delivery_date"
                                                   value="{{ $da->deliveryDate->date ?? '' }}">
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">
                                                Book {{ $da->serviceDescription }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@else
    <div class="row">
        <div class="col-md-12">
            <h3>Courier Details</h3>
            <label>Carrier : {{ $order_details_envia->carrier ?? '' }}</label><br>
            <label>Service : {{ $order_details_envia->service ?? '' }}</label><br>
            <label>ShipmentId : {{ $order_details_envia->shipmentId ?? '' }}</label><br>
            <label>TrackingNumber : {{ $order_details_envia->trackingNumber ?? '' }}</label><br>
            <label>TrackUrl : <a target="_blank" href="{{ $order_details_envia->trackUrl ?? '' }}">Click Here</a></label><br>
            <label>Label : <a target="_blank" href="{{ $order_details_envia->label ?? '' }}">Print</a></label><br>
            <label>TotalPrice : {{ $order_details_envia->totalPrice ?? '' }}</label><br>
            <label>CurrentBalance : {{ $order_details_envia->currentBalance ?? '' }}</label><br>
        </div>
    </div>

@endif


