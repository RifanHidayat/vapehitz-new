

<table>
<thead>
                            <tr>
                                <th>Supplierr</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">0 - 30</th>
                                <th class="text-right">31 - 60</th>
                                <th class="text-right">61 - 90</th>
                                <th class="text-right">90+</th>
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
                        // ?>
                        <tbody id="datatable-tbody">
                            <?php $grandTotal = 0; ?>
                            @foreach($customers as $customerName => $customer)
                            <?php $total = collect($customer)->sum() ?>
                            <tr>
                                <td>{{ $customerName }}</td>
                                <td class="text-right">{{ number_format($total) }}</td>
                                <td class="text-right">{{ number_format(validateKey($customer, '0-30')) }}</td>
                                <td class="text-right">{{ number_format(validateKey($customer, '31-60')) }}</td>
                                <td class="text-right">{{ number_format(validateKey($customer, '61-90')) }}</td>
                                <td class="text-right">{{ number_format(validateKey($customer, '90+')) }}</td>
                            </tr>
                            <?php $grandTotal += $total; ?>
                            @endforeach
                            <tr>
                                <th>Grand Total</th>
                                <th class="text-right">{{ number_format($grandTotal) }}</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
</table>