<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Date</th>
            <th>Created By</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Free</th>
            <th>Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php $emptyCellCount = 6; ?>
        <?php $total = 0; ?>
        @foreach($sales as $sale)
        <?php $invoiceTotal = 0; ?>
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->date }}</td>
            @if($sale->createdBy !== null)
            <td>{{ $sale->createdBy->name }}</td>
            @else
            <td></td>
            @endif
            @for($i = 0; $i < $emptyCellCount; $i++) <td>
                </td>
                @endfor
        </tr>
        @foreach($sale->products as $product)
        <tr>
            <td></td>
            <td></td>
            <td></td>
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
            <?php $amount = $product->pivot->quantity * $product->pivot->price ?>
            <td data-format="#,##0_-">{{ $amount }}</td>
        </tr>
        <?php $total += $amount ?>
        <?php $invoiceTotal += $amount ?>
        @endforeach
        <tr>
            <td>Total Amount</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $sale->subtotal }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <?php 
                $discount = $sale->discount;
                if($sale->discount_type == 'percentage') {
                    $discount = $sale->subtotal * ($sale->discount / 100);
                }
                ?>
                <td data-format="#,##0_-">-{{ $discount }}</td>
        </tr>
        <tr>
            <td>Biaya Lainnya ({{$sale->other_cost_description}})</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <td data-format="#,##0_-">{{ $sale->other_cost }}</td>
        </tr>
        <tr>
            <td>Total for {{ $sale->code }}</td>
            @for($i = 0; $i < ($emptyCellCount); $i++) <td>
                </td> @endfor
                <?php $grandTotal = $sale->subtotal - $discount + $sale->other_cost ?>
                <td data-format="#,##0_-"><strong>{{ $grandTotal }}</strong></td>
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