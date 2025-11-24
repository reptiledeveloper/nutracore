@php
    use App\Helpers\CustomHelper;
   $quoteData = CustomHelper::getQuotePorter($orders);
   $exist = DB::table('order_courier')->where("order_id",$orders->id)->where('envia_data','!=',null)->first();
$order_details_envia = [];
if(!empty($exist)){
       $order_details_envia_data = json_decode($exist->envia_data)??'';

       $order_details_envia = $order_details_envia_data->data[0] ?? [];
        $order_details_envia_error = $order_details_envia_data->error??'';
}

$couriers = DB::table('couriers')->get();
@endphp

@if(empty($exist) || !empty($order_details_envia_error))
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
                            $cdata = $ship_data->data ?? [];
                        @endphp

                        <tr>
                            <td>{{ $courier->description ?? '' }}</td>
                            <td>
                                @if(!empty($cdata))
                                    <form action="{{ route('orders.book_envia_shipment', ['id' => $orders->id]) }}"
                                          method="POST" id="form_{{ $courier->id }}">
                                        @csrf
                                        <div class="d-flex align-items-center gap-2">
                                            <select name="selected_service" class="form-select form-select-sm w-auto"
                                                    required
                                                    onchange="updateServiceDetails(this, '{{ $courier->id }}')">
                                                <option value="">Select Service</option>
                                                @foreach($cdata as $da)
                                                    <option value="{{ json_encode($da) }}">
                                                        {{ $da->serviceDescription ?? '' }} -
                                                        ₹{{ $da->totalPrice ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="details_{{ $courier->id }}" class="small text-muted"></div>
                                        </div>

                                        {{-- Hidden fields populated dynamically --}}
                                        <input type="hidden" name="service">
                                        <input type="hidden" name="price">
                                        <input type="hidden" name="carrier">
                                        <input type="hidden" name="courier">
                                        <input type="hidden" name="delivery_date">

                                        <button type="submit" class="btn btn-primary btn-sm mt-2" disabled>
                                            Book Selected Service
                                        </button>
                                    </form>
                                @else
                                    <em>No shipping options found</em>
                                @endif
                            </td>
                            <td class="text-end"></td>
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
            <label>TrackUrl : <a target="_blank" href="{{ $order_details_envia->trackUrl ?? '' }}">Click
                    Here</a></label><br>
            <label>Label : <a target="_blank" href="{{ $order_details_envia->label ?? '' }}">Print</a></label><br>
            <label>TotalPrice : {{ $order_details_envia->totalPrice ?? '' }}</label><br>
            <label>CurrentBalance : {{ $order_details_envia->currentBalance ?? '' }}</label><br>
        </div>
    </div>

@endif


@if(!empty($order_details_envia_error))
    <div class="row">
        <div class="col-md-12">
            <h3>Courier Details</h3>
            <label>Carrier : {{ $order_details_envia->carrier ?? '' }}</label><br>
            <label>Service : {{ $order_details_envia->service ?? '' }}</label><br>
            <label>ShipmentId : {{ $order_details_envia->shipmentId ?? '' }}</label><br>
            <label>TrackingNumber : {{ $order_details_envia->trackingNumber ?? '' }}</label><br>
            <label>TrackUrl : <a target="_blank" href="{{ $order_details_envia->trackUrl ?? '' }}">Click
                    Here</a></label><br>
            <label>Label : <a target="_blank" href="{{ $order_details_envia->label ?? '' }}">Print</a></label><br>
            <label>TotalPrice : {{ $order_details_envia->totalPrice ?? '' }}</label><br>
            <label>CurrentBalance : {{ $order_details_envia->currentBalance ?? '' }}</label><br>
        </div>
    </div>
    <h6 style="color: red;margin-top: 10px">Error : {{$order_details_envia_error->message??''}}</h6>
@endif

<script>
    function updateServiceDetails(selectEl, courierId) {
        const form = document.getElementById('form_' + courierId);
        const btn = form.querySelector('button[type="submit"]');
        const detailsDiv = document.getElementById('details_' + courierId);
        if (!selectEl.value) {
            btn.disabled = true;
            detailsDiv.innerHTML = '';
            return;
        }
        const data = JSON.parse(selectEl.value);
        // Fill hidden fields
        form.querySelector('[name="service"]').value = data.service || '';
        form.querySelector('[name="price"]').value = data.totalPrice || '';
        form.querySelector('[name="carrier"]').value = data.carrierDescription || '';
        form.querySelector('[name="courier"]').value = data.serviceDescription || '';
        form.querySelector('[name="delivery_date"]').value = data.deliveryDate?.date || '';

        // Show details below dropdown
        detailsDiv.innerHTML = `
        <div><strong>Delivery Date:</strong> ${data.deliveryDate?.date || 'N/A'}</div>
        <div><strong>Total Price:</strong> ₹${data.totalPrice || '0'}</div>`;
        btn.disabled = false;
    }
</script>
