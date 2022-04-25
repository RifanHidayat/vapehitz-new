<table>
    <thead>
        <tr>
            <th>Customer</th>
            <th>No.</th>
            <th>Tanggal</th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: right;">0 - 30</th>
            <th style="text-align: right;">31 - 60</th>
            <th style="text-align: right;">61 - 90</th>
            <th style="text-align: right;">90+</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customerName => $customer)
        <tr>
            <td>{{ $customerName }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $grandTotal = 0 ?>
        <?php $total30 = 0 ?>
        <?php $total60 = 0 ?>
        <?php $total90 = 0 ?>
        <?php $totalUpper90 = 0 ?>
        @foreach($customer as $invoice)
        <?php $debt = $invoice->net_total - $invoice->total_payment ?>
        <tr>
            <td></td>
            <td>{{ $invoice->code }}</td>
            <td>{{ date("Y-m-d", strtotime($invoice->date)) }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $debt }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ ($invoice->due_group == '0-30') ? $debt : 0 }}</td>
            <?php $total30 += ($invoice->due_group == '0-30') ? $debt : 0 ?>
            <td style="text-align: right;" data-format="#,##0_-">{{ ($invoice->due_group == '31-60') ? $debt : 0 }}</td>
            <?php $total60 += ($invoice->due_group == '31-60') ? $debt : 0 ?>
            <td style="text-align: right;" data-format="#,##0_-">{{ ($invoice->due_group == '61-90') ? $debt : 0 }}</td>
            <?php $total90 += ($invoice->due_group == '61-90') ? $debt : 0 ?>
            <td style="text-align: right;" data-format="#,##0_-">{{ ($invoice->due_group == '90+') ? $debt : 0 }}</td>
            <?php $totalUpper90 += ($invoice->due_group == '90+') ? $debt : 0 ?>
            <?php $grandTotal += $debt ?>
        </tr>
        @endforeach
        <tr>
            <td colspan="3">Total for {{ $customerName }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $grandTotal }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $total30 }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $total60 }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $total90 }}</td>
            <td style="text-align: right;" data-format="#,##0_-">{{ $totalUpper90 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>