<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - NutraCore</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 13px;
            color: #000;
        }
        .invoice-box {
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            padding: 20mm;
            box-sizing: border-box;
            border: 1px solid #000;
        }
        .header {
            text-align: right;
            font-style: italic;
        }
        .company-info {
            text-align: left;
            margin-bottom: 20px;
        }
        .company-info h2 {
            margin: 0;
            color: #006699;
        }
        .buyer-info, .invoice-info {
            width: 100%;
            margin-bottom: 15px;
        }
        .buyer-info td, .invoice-info td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 12px;
        }
        .table th {
            background-color: #0f8f8f;
            color: #fff;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-weight: bold;
        }
        @media print {
            body {
                margin: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>





<div class="invoice-box">
    <div class="header">(Duplicate)</div>

    <div class="company-info">
        <h2>NutraCore</h2>
        <p>
            House No 2-39, 1st Floor, Tellapur Road, Hyderabad <br>
            Rangareddy - 500019, Telangana (36) <br>
            GST/UIN: 36GOOPS5702B1ZS <br>
            PAN NO: GOOPS5702B <br>
            Mobile: 8885065550 <br>
            Email: nutracore.in@gmail.com
        </p>
    </div>

    <table class="buyer-info">
        <tr><td><b>Buyer :</b> Zitesh Goud</td></tr>
        <tr><td><b>Address :</b></td></tr>
        <tr><td><b>City :</b> Rangareddy</td></tr>
        <tr><td><b>State :</b> Telangana (Telangana)</td></tr>
        <tr><td><b>GSTIN :</b></td></tr>
        <tr><td><b>Email :</b></td></tr>
        <tr><td><b>Contact No :</b> 8309182672</td></tr>
    </table>

    <table class="invoice-info">
        <tr>
            <td><b>Invoice Date :</b> 24/09/2025</td>
            <td><b>Invoice No :</b> NC/25-26/1769</td>
        </tr>
        <tr>
            <td><b>Created By :</b> NutraCore</td>
            <td><b>Salesman :</b> Karan</td>
        </tr>
    </table>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Description of Goods</th>
            <th>HSN</th>
            <th>UOM</th>
            <th>QTY</th>
            <th>Rate</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Tax Amount</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>ON Gold Standard Whey Protein 5 LB / Double Rich Chocolate</td>
            <td>21069099</td>
            <td>PCS</td>
            <td>1</td>
            <td>7,713.33</td>
            <td>1,084.00</td>
            <td>5.0%</td>
            <td>334.05</td>
            <td>7,015.00</td>
        </tr>
        <tr>
            <td>2</td>
            <td>ON Multivitamin for MEN - 60 Tablets, 26 Vitamin</td>
            <td>21069099</td>
            <td>PCS</td>
            <td>1</td>
            <td>898.10</td>
            <td>310.50</td>
            <td>5.0%</td>
            <td>27.95</td>
            <td>587.00</td>
        </tr>
        <tr>
            <td>3</td>
            <td>ON Zinc Magnesium Aspartate ZMA - 60 Capsules</td>
            <td>21069099</td>
            <td>PCS</td>
            <td>1</td>
            <td>1,020.00</td>
            <td>404.00</td>
            <td>5.0%</td>
            <td>31.76</td>
            <td>667.00</td>
        </tr>
        </tbody>
    </table>

    <div class="footer">
        Grand Total: 8,269.00
    </div>
</div>
</body>
</html>
