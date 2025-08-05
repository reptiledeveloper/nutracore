@php
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
    $total_qty = 0;
    $total_discount = 0;
    $tax_val = 0;
    $total_cart_price = 0;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - ORD{{ $orders->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0 auto;
            width: 100%;
        }

        .container {
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line, .line-light {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            font-size: 12px;
        }

        .table th {
            background-color: #f9f9f9;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        .logo {
            height: 60px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .border {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="center">
        <img src="{{url('public/assets/images/logo.png')}}" class="logo" alt="Logo">
    </div>

    <div class="center bold mt-10">{{ $seller_details->name ?? '' }}</div>
    <div class="center bold mt-10">Invoice</div>
    <div class="center">{{ $seller_details->address ?? '' }}</div>
    <div class="center">GSTIN: {{ $seller_details->tax_number ?? '' }}</div>
    <div class="center">Email: {{ $seller_details->user_email ?? '' }}</div>
    <div class="center">Phone: {{ $seller_details->user_phone ?? '' }}</div>

    <div class="line"></div>

    <table class="table">
        <tr>
            <td>Name: {{ $orders->customer_name ?? '' }}</td>
            <td>Date: {{ date('d/m/Y', strtotime($orders->created_at)) }}</td>
        </tr>
        <tr>
            <td>Mobile: +91-{{ $orders->contact_no ?? '' }}</td>
            <td>Time: {{ date('h:i A', strtotime($orders->created_at)) }}</td>
        </tr>
        <tr>
            <td>Invoice No: ORD{{ $orders->id }}</td>
            <td></td>
        </tr>
    </table>

    <div class="line-light"></div>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>MRP</th>
            <th>Disc</th>
            <th>Net Amt</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order_items as $key => $value)
            @php
                $product = \App\Helpers\CustomHelper::getProductDeatils($value['product_id'] ?? '');
                $varients = \App\Helpers\CustomHelper::getVendorProductSingleVarients($orders->vendor_id, $value['product_id'], $value['variant_id']);
                $qty = $value['qty'] ?? 0;
                $price = $value['price'] ?? 0;
                $mrp = $varients->mrp ?? 0;
                $disc = (int)$mrp - (int)$price;
                $total_disc = $qty * $disc;
                $net_price = $value['net_price'] ?? 0;

                $total_qty += $qty;
                $total_discount += $total_disc;
                $total_cart_price += $net_price;

                if (!empty($product->tax) && $product->tax > 0) {
                    $tax_cal = $net_price / (1 + ($product->tax / 100));
                    $tax_val += $tax_cal;
                }
            @endphp
            <tr>
                <td class="center">{{ $key + 1 }}</td>
                <td>{{ $product->name ?? '' }} - {{ $varients->unit ?? '' }} {{ $varients->unit_value ?? '' }}<br>
                    HSN: {{ $product->sku ?? '' }} {{ (int)$product->tax > 0 ? "GST $product->tax%" : "" }}
                </td>
                <td class="center">{{ $qty }}</td>
                <td class="center">{{ $mrp }}</td>
                <td class="center">{{ $total_disc }}</td>
                <td class="center">{{ $net_price }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="line-light"></div>

    <table class="table">
        <tr>
            <td>ITEM TOTAL:</td>
            <td class="text-right">{{ $orders->order_amount ?? 0 }}</td>
        </tr>
        <tr>
            <td>Delivery Charges:</td>
            <td class="text-right">{{ $orders->delivery_charges ?? 0 }}</td>
        </tr>
        <tr>
            <td>Handling Charges:</td>
            <td class="text-right">{{ $orders->handling_charges ?? 0 }}</td>
        </tr>
        <tr>
            <td>Surge Fee:</td>
            <td class="text-right">{{ $orders->surge_fee ?? 0 }}</td>
        </tr>
        <tr>
            <td>Platform Fee:</td>
            <td class="text-right">{{ $orders->platform_fee ?? 0 }}</td>
        </tr>
        <tr>
            <td>Rain Fee:</td>
            <td class="text-right">{{ $orders->rain_fee ?? 0 }}</td>
        </tr>
        <tr>
            <td>Small Cart Fee:</td>
            <td class="text-right">{{ $orders->small_cart_fee ?? 0 }}</td>
        </tr>
        <tr>
            <td>SUB TOTAL:</td>
            <td class="text-right total">{{ $orders->total_amount ?? 0 }}</td>
        </tr>
    </table>

    <div class="line-light"></div>

    <div class="center">NO OF QTY : {{ $total_qty }}</div>
    <div class="center mt-10">You Saved Rs. : {{ $total_discount }}</div>

    <div class="line-light"></div>

    <div class="text-left">
        {{ \App\Helpers\CustomHelper::numberToWords($orders->total_amount ?? 0) }}
    </div>
    <div class="text-left">Prices are inclusive of all taxes - Place of Supply: Uttar Pradesh</div>

    <div class="center bold mt-10">TAX SUMMARY</div>

    @php
        $tax = number_format($total_cart_price - $tax_val, 2);
    @endphp

    <table class="table">
        <tr>
            <th class="border">TAXABLE VALUE</th>
            <th class="border">CGST</th>
            <th class="border">SGST</th>
            <th class="border">Cess</th>
            <th class="border">IGST</th>
        </tr>
        <tr>
            <td class="border">{{ number_format($total_cart_price - $tax, 2) }}</td>
            <td class="border">{{ number_format($tax / 2, 2) }}</td>
            <td class="border">{{ number_format($tax / 2, 2) }}</td>
            <td class="border">0.00</td>
            <td class="border">0.00</td>
        </tr>
    </table>

    <div class="line-light"></div>

    <div class="bold mt-10">Customer Details</div>
    <div>Address: Gautambuddha Naga</div>

    <div class="line-light"></div>

    <div class="bold mt-10">Terms & Conditions</div>

    <div class="center mt-10">Thank you for shopping with us!</div>

    <div class="mt-10">
        <span class="text-left">Printed On: {{ now()->format('d/m/Y h:i A') }}</span>
        <span class="text-right" style="float: right;">E & O E</span>
    </div>
</div>
</body>
</html>
