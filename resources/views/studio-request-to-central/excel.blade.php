<table>
    <tr>
        <td>
            PERMINTAAN BARANG KE PUSAT
        </td>
    </tr>
    <tr>
        <td>
            No. {{ $req->code }}
        </td>
    </tr>
    <tr>
        <td>
            Date. {{ $req->date }}
        </td>
    </tr>
    <tr>
        <td>Product</td>
        <td>Qty</td>
    </tr>
    @foreach($req->products as $product)
    <tr>
        <td>{{ $product->name }}</td>
        <td>{{ $product->pivot->quantity }}</td>
    </tr>
    @endforeach
</table>