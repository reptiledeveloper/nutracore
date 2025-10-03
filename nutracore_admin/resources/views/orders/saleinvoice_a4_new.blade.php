@php
    use App\Helpers\CustomHelper;
    $order_items = \App\Helpers\CustomHelper::getOrderItemsWithProduct($orders->id);
    $total_qty = 0;
    $total_discount = 0;
    $tax_val = 0;
    $total_cart_price = 0;

    $address = DB::table('user_address')->where('id', $orders->address_id)->first();
@endphp

    <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" href="{{favicon()}}">
    <title>Invoice </title>
    <style>
        /* A4 page sizing for print */
        @page {
            size: A4;
            margin: 20mm;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 20px;
        }

        .paper {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        header, .bill-to, table, .notes, .totals, footer {
            width: 100%;
            margin-bottom: 12px;
        }

        header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .company {
            width: 48%;
            font-size: 12px;
            line-height: 1.35;
        }

        .invoice-meta {
            width: 48%;
            text-align: right;
            font-size: 13px;
        }

        .invoice-meta h1 {
            margin: 0 0 6px;
            font-size: 22px;
        }

        .bill-to {
            display: flex;
            justify-content: space-between;
            margin: 16px 0;
        }

        .bill-block {
            width: 48%;
            font-size: 13px;
        }

        .bill-block h3 {
            margin: 0 0 6px;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .qty, .unit, .rate, .amount {
            text-align: right;
        }

        .totals {
            margin-top: 12px;
            border-top: 1px solid #000;
            padding-top: 8px;
            font-size: 11px;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            margin: 6px 0;
        }

        .notes {
            margin-top: 18px;
            font-size: 12px;
            color: #444;
        }

        footer {
            font-size: 11px;
            color: #666;
            margin-top: 20px;
            text-align: left;
        }

        /* Ensure page breaks for PDF generation */
        .page-break {
            page-break-before: always;
        }
    </style>
    <style>
        .noborder-table {
            border-collapse: collapse;
            width: 100%;
        }

        .noborder-table td,
        .noborder-table th {
            border: none !important;
            padding: 4px 6px;
            font-size: 13px;
            vertical-align: top;
        }
    </style>

</head>
<body>

<div class="paper">
    <table class="noborder-table" style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:10px;">
        <tr>
            <!-- Left Side: Logo + Company Info + Bill To / Ship To -->
            <td style="width:60%; vertical-align:top;">

                <!-- Logo + Company Info -->
                <table class="noborder-table" style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                    <tr>
                        <td style="width:80px;">
                            <img src="{{ url('public/assets/images/logo.png') }}"
                                 alt="Logo"
                                 style="max-height:60px;">
                            <br>
                            <strong>NutraCore</strong> <br>
                            House No 2-39, 1st Floor, Tellapur Road,Hyderabad ,Rangareddy- 500019<br>
                            GSTIN: 36GOOPS5702B1ZS<br>
                            PAN: GOOPS5702B<br>
                            Phone: +91 88850 65550 | Email: nutracore.in@gmail.com
                        </td>

                    </tr>
                </table>

            </td>

            <!-- Right Side: Invoice Meta -->
            <td style="width:35%; vertical-align:top; text-align:right;">
                <h1 style="margin:0 0 6px; font-size:20px;">INVOICE</h1>
                <div>Invoice #: <strong>{{$orders->invoice_no??''}}</strong></div>
                <div>Date: <strong>{{ date('d/m/Y', strtotime($orders->created_at)) }}</strong></div>
            </td>
        </tr>
    </table>


    <section class="bill-to" style="margin: 16px 0;">
        <table class="noborder-table">
            <tr>
                <!-- Bill To -->
                <td width="50%">
                    <h3 style="margin: 0 0 6px; font-size: 14px;">Bill To</h3>
                    <div><strong>{{ $orders->customer_name ?? '' }}</strong></div>
                    <div>{{ !empty($orders->customer_name) ? $orders->customer_name : $user->name ??''}}</div>
                    <div>{{ $orders->house_no }} {{ $orders->apartment }}</div>
                    <div>{{ $orders->landmark }}</div>
                    <div>{{ $orders->location }}</div>
                    <div>Phone: +91 {{ $orders->contact_no ?? '' }}</div>
                </td>

                <!-- Ship To -->
                <td width="50%" style="text-align: right">
                    <h3 style="margin: 0 0 6px; font-size: 14px;">Ship To</h3>
                    <div><strong>{{ $orders->customer_name ?? '' }}</strong></div>
                    <div>{{ !empty($orders->customer_name) ? $orders->customer_name : $user->name ??''}}</div>
                    <div>{{ $orders->house_no }} {{ $orders->apartment }}</div>
                    <div>{{ $orders->landmark }}</div>
                    <div>{{ $orders->location }}</div>
                    <div>Phone: +91 {{ $orders->contact_no ?? '' }}</div>
                </td>
            </tr>
        </table>
    </section>


    <table style="font-size: 10px">
        <thead>
        <tr>
            <th>#</th>
            <th> DESCRIPTION OF GOODS</th>
            <th>Qty</th>
            <th>MRP</th>
            <th>TAXABLE</th>
            <th>DISCOUNT</th>
            <th> TAX</th>
            <th> TAX AMOUNT</th>
            <th>TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sub_total = 0;
            $total_discount = 0;
            $taxable_value = 0;
            $cgst = 0;
            $igst = 0;
            $delivery_charge = $orders->delivery_charge??0;
            $sgst = 0;
            $grand_total = 0;
        @endphp
        @foreach ($order_items as $i => $value)
            @php
                $product = CustomHelper::getProductDeatils($value->product_id);
                $image = CustomHelper::getImageUrl('products', $product->image);
                $varients = CustomHelper::getAdminProductSingleVarients($value->product_id, $value->variant_id);
                $discount = (int)$varients->mrp - (int)$value->price;

                $amountIncludingTax = (int)$value->price;
                $taxRate = $product->tax ?? 0;
                $tax_amount = 0;
                $taxableAmount = 0;
                if($taxRate > 0){
                   $amountIncludingTax;
                     $taxableAmount = $amountIncludingTax / (1 + $taxRate/100);
               $taxableAmount = round($taxableAmount);
               $tax_amount = (int)$value->price -(int) $taxableAmount;
                }


               $sub_total+=(int)$varients->mrp* (int)$value->qty;
               $total_discount+=(int)$discount* (int)$value->qty;
               $taxable_value+=(int)$taxableAmount;
               $cgst+=(int)$tax_amount/2;
               $sgst+=(int)$tax_amount/2;
               $igst+=(int)$tax_amount;
               $grand_total+=(int)$value->net_price;

            @endphp
            <tr>
                <td>{{$i+1}}</td>
                <td>{{ $product->name }}<br><small>{{ $varients->unit ??'' }} {{ $varients->unit_value ??'' }}</small>
                </td>
                <td class="qty">{{ $value->qty ??'' }}</td>
                <td class="">₹ {{ $varients->mrp ??0}}</td>
                <td class="">₹ {{ $taxableAmount ??''}}</td>
                <td class="">₹ {{$discount??0 }}</td>
                <td class="">{{$product->tax??0 }} %</td>
                <td class="">₹ {{$tax_amount}}</td>
                <td class="">₹{{ $value->net_price ??'' }}</td>
            </tr>
        @endforeach

        </tbody>

    </table>

    <section class="bill-to" style="margin: 16px 0;">
        <table class="noborder-table">
            <tr>
                <!-- Bill To -->
                <td width="50%">
                    <strong> Bank Details :</strong>
                    <div class="d-flex">
                        <span>A/C Name : NutraCore</span><br>
                        <span>Account No : 00066340000773</span><br>
                        <span>Name of Bank :  Yes Bank</span><br>
                    </div>

                    <div class="d-flex">
                        <span>Branch Name : Somajiguda</span><br>
                        <span>IFSC Code : YESB0000006</span><br>
                    </div>
                </td>

                <!-- Ship To -->
                <td width="50%" style="text-align: right">

                    <strong>Invoice Summary</strong>
                    <div>Sub Total: ₹{{ $sub_total }}</div>
                    <div>Discount: -₹{{ $total_discount }}</div>
                    <div>Taxable Value: ₹{{ $taxable_value }}</div>

                    @if(!empty($address) && $address->state == 'TG')
                        <div>CGST {{ $product->tax / 2 }}%: ₹{{ $cgst }}</div>
                        <div>SGST {{ $product->tax / 2 }}%: ₹{{ $sgst }}</div>
                    @elseif($orders->order_from == 'POS')
                        <div>CGST {{ $product->tax / 2 }}%: ₹{{ $cgst }}</div>
                        <div>SGST {{ $product->tax / 2 }}%: ₹{{ $sgst }}</div>
                    @else
                        <div>IGST {{ $product->tax ?? 0 }}%: ₹{{ $igst }}</div>
                    @endif

                    <div>Delivery Charge: ₹{{ $delivery_charge }}</div>
                    <div style="font-weight:bold; font-size:13px;">Grand Total:
                        ₹{{ (int)$grand_total + (int)$delivery_charge }}</div>

                </td>
            </tr>
        </table>


        <div class="totals">
            <div class="row">
                <div>Amount in words:</div>
                <div>
                    <em>{{ucfirst(CustomHelper::convert_number_to_words((int)$grand_total+(int)$delivery_charge))}}</em>
                </div>
            </div>
        </div>

        <footer>
            <div><strong>For Nutracore.</strong></div>
            <div style="margin-top:28px;">Authorized Signatory</div>
            <div style="margin-top:8px; font-size:11px; color:#777;">This is a computer-generated invoice and does not
                require a signature.
            </div>

            <div style="text-align: center">
                THANKS FOR VISIT
            </div>
        </footer>
</div>
</body>
</html>
