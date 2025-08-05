@php
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
@endphp

    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{$orders->id??''}}</title>
</head>
<style type="text/css">
    body {
        font-family: DejaVu Sans;
    }

    .d-flex {
        display: flex !important;
    }

    .text-left {
        text-align: left;
    }

    .linux-details {
        width: 100%;
        padding: 1px;
    }

    .linux-img {
        height: 100px;
        width: 300px;
        margin-left: 0%;
        float: left;
    }

    .text-center {
        text-align: center;
    }

    .td_class {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .geeks {
        font-size: 9px;
        border-spacing: 0 5px;
        border: 1px solid black;
        border-collapse: collapse;
    }

    .fs {
        font-size: 10px;
        padding: 3px;
    }

    .fs-12 {
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        word-wrap: break-word;
    }

    .row {
        margin-left: -5px;
        margin-right: -5px;
    }

    .column {
        float: left;
        width: 50%;
        padding: 1px;
    }

    .vertical {
        writing-mode: vertical-lr;
        transform: rotate(-90deg);
    }

    /* Clearfix (clear floats) */
    .row::after {
        content: "";
        clear: both;
        display: table;
    }

    .table_new {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
        border: 1px solid black;
        border-collapse: collapse;
    }

    .new_th, .new_td {
        text-align: left;
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }

    #footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    .fs-15 {
        font-size: 18px;
        font-weight: 600;
    }

    .table.table-custom {
        border-spacing: 0 15px;
        border-collapse: separate;
    }

    .table {
        min-width: 100%;
        margin-bottom: 0;
    }

    .mb-0 {
        margin-bottom: 0 !important;
    }
</style>
<?php
//$logo =
?>


<div class="row" style="">
    <div class='linux-details'>
        <div>
            <img
                src="data:image/png;base64,{{ base64_encode(file_get_contents("https://buybuycart.com/uploads/media/2023/BUYBUYCART_APP_LOGO1.png")) }}"
                class='linux-img'>
        </div>
        <div style="text-align:right;font-size: 20px">
            Mob. 9999999999
        </div>
    </div>
</div>
<table style="margin-top: 20px">
    <thead>
    <tr>
        <th style="text-align: left;font-weight: 500;font-size: 10px">
            <span>From</span><br>
            <strong>BuyBuy Technologies Private Limited</strong><br>
            <span>Email: cs@buybuycart.com</span><br>
            <span>Customer Care : 7669900247</span><br>
            <span><strong>GST NUMBER</strong> : 09AAJCB6878M1ZR</span><br>
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align: left;font-weight: 500;font-size: 10px">
            <span>Shipping Address</span><br>
            <strong>{{$orders->customer_name??''}}</strong><br>
            <span>{{ $orders->house_no ?? '' }} {{ $orders->appartment ?? '' }} {{ $orders->landmark ?? '' }} {{ $orders->location ?? '' }}</span><br>
            <strong>{{ $orders->contact_no ?? '' }}</strong><br>
            <strong>{{ $orders->email ?? '' }}</strong><br>
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align: left;font-weight: 500;font-size: 10px">
            <strong>Retail Invoice No :</strong> # BBC{{$orders->id}}<br>
            <strong>Date: </strong>{{ date('d M Y h:i A',strtotime($orders->created_at)) }}0<br>
            <strong>Payment Method : </strong>{{ strtoupper($orders->payment_method) }}<br>
        </th>

    </tr>
    <tr style="margin-top: 20px">
        <th style="text-align: left;font-weight: 500;font-size: 10px">
            <span>Sold By</span><br>
            <strong>BuyBuy Cart</strong><br>
            <span>Email: cs@buybuycart.com</span><br>
            <span>Customer Care : 7669900247</span><br>
        </th>
    </tr>
    </thead>
</table>


<h3>Product Details:</h3>

<div class="table-responsive">
    <table style="width: 100%">
        <thead>
        <tr style="background-color: #ccc1c1;text-align: center;font-size: 12px">
            <th>Sr No.</th>
            <th>Image</th>
            <th>Name</th>
            <th>Variants</th>
            <th>Price</th>
            <th>Tax (%)</th>
            <th>Qty</th>
            <th>SubTotal (₹)</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($order_items as $key => $value)
            @php
                $image = \App\Helpers\CustomHelper::getImageUrl('products', $value->image);
                $varients = \App\Helpers\CustomHelper::getVendorProductSingleVarients($orders->vendor_id, $value->product_id, $value->varient_id);

            @endphp
            <tr class="text-center" style="font-size: 12px;">
                <td>{{$key+1}}</td>
                <td>
                    <img class="product-img"
                         src="{{$image}}"
                         alt="" height="50px" width="50px">
                </td>
                <td>{{$value->product_name??''}}</td>
                <td> {{$varients->unit??''}} {{$varients->unit_value??''}}</td>
                <td>₹ {{$value->price??''}}</td>
                <td>0</td>
                <td>{{$value->qty??''}}</td>
                <td>₹ {{$value->net_price??''}}</td>
            </tr>

        @endforeach

        </tbody>
    </table>

    <hr>

    <div  style="float: right; margin-right:50px;font-size: 12px">
        <span>Sub Total : </span> ₹ {{$orders->order_amount??'0'}}<br>
        <span>Delivery Charges:  </span> {{$order->delivery_charges??''}}<br>
        <span>Tax(18%) :  </span> {{$order->delivery_charges??''}}<br>
        <span>Online Amount :  </span> {{$order->online_amount??''}}<br>
        <span>COD Amount :  </span> {{$order->cod_amount??''}}<br>
        <span>Wallet Amount :  </span> {{$order->wallet??''}}<br>
        <span>Total :</span> {{$orders->total_amount??'0'}}<br>
    </div>

</div>
