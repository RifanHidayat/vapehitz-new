<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Date</th>
            <th>No.</th>
            <th>Supplier</th>
            <th>Qty</th>
            <th>Free</th>
            <th>Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <?php $grandTotal = 0; ?>
    <?php $emptyCellCount = 7; ?>
    @foreach($purchases_by_product as $product)
    <tr>
        <td>{{ $product->name }}</td>
        @for($i = 0; $i < $emptyCellCount; $i++) <td>
            </td>
            @endfor
    </tr>
    <?php $totalByProduct = 0; ?>
    @foreach($product->centralPurchases as $purchase)
    <tr>
        <td></td>
        <td>{{ $purchase->date }}</td>
        <td>{{ $purchase->code }}</td>
        <td>{{ $purchase->supplier->name }}</td>
        <td data-format="#,##0_-">{{ $purchase->pivot->quantity }}</td>
        <td data-format="#,##0_-">{{ $purchase->pivot->free }}</td>
        <td data-format="#,##0_-">{{ $purchase->pivot->price }}</td>
        <?php $amount = $purchase->pivot->quantity * $purchase->pivot->price ?>
        <td data-format="#,##0_-">{{ $amount }}</td>
    </tr>
    <?php $totalByProduct += $amount ?>
    @endforeach
    <tr style="border-top: 1px solid #000;">
        <td>Total For {{ $product->name }}</td>
        @for($i = 0; $i < ($emptyCellCount - 1); $i++) <td>
            </td> @endfor
            <td data-format="#,##0_-">{{ $totalByProduct }}</td>
    </tr>
    <?php $grandTotal += $totalByProduct ?>
    @endforeach
    <tr>
        <td>TOTAL</td>
        @for($i = 0; $i < ($emptyCellCount - 1); $i++) <td>
            </td>
            @endfor
            <td data-format="#,##0_-">{{ $grandTotal }}</td>
    </tr>
</table>