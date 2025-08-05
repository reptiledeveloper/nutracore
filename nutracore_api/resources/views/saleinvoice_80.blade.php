@php
    $height = 250;
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct1($orders->id);
    
    $count = count($order_items);
    if ($count > 0) {
        $height1 = $count * 32;
        if ($height1 > $height) {
            $height = $height1;
        }
    }
    $total_qty = 0;
    $total_discount = 0;

    $tax_val = 0;
    $total_cart_price = 0;
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 80mm;
            /* Thermal receipt width */
        }

        @page {
            size: 90mm {{ $height }}mm;
            /* 80mm width, auto height */
            margin: 5mm;
            /* Adjust margins for proper spacing */
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 2px dashed black;
            margin: 5px 0;
        }

        .line-light {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            padding: 2px;
        }

        .total {
            font-size: 14px;
            font-weight: bold;
        }

        .linux-img {
            height: 50px;
            width: 204px;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="center">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://buybuycart.com/uploads/media/2023/BUYBUYCART_APP_LOGO1.png')) }}"
                class='linux-img'>
        </div>
        <div class="center bold" style="margin-top: 10px">{{ $seller_details->name ?? '' }}</div>
        <div class="center bold" style="margin-top: 10px;font-weight: bolder">Invoice</div>
        <div class="center">{{ $seller_details->address ?? '' }}
        </div>
        <div class="center">GSTIN NO : {{ $seller_details->tax_number ?? '' }}</div>
        <div class="center">Email : {{ $seller_details->user_email ?? '' }}</div>
        <div class="center">Phone No : {{ $seller_details->user_phone ?? '' }}</div>

        <div class="line bold"></div>

        <table class="table">
            <tr>
                <th style="text-align: left;">
                    Name : {{ $orders->customer_name ?? '' }}
                </th>
                <th style="text-align: left;">
                    Date : {{ date('d/m/Y', strtotime($orders->created_at)) ?? '' }}
                </th>
            </tr>
            <tr>
                <th style="text-align: left;">
                    Mobile : +91-{{ $orders->contact_no ?? '' }}
                </th>
                <th style="text-align: left;">
                    Time : {{ date('h:i A', strtotime($orders->created_at)) ?? '' }}
                </th>
            </tr>
            <tr>
                <th style="text-align: left;">
                    Invoice No: ORD{{ $orders->id }}
                </th>

            </tr>
        </table>

        <div class="line-light"></div>

        <table class="table">
            <thead>
                <tr style="font-weight: 600;">
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>MRP</th>
                    <th>Disc</th>
                    <th>Net Amt</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px dashed black;">
                @foreach ($order_items as $key => $value)
                    <?php 
               
                        $product = \App\Helpers\CustomHelper::getProductDeatils($value['product_id'] ?? '');
                        if(!empty($product)){
                        $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image ?? '');
                        $varients = \App\Helpers\CustomHelper::getVendorProductSingleVarients(
                            $orders->vendor_id,
                            $value['product_id'],
                            $value['variant_id'],
                        );
                        $qty = $value['qty'] ?? 0;
                        $price = $value['price'] ?? 0;

                        $mrp = $varients->mrp ?? 0;
                        $disc = (int) $mrp - (int) $price;
                        $total_disc = (int) $qty * (int) $disc;
                        $total_qty += $qty;
                        $total_discount += $total_disc;
                        $net_price = $value['net_price'] ?? 0;
                        $total_cart_price += $net_price;
                        if (!empty($product) && $product->tax > 0) {
                            $tax_cal = $net_price / (1 + $product->tax / 100);
                            $tax_val += $tax_cal;
                        }

                   ?>
                    <tr style="text-align:center">
                        <td>{{ $key + 1 }}</td>
                        <td style="text-align: left">{{ $product->name ?? '' }} - {{ $varients->unit ?? '' }}
                            {{ $varients->unit_value ?? '' }} <br>
                            HSN: {{ $product->sku ?? '' }} {{ (int) $product->tax > 0 ? "GST $product->tax%" : '' }}
                        </td>
                        <td>{{ $value['qty'] ?? '' }}</td>
                        <td>{{ $varients->mrp ?? '' }}</td>
                        <td>
                            {{ $total_disc ?? 0 }}

                        </td>
                        <td>{{ $value['net_price'] ?? '' }}</td>
                    </tr>
                    <?php }?>
                @endforeach
            </tbody>
        </table>

        <div class="line-light"></div>

        <table class="table">
            <tr>
                <td>ITEM TOTAL:</td>
                <td class="bold" style="text-align: right">{{ $orders->order_amount ?? 0 }}</td>
            </tr>
            <tr>
                <td>Delivery Charges:</td>
                <td class="bold" style="text-align: right">{{ $orders->delivery_charges ?? 0 }}</td>
            </tr>
            <tr>
                <td>Handling Charges:</td>
                <td class="bold" style="text-align: right">{{ $orders->handling_charges ?? 0 }}</td>
            </tr>
            <tr>
                <td>Surge Fee:</td>
                <td class="bold" style="text-align: right">{{ $orders->surge_fee ?? 0 }}</td>
            </tr>
            <tr>
                <td>Platform Fee:</td>
                <td class="bold" style="text-align: right">{{ $orders->platform_fee ?? 0 }}</td>
            </tr>
            <tr>
                <td>Rain Fee:</td>
                <td class="bold" style="text-align: right">{{ $orders->rain_fee ?? 0 }}</td>
            </tr>
            <tr>
                <td>Small Cart Fee:</td>
                <td class="bold" style="text-align: right">{{ $orders->small_cart_fee ?? 0 }}</td>
            </tr>
            <tr>
                <td>SUB TOTAL:</td>
                <td class="bold" style="text-align: right">{{ $orders->total_amount ?? 0 }}</td>
            </tr>
        </table>

        <div class="line-light"></div>

        <div style="text-align: center">
            NO OF QTY : {{ $total_qty ?? 0 }}
        </div>
        <div style="margin-top: 10px;text-align: center">
            You Saved Rs. : {{ $total_discount ?? 0 }}

        </div>
        <div class="line-light"></div>

        <div style="text-align: left">
            {{ \App\Helpers\CustomHelper::numberToWords($orders->total_amount ?? 0) }}
        </div>
        <div style="text-align: left">
            Prices are inclusive of all taxes - Place of Supply :
            Uttar Pradesh
        </div>
        <div style="text-align: center">
            TAX SUMMARY
        </div>

        <style>
            .border {
                border: 1px solid black;
            }
        </style>


        <table style="width: 100%;text-align: center;border-collapse: collapse;">
            <tr>
                <th class="border">
                    TAXABLE VALUE
                </th>
                <th class="border">
                    CGST
                </th>
                <th class="border">
                    SGST
                </th>
                <th class="border">
                    Cess
                </th>
                <th class="border">
                    IGST
                </th>
            </tr>
            @php
                $tax = number_format($total_cart_price - $tax_val, 2);
            @endphp
            <tr>
                <td class="border">{{ number_format((float) $total_cart_price - (float) $tax, 2) }}</td>
                <td class="border">{{ number_format((float) $tax / 2, 2) }}</td>
                <td class="border">{{ number_format((float) $tax / 2, 2) }}</td>
                <td class="border">0.00</td>
                <td class="border">0.00</td>
            </tr>
        </table>
        <div class="line-light"></div>
        <div>
            Customer Details

        </div>

        <div>
            Address : ,Gautambuddha Naga
        </div>

        <div class="line-light"></div>
        <div>
            T & C
        </div>

        <div style="text-align: center;">
            <?php /*
  <img style="width: 200px" src="data:image/png;base64,{{DNS1D::getBarcodePNG('ORD2619', 'C39+',3,33)}}" alt="barcode"   />*/
            ?>
        </div>

        <div class="center">Thank you for shopping with us!</div>

        <div style="display: flex;margin-top: 20px">
            <div>
                <span style="text-align: left">Printed On: 01/02/2025 10:38 PM</span>
                <span style="text-align: right;margin-left: 60px">E & O E</span>
            </div>
        </div>
    </div>
</body>

</html>
