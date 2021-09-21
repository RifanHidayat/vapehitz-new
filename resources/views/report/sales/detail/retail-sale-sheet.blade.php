<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Free</th>
            <th>Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php $emptyCellCount = 5; ?>
        <?php $total = 0; ?>
        @foreach($sales as $sale)
        <?php $invoiceTotal = 0; ?>
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->date }}</td>
            @for($i = 0; $i < $emptyCellCount; $i++) <td>
                </td>
                @endfor
        </tr>
        @foreach($sale->products as $product)
        <tr>
            <td></td>
            <td></td>
            <td>{{ $product->productCategory->name . ':' }}{{ $product->productSubcategory->name . ' - ' }}{{ $product->name }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->quantity }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->free }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->price }}</td>
            <?php $amount = $product->pivot->quantity * $product->pivot->price ?>
            <td data-format="#,##0_-">{{ $amount }}</td>
        </tr>
        <?php $total += $amount ?>
        <?php $invoiceTotal += $amount ?>
        @endforeach
        <tr>
            <td>Total for {{ $sale->code }}</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $invoiceTotal }}</td>
        </tr>
        @endforeach
        <tr>
            <td>Total</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $total }}</td>
        </tr>
    </tbody>
</table>