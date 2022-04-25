<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALES RECEIPT </title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            width: 80mm;
            margin: 0;
            padding: 0;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .display-block {
            display: block;
        }

        .w-50 {
            width: 50%;
        }

        .container {
            width: 74mm;
            margin: 0 auto;
            padding-top: 5mm;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main {
            /* margin-top: 10px; */
            padding: 5px 0;
            border-bottom: 1px dashed #000;
        }

        .table-product thead tr {
            background-color: turquoise;
        }

        .table-product tbody tr td {
            padding: 4px 0;
            font-size: 11px;
        }

        .table-summary tbody tr td {
            padding: 4px 0;
            font-size: 11px;
        }

        .header {
            padding: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            display: flex;
            justify-content: space-between;
            margin-top: 3mm;
        }

        .divider-dash {
            width: 100%;
            border-bottom: 1px dotted #000;
            margin: 20px 0;
        }

        .table-summary tr td {
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center">
            <img src="https://images.tokopedia.net/img/cache/215-square/shops-1/2017/8/21/223853/223853_dcbe9f7f-5194-4fe5-b050-432d5e9df88f.jpg" alt="Logo" width="60">
            <strong class="display-block">Vapehitz</strong>
        </div>
        <div class="header">
            <span style="width: 50%;">No. {{ $sale->code }}</span>
            <span style="width: 50%; text-align: right;">
                Sales.
                @if($sale->createdBy !== null)
                {{ $sale->createdBy->name }}
                @endif
            </span>
        </div>
        <div class="main">
            <table class="table table-product">
                <tbody>
                    <?php $totalAmount = 0; ?>
                    @foreach($sale->products as $product)
                    <tr>
                        <td>
                            <div class="product-item">
                                <div class="w-50">
                                    <span class="display-block">{{ $product->name }}</span>
                                    <span class="display-block" style="margin-top: 3px;">{{ number_format($product->pivot->quantity * 1, 0, ',', '.') }} x {{ number_format($product->pivot->price, 0, ',', '.') }}</span>
                                    <span class="display-block" style="margin-top: 3px;">Free: {{ $product->pivot->free * 1 }}</span>
                                </div>
                                <div class="w-50">
                                    <span class="display-block text-right">{{ number_format($product->pivot->quantity * $product->pivot->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <?php $totalAmount += ($product->pivot->quantity * $product->pivot->price) ?>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="summary">
            <table class="table table-summary">
                <tr>
                    <td style="font-size: 12px;"><strong>Subtotal</strong></td>
                    <td class="text-right" style="font-size: 12px;"><strong>{{ number_format($totalAmount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td>Diskon</td>
                    @if($sale->discount_type == 'nominal')
                    <td class="text-right">{{ number_format(0 - $sale->discount, 0, ',', '.') }}</td>
                    @else
                    @php
                    $discount = $totalAmount * ($sale->discount / 100);
                    @endphp
                    <td class="text-right">{{ number_format(0 - $discount, 0, ',', '.') }}</td>
                    @endif
                </tr>
                <tr>
                    <td>Shipping</td>
                    <td class="text-right">{{ number_format($sale->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Lainnya {{ $sale->detail_other_cost !== null ? '(' . $sale->detail_other_cost . ')' : ''  }}</td>
                    <td class="text-right">{{ number_format($sale->other_cost, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 1px dashed #000;">
                    <td style="font-size: 14px;"><strong>Total</strong></td>
                    <td class="text-right" style="font-size: 14px;"><strong>{{ number_format($sale->net_total, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td>Bayar</td>
                    <td class="text-right">{{ number_format($sale->payment_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">{{ number_format($sale->payment_amount - $sale->net_total, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 1px dashed #000;">
                    <td>{{ date('d/m/y', strtotime($sale->date)) }} {{ date('H:i', strtotime($sale->created_at)) }}</td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>