@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h4 class="nk-block-title fw-normal">Rekapitulasi Piutang Customer (Detail)</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <!-- <a href="/central-sale/create" class="btn btn-primary">Tambah</a>
        <p></p>
        <p></p> -->
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <div class="d-flex align-items-center justify-content-end mb-5">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <em class="icon ni ni-download"></em>&nbsp;Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="/customer/report/piutang/summary?report_type=detail" target="_blank">.xlsx</a>
                            <!-- <a class="dropdown-item" href="#" disabled>.pdf</a> -->
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 1000px;">
                    <table class="table table-striped">
                        <thead class="sticky-top bg-white">
                            <tr>
                                <th>Customer</th>
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
                            <?php $debt = $invoice->net_total - $invoice->total_payment ?>
                            <tr>
                                <td></td>
                                <td>{{ $invoice->code }}</td>
                                <td>{{ date("Y-m-d", strtotime($invoice->date)) }}</td>
                                <td class="text-right">{{ number_format($debt) }}</td>
                                <td class="text-right">{{ number_format(($invoice->due_group == '0-30') ? $debt : 0) }}</td>
                                <?php $total30 += ($invoice->due_group == '0-30') ? $debt : 0 ?>
                                <td class="text-right">{{ number_format(($invoice->due_group == '31-60') ? $debt : 0) }}</td>
                                <?php $total60 += ($invoice->due_group == '31-60') ? $debt : 0 ?>
                                <td class="text-right">{{ number_format(($invoice->due_group == '61-90') ? $debt : 0) }}</td>
                                <?php $total90 += ($invoice->due_group == '61-90') ? $debt : 0 ?>
                                <td class="text-right">{{ number_format(($invoice->due_group == '90+') ? $debt : 0) }}</td>
                                <?php $totalUpper90 += ($invoice->due_group == '90+') ? $debt : 0 ?>
                                <?php $grandTotal += $debt ?>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3">Total for {{ $customerName }}</td>
                                <td class="text-right">{{ number_format($grandTotal) }}</td>
                                <td class="text-right">{{ number_format($total30) }}</td>
                                <td class="text-right">{{ number_format($total60) }}</td>
                                <td class="text-right">{{ number_format($total90) }}</td>
                                <td class="text-right">{{ number_format($totalUpper90) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
@endsection