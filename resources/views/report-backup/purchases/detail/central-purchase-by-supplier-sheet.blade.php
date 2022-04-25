<table>
    <thead>
        <tr>
            <td>Supplier</td>
            <td>Date</td>
            <td>No.</td>
            <td>Created By</td>
            <td>Product</td>
            <td>Quantity</td>
            <td>Free</td>
            <td>Price</td>
            <td>Amount</td>
        </tr>
    </thead>
    <tbody>
        <?php $emptyCellCount = 8; ?>
        @foreach($purchases_by_supplier as $supplier => $purchases)
        <tr>
            <td>{{ $supplier }}</td>
            @for($i = 0; $i < $emptyCellCount; $i++) <td>
                </td>
                @endfor
        </tr>
        <?php $totalBySupplier = 0; ?>
        @foreach($purchases as $purchase)
        @foreach($purchase->products as $product)
        <tr>
            <td></td>
            <td>{{ $purchase->date }}</td>
            <td>{{ $purchase->code }}</td>
            @if($purchase->createdBy !== null)
            <td>{{ $purchase->createdBy->name }}</td>
            @else
            <td></td>
            @endif
            <td>{{ $product->productCategory->name . ':' }}{{ $product->productSubcategory->name . ' - ' }}{{ $product->name }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->quantity }}</td>
            <td data-format="#,##0_-">{{ 0 }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->price }}</td>
            <?php $amount = $product->pivot->quantity * $product->pivot->price ?>
            <td data-format="#,##0_-">{{ $amount }}</td>
        </tr>
        <?php $totalBySupplier += $amount ?>
        @endforeach

        @endforeach
        <tr style="border-top: 1px solid #000;">
            <td>Total For {{ $supplier }}</td>
            @for($i = 0; $i < ($emptyCellCount - 1); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $totalBySupplier }}</td>
        </tr>
        @endforeach
    </tbody>
</table>