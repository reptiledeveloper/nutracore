@php
    use App\Helpers\CustomHelper;
   $quoteData = CustomHelper::getQuotePorter($orders);

   $exist = DB::table('order_courier')->where("order_id",$orders->id)->first();
   //$track_order = CustomHelper::trackPorterOrder($exist);
   $exist = DB::table('order_courier')->where("order_id",$orders->id)->first();
@endphp

<div class="row">

    <div class="col-md-6">
        <div class="col-md-12">
            <label>Price : {{ $quoteData["price"] ?? '' }}</label>
        </div>
        <div class="col-md-12">
            <label>ETA : {{ $quoteData["eta"] ?? '' }}</label>
        </div>
        <div class="col-md-12">
            <label>Tracking URL : <a href="{{ $exist->tracking_url ?? '' }}" target="_blank">Click Here</a></label>
        </div>
        <div class="col-md-12">
            <label>Porter Order ID : {{ $exist->porter_order_id ?? '' }}</label>
        </div>
        <div class="col-md-12">
            <label>Estimated Pickup Time : {{ $exist->estimated_pickup_time ?? '' }}</label>
        </div>
        @if(empty($exist))
            <div class="ms-auto mt-3" id="bookBtn">
                <a href="{{route('orders.book_porter',['order_id'=>$orders->id])}}" class="btn btn-primary">Book</i>
                </a>

            </div>
        @else
            <div class="ms-auto mt-3" id="bookBtn">
                <a href="{{route('orders.cancel_porter',['order_id'=>$orders->id])}}" class="btn btn-danger">Cancel</i>
                </a>

            </div>
        @endif

    </div>
    <div class="col-md-6">
        @if(!empty($exist->order_details_porter))
            @php
                $order_details_porter = json_decode($exist->order_details_porter??'');
            @endphp
            <div class="col-md-12">
                <h3>Partner Details</h3>
                <label>Name : {{ $order_details_porter->partner_info->name ?? '' }}</label><br>
                <label>Vehicle No : {{ $order_details_porter->partner_info->vehicle_number ?? '' }}</label><br>
                <label>Vehicle Type : {{ $order_details_porter->partner_info->vehicle_type ?? '' }}</label><br>
                <label>Mobile No : {{ $order_details_porter->partner_info->mobile->mobile_number ?? '' }}</label><br>
                <label>Sec Mobile No
                    : {{ $order_details_porter->partner_info->partner_secondary_mobile->mobile_number ?? '' }}</label><br>
            </div>

            <div class="col-md-12">
                <h3>Order Timmings Details</h3>
                <label>Pickup Time : {{ $order_details_porter->order_timings->pickup_time ?? '' }}</label><br>
                <label>Accept Time : {{ $order_details_porter->partner_info->order_accepted_time ?? '' }}</label><br>
                <label>Order Started Time
                    : {{ $order_details_porter->partner_info->order_started_time ?? '' }}</label><br>
                <label>Order End Time
                    : {{ $order_details_porter->partner_info->mobile->order_ended_time ?? '' }}</label><br>
            </div>
            <div class="col-md-12">
                <h3>Fare Details</h3>
                <label>Fare
                    : {{ $order_details_porter->fare_details->estimated_fare_details->minor_amount ?? '' }}</label><br>
            </div>
        @endif
    </div>
</div>


<div class="modal fade" id="bookShipment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Book Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.bookshipment_shiprocket', ['id' => $orders->id]) }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{$orders->id}}">
                <input type="hidden" name="courier_id" id="courier_id" value="{{ $order_courier->courier_id ?? '' }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Length(in CM)</label>
                            <input type="text" class="form-control" name="length"
                                   value="{{ $order_courier->length ?? '' }}">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Breadth(in CM)</label>
                            <input type="text" class="form-control" name="breadth"
                                   value="{{ $order_courier->breadth ?? '' }}">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Height(in CM)</label>
                            <input type="text" class="form-control" name="height"
                                   value="{{ $order_courier->height ?? '' }}">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Weight(in KG)</label>
                            <input type="text" class="form-control" name="weight"
                                   value="{{ $order_courier->weight ?? '' }}">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function selectCourier(courier_id) {
        $('#bookBtn').hide();
        if (courier_id != '') {
            $('#bookBtn').show();
            $('#courier_id').val(courier_id);
        }

    }
</script>
