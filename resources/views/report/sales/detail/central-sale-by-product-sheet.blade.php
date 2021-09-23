<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Date</th>
            <th>No.</th>
            <th>Created By</th>
            <th>Customer</th>
            <th>Qty</th>
            <th>Free</th>
            <th>Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php $grandTotal = 0; ?>
        <?php $emptyCellCount = 8; ?>
        @foreach($sales_by_product as $product)
        <tr>
            <td>{{ $product->name }}</td>
            @for($i = 0; $i < $emptyCellCount; $i++) <td>
                </td>
                @endfor
        </tr>
        <?php $totalByProduct = 0; ?>
        @foreach($product->centralSales as $sale)
        <tr>
            <td></td>
            <td>{{ $sale->date }}</td>
            <td>{{ $sale->code }}</td>
            @if($sale->createdBy !== null)
            <td>{{ $sale->createdBy->name }}</td>
            @else
            <td></td>
            @endif
            <td>{{ $sale->customer->name }}</td>
            <td data-format="#,##0_-">{{ $sale->pivot->quantity }}</td>
            <td data-format="#,##0_-">{{ $sale->pivot->free }}</td>
            <td data-format="#,##0_-">{{ $sale->pivot->price }}</td>
            <td data-format="#,##0_-">{{ $sale->pivot->amount }}</td>
        </tr>
        <?php $totalByProduct += $sale->pivot->amount ?>
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
    </tbody>
</table>