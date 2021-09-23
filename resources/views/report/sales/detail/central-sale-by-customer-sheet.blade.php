<table>
    <?php $emptyCellCount = 7; ?>
    @foreach($sales_by_customer as $customer => $sales)
    <tr>
        <td>{{ $customer }}</td>
        @for($i = 0; $i < $emptyCellCount; $i++) <td>
            </td>
            @endfor
    </tr>
    <?php $totalByCustomer = 0; ?>
    @foreach($sales as $sale)
    @foreach($sale->products as $product)
    <tr>
        <td></td>
        <td>{{ $sale->date }}</td>
        <td>{{ $sale->code }}</td>
        <td>{{ $product->productCategory->name . ':' }}{{ $product->productSubcategory->name . ' - ' }}{{ $product->name }}</td>
        <td data-format="#,##0_-">{{ $product->pivot->quantity }}</td>
        <td data-format="#,##0_-">{{ $product->pivot->free }}</td>
        <td data-format="#,##0_-">{{ $product->pivot->price }}</td>
        <td data-format="#,##0_-">{{ $product->pivot->amount }}</td>
    </tr>
    <?php $totalByCustomer += $product->pivot->amount ?>
    @endforeach

    @endforeach
    <tr style="border-top: 1px solid #000;">
        <td>Total For {{ $customer }}</td>
        @for($i = 0; $i < ($emptyCellCount - 1); $i++) <td>
            </td> @endfor
            <td data-format="#,##0_-">{{ $totalByCustomer }}</td>
    </tr>
    @endforeach
</table>