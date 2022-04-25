<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALES RECEIPT - {{ $sale->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
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

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .main {
            margin-top: 30px;
        }

        .table-product thead tr {
            background-color: turquoise;
        }

        .table-product thead tr td,
        .table-product tbody tr td {
            padding: 3px 5px;
            font-size: 10px;
        }

        .divider-dash {
            width: 100%;
            border-bottom: 1px dotted #000;
            margin: 20px 0;
        }

        .table-summary tr td {
            padding: 5px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h1 style="font-weight: lighter; font-size: 3rem">SALES RECEIPT</h1>
    <div class="header">
        <table class="table">
            <thead>
                <tr>
                    <td class="text-uppercase text-bold">Ship To</td>
                    <td class="text-uppercase text-bold">Bill To</td>
                    <td class="text-uppercase text-center"><strong>Sales No.</strong> {{ $sale->code }}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @if($sale->customer !== null)
                    <td class="text-uppercase">{{ $sale->customer->name }}</td>
                    @else
                    <td></td>
                    @endif
                    <td class="text-uppercase">{{ $sale->recipient }}</td>
                    <td class="text-uppercase text-center"><strong>DATE</strong> {{ date("d/m/Y", strtotime($sale->date)) }}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <td class="text-uppercase text-bold" style="width: 20%;">Ship Date</td>
                    <td class="text-uppercase text-bold" style="width: 20%;">Due Date</td>
                    <td class="text-uppercase text-bold" style="width: 20%;">Ship Via</td>
                    <td class="text-uppercase text-bold" style="width: 20%;">FOB Ship Point</td>
                    <td class="text-uppercase text-bold" style="width: 20%;">PMT Method</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-uppercase">{{ date("d/m/Y", strtotime($sale->date)) }}</td>
                    <td class="text-uppercase">{{ date("d/m/Y", strtotime($sale->due_date)) }}</td>
                    @if($sale->shipment !== null)
                    <td class="text-uppercase">{{ $sale->shipment->name }}</td>
                    @else
                    <td></td>
                    @endif
                    <td class="text-uppercase">-</td>
                    <td class="text-uppercase">-</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="main">
        <table class="table table-product">
            <thead>
                <tr>
                    <!-- <td class="text-uppercase">Date</td> -->
                    <td class="text-uppercase">Product</td>
                    <td class="text-uppercase">Description</td>
                    <td class="text-uppercase text-right">Qty</td>
                    <td class="text-uppercase text-right">Free</td>
                    <td class="text-uppercase text-right">Price</td>
                    <td class="text-uppercase text-right">Amount</td>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->products as $product)
                <tr>
                    <td class="text-uppercase">{{ $product->name }}</td>
                    <td>-</td>
                    <td class="text-right">{{ number_format($product->pivot->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->pivot->free, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->pivot->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="divider-dash"></div>
    <table class="table table-summary">
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Total Amount</td>
            <td class="text-right">{{ number_format($sale->total_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Discount</td>
            @if($sale->discount_type == 'nominal')
            <td class="text-right">{{ number_format(0 - $sale->discount, 0, ',', '.') }}</td>
            @else
            @php
            $discount = $sale->total_cost * ($sale->discount / 100);
            @endphp
            <td class="text-right">{{ number_format(0 - $discount, 0, ',', '.') }}</td>
            @endif
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Subtotal</td>
            <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Shipping Cost</td>
            <td class="text-right">{{ number_format($sale->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Other Cost {{ $sale->detail_other_cost !== null ? '(' . $sale->detail_other_cost . ')' : ''  }}</td>
            <td class="text-right">{{ number_format($sale->other_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Customer's Deposit</td>
            <td class="text-right">{{ number_format(0 - $sale->deposit_customer, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Total</td>
            <td class="text-right">{{ number_format($sale->net_total, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>