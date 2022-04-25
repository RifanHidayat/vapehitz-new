<table>
    <thead>
        <tr>
            <td>Customer</td>
            <td>Date</td>
            <td>No.</td>
            <td>Created By</td>
            <td>Product</td>
            <td>Quantity</td>
            <td>Free</td>
            <td>Price</td>
            <td>Amount</td>
            <td>Diskon</td>
            <td>Biaya Kirim</td>
        </tr>
    </thead>
    <tbody>
        <?php $emptyCellCount = 8; ?>
        @foreach($sales_by_customer as $customer => $sales)
        <tr>
            <td>{{ $customer }}</td>
            @for($i = 0; $i < $emptyCellCount; $i++) <td>
                </td>
                @endfor
        </tr>
        <?php $totalByCustomer = 0; ?>
        @foreach($sales as $sale)
        @foreach($sale->products as $index => $product)
        <tr>
            <td></td>
            <td>{{ $sale->date }}</td>
            <td>{{ $sale->code }}</td>
            @if($sale->createdBy !== null)
            <td>{{ $sale->createdBy->name }}</td>
            @else
            <td>-</td>
            @endif
            <td>
                @if($product->productCategory !== null)
                {{ $product->productCategory->name . ':' }}
                @endif
                @if($product->productSubcategory !== null)
                {{ $product->productSubcategory->name . ' - ' }}
                @endif
                {{ $product->name }}
            </td>
            <td data-format="#,##0_-">{{ $product->pivot->quantity }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->free }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->price }}</td>
            <td data-format="#,##0_-">{{ $product->pivot->amount }}</td>
            <?php 
                $discount = $sale->discount;
                if($sale->discount_type == 'percentage') {
                    $discount = $sale->total_cost * ($sale->discount / 100);
                }
            ?>
            @if($index == 1)
            <td data-format="#,##0_-">{{ $discount }}</td>
            <td data-format="#,##0_-">{{ $sale->shipping_cost }}</td>
            @else
            <td></td>
            <td></td>
            @endif
        </tr>
        <?php $totalByCustomer += $product->pivot->amount ?>
        @endforeach

        @endforeach
        <tr style="border-top: 1px solid #000;">
            <td>Total Amount For {{ $customer }}</td>
            @for($i = 0; $i < ($emptyCellCount - 1); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $totalByCustomer }}</td>
        </tr>
        @endforeach
    </tbody>
</table>