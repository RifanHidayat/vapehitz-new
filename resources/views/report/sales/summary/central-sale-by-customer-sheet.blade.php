<table>
    <thead>
        <tr>
            <td>Customer</td>
            <td>Total</td>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        @foreach($sales_by_customer as $sale)
        <tr>
            <td>{{ $sale['customer'] }}</td>
            <td data-format="#,##0_-">{{ $sale['total'] }}</td>
        </tr>
        <?php $total += $sale['total'] ?>
        @endforeach
        <tr>
            <td>TOTAL</td>
            <td data-format="#,##0_-">{{ $total }}</td>
        </tr>
    </tbody>
</table>