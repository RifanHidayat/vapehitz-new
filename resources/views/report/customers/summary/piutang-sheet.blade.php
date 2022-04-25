<table>
    <thead>
        <tr>
            <th>Customer</th>
            <th>Total</th>
            <th>0 - 30</th>
            <th>31 - 60</th>
            <th>61 - 90</th>
            <th>90+</th>
        </tr>
    </thead>
    <?php
    function validateKey($arr, $key)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        } else {
            return 0;
        }
    }
    ?>
    <tbody>
        <?php $grandTotal = 0; ?>
        @foreach($customers as $customerName => $customer)
        <?php $total = collect($customer)->sum() ?>
        <tr>
            <td>{{ $customerName }}</td>
            <td data-format="#,##0_-">{{ $total }}</td>
            <td data-format="#,##0_-">{{ validateKey($customer, '0-30') }}</td>
            <td data-format="#,##0_-">{{ validateKey($customer, '31-60') }}</td>
            <td data-format="#,##0_-">{{ validateKey($customer, '61-90') }}</td>
            <td data-format="#,##0_-">{{ validateKey($customer, '90+') }}</td>
        </tr>
        <?php $grandTotal += $total; ?>
        @endforeach
        <tr>
            <td></td>
            <td data-format="#,##0_-">{{ $grandTotal }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>