<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PURCHASE RECEIPT</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
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
            font-size: 12px;
        }

        .divider-dash {
            width: 100%;
            border-bottom: 1px dotted #000;
            margin: 20px 0;
        }

        .table-summary tr td {
            padding: 5px;
        }
    </style>
</head>

<body>
<h1 style="font-weight: lighter; font-size: 2rem">PURCHASE RECEIPT</h1>
    <div class="header">
        <table class="table">
            <thead>
                <tr>
                    <td class="text-uppercase text-bold">Ship From</td>
                    <td class="text-uppercase text-bold">Ship To</td>
                    <td class="text-uppercase text-center"><strong>Purchase No.</strong> {{ $purchase->code }}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                  
                  
                    @if($purchase->supplier !== null)
                    <td class="text-uppercase">{{ $purchase->supplier->name }}</td>
                    @else
                    <td></td>
                    @endif
                      <td class="text-uppercase">Vapehitz</td>
                    <td class="text-uppercase text-center"><strong>DATE</strong> {{ date("d/m/Y", strtotime($purchase->date)) }}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        
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
                @foreach($purchase->products as $product)
                <tr>
                    <td class="text-uppercase">{{ $product->name }}</td>
                    <td>-</td>
                    <td class="text-right">{{ number_format($product->pivot->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->pivot->free, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($product->pivot->price *$product->pivot->quantity ), 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="divider-dash"></div>
    <table class="table table-summary">
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Subtotal</td>
            <td class="text-right">{{ number_format($purchase->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Shipping Cost</td>
            <td class="text-right">{{ number_format($purchase->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Discount</td>
            <td class="text-right">{{ number_format(0 - $purchase->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Net Total</td>
            <td class="text-right">{{ number_format($purchase->netto, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td style="width: 50%;"></td>
            <td class="text-uppercase">Pay Amount</td>
            <td class="text-right">{{ number_format($pay_amount, 0, ',', '.') }}</td>
        </tr>
        

    </table>
</body>

</html>