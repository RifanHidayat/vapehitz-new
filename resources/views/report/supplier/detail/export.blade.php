<table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">0 - 30</th>
                                <th class="text-right">31 - 60</th>
                                <th class="text-right">61 - 90</th>
                                <th class="text-right">90+</th>
                            </tr>
                        </thead>
                        <tbody id="datatable-tbody">
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
                            <?php $debt = $invoice->netto - $invoice->total_payment ?>
                            <tr>
                                <td></td>
                                <td>{{ $invoice->code }}</td>
                                <td>{{ date("Y-m-d", strtotime($invoice->date)) }}</td>
                                <td class="text-right">{{ number_format($debt) }}</td>
                                <td class="text-right">{{ ($invoice->due_group == '0-30') ? $debt : 0 }}</td>
                                <?php $total30 += ($invoice->due_group == '0-30') ? $debt : 0 ?>
                                <td class="text-right">{{ ($invoice->due_group == '31-60') ? $debt : 0 }}</td>
                                <?php $total60 += ($invoice->due_group == '31-60') ? $debt : 0 ?>
                                <td class="text-right">{{ ($invoice->due_group == '61-90') ? $debt : 0 }}</td>
                                <?php $total90 += ($invoice->due_group == '61-90') ? $debt : 0 ?>
                                <td class="text-right">{{ ($invoice->due_group == '90+') ? $debt : 0 }}</td>
                                <?php $totalUpper90 += ($invoice->due_group == '90+') ? $debt : 0 ?>
                                <?php $grandTotal += $debt ?>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3">Total for {{ $customerName }}</td>
                                <td class="text-right">{{ $grandTotal }}</td>
                                <td class="text-right">{{ $total30 }}</td>
                                <td class="text-right">{{ $total60 }}</td>
                                <td class="text-right">{{ $total90 }}</td>
                                <td class="text-right">{{ $totalUpper90 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>