<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERMINTAAN BARANG KE PUSAT {{ $req->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
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
            margin-top: 10px;
        }

        .table-product thead tr {
            background-color: turquoise;
        }

        .table-product thead tr td,
        .table-product tbody tr td {
            padding: 3px 5px;
            font-size: 12px;
        }

        .table-product tbody tr:nth-child(odd) {
            background-color: #ecf0f1;
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
    <h2 style="font-weight: lighter;">PERMINTAAN BARANG DARI STUDIO KE PUSAT</h2>
    <div class="header">
        <table class="table">
            <tr>
                <td class="text-uppercase"><strong>No.</strong> {{ $req->code }}</td>
                <td class="text-uppercase text-right"><strong>Date.</strong> {{ date("d/m/Y", strtotime($req->date)) }}</td>
            </tr>
        </table>
    </div>
    <div class="main">
        <table class="table table-product">
            <thead>
                <tr>
                    <td class="text-uppercase">Product</td>
                    <td class="text-uppercase text-right">Qty</td>
                </tr>
            </thead>
            <tbody>
                @foreach($req->products as $product)
                <tr>
                    <td class="text-uppercase" style="width: 50%;">{{ $product->name }}</td>
                    <td class=" text-right">{{ number_format($product->pivot->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>