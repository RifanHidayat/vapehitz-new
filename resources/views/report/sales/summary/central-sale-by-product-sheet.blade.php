<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Avg Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales_by_product as $category => $products)
        <tr>
            <td>{{ $category }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $totalAmountByCategory = 0; ?>
        @foreach($products as $product)
        <tr>
            <td></td>
            <td>{{ $product['name'] }}</td>
            <td data-format="#,##0_-">{{ $product['quantity'] }}</td>
            <td data-format="#,##0_-">{{ $product['amount'] }}</td>
            <td data-format="#,##0_-">{{ $product['avg_price'] }}</td>
        </tr>
        <?php $totalAmountByCategory += $product['amount'] ?>
        @endforeach
        <tr>
            <td>Total For {{ $category }}</td>
            <td></td>
            <td></td>
            <td data-format="#,##0_-">{{ $totalAmountByCategory }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>