<table>
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        @foreach($purchases_by_supplier as $purchase)
        <tr>
            <td>{{ $purchase['supplier'] }}</td>
            <td data-format="#,##0_-">{{ $purchase['total'] }}</td>
        </tr>
        <?php $total += $purchase['total'] ?>
        @endforeach
        <tr>
            <td>TOTAL</td>
            <td data-format="#,##0_-">{{ $total }}</td>
        </tr>
    </tbody>
</table>