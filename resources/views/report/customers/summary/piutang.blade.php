@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h4 class="nk-block-title fw-normal">Rekapitulasi Piutang Customer (Summary)</h4>
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
                            <a class="dropdown-item" href="/customer/report/piutang/summary?report_type=summary" target="_blank">.xlsx</a>
                            <!-- <a class="dropdown-item" href="#" disabled>.pdf</a> -->
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 1000px;">
                    <table class="table table-striped">
                        <thead  class="sticky-top bg-white">
                            <tr>
                                <th>Customer</th>
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
                        ?>
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
                                <td></td>
                                <td class="text-right">{{ number_format($grandTotal) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
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