@php
    use App\Helpers\CustomHelper;
    $address = DB::table('user_address')->where('id', $orders->address_id)->first();



    $order_courier = DB::table('order_courier')->where('order_id', $orders->id)->first();
    $pincode = $address->pincode ?? '';
    $check_delivery = CustomHelper::checkDelivery($pincode);
    $check_delivery = json_decode($check_delivery);
    $available_courier_companies = $check_delivery->data->available_courier_companies ?? '';

    $recommended_courier_company_id = $check_delivery->data->recommended_courier_company_id ?? '';

    $selected_courier = [];
    //if (!empty($available_courier_companies)) {
    //     foreach ($available_courier_companies as $company) {
    //        if ($recommended_courier_company_id == $company->courier_company_id) {
    //          $selected_courier = $company;
    //      }

    //  }
    //}
    // echo "<pre>";
    // print_r($selected_courier);
@endphp

<div class="row">
    <div class="col-md-6">
        <label>Courier Name</label>
        <select class="form-control" name="" onchange="selectCourier(this.value)">
            <option value="" selected>Select</option>
            <?php 
                 if (!empty($available_courier_companies)) {
    foreach ($available_courier_companies as $company) {?>
            <option value="{{ $company->courier_company_id ?? '' }}" {{ !empty($order_courier) && $order_courier->courier_id == $company->courier_company_id ? "selected" : "" }}>
                {{ $company->courier_name ?? '' }} - ETD :
                {{ $company->etd ?? '' }} - Freight Charge : {{ $company->freight_charge ?? '' }}
            </option>
            <?php    }
}
            
            ?>
        </select>
    </div>
    <!-- <div class="col-md-12">
        <label>ETD : {{ $selected_courier->etd ?? '' }}</label>
    </div>
    <div class="col-md-12">
        <label>Freight Charge : {{ $selected_courier->freight_charge ?? '' }}</label>
    </div>
    <div class="col-md-12">
        <label>RTO Charges : {{ $selected_courier->rto_charges ?? '' }}</label>
    </div>
    <div class="col-md-12">
        <label>Postcode : {{ $selected_courier->postcode ?? '' }}</label>
    </div> -->

</div>
<div class="ms-auto mt-3" id="bookBtn" style="display: {{!empty($order_courier->courier_id) ? "" : "none"}};">
    <a data-bs-toggle="modal" data-bs-target="#bookShipment" class="btn btn-primary">Book</i>
    </a>
    <!-- href="" -->
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