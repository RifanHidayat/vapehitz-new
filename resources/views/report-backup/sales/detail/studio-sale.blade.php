@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h4 class="nk-block-title fw-normal">Laporan Penjualan Retail</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <!-- <a href="/central-sale/create" class="btn btn-primary">Tambah</a>
        <p></p>
        <p></p> -->
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="dropdown mr-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <em class="icon ni ni-download"></em>&nbsp;Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" :href="'/studio-sale/report/sheet' + generatedRequest" target="_blank">.xlsx</a>
                            <!-- <a class="dropdown-item" href="#" disabled>.pdf</a> -->
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                        </div>
                    </div>
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter"><em class="icon ni ni-setting align-middle"></em>&nbsp; Filter</a>
                </div>
                <div class="collapse row" id="collapseFilter">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="default-06">Periode Laporan</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control" id="default-06">
                                                <option value="default_option">Custom Date</option>
                                                <!-- <option value="default_option">Default Option</option>
                                            <option value="default_option">Default Option</option> -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-right">
                                            <em class="icon ni ni-calendar-alt"></em>
                                        </div>
                                        <input type="text" class="form-control start-date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-right">
                                            <em class="icon ni ni-calendar-alt"></em>
                                        </div>
                                        <input type="text" class="form-control end-date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="form-group col-md-4">
                                <label class="form-label" for="filter-status">Status</label>
                                <div class="form-control-wrap ">
                                    <div class="form-control-select">
                                        <select class="form-control" id="filter-status">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label" for="filter-customer">Customer</label>
                                <div class="form-control-wrap ">
                                    <select class="form-control filter-customer" id="filter-customer">
                                        <option value="">All</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label" for="filter-shipment">Shipment</label>
                                <div class="form-control-wrap ">
                                    <select class="form-control filter-shipment" id="filter-shipment">
                                        <option value="">All</option>
                                        @foreach($shipments as $shipment)
                                        <option value="{{ $shipment->id }}">{{ $shipment->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Kolom</label>
                                    <div class="row">
                                        <div v-for="column in filter.columns" class="col-md-3">
                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                <input type="checkbox" v-model="filter.columnSelections" :value="column.id" class="custom-control-input" :id="'check' + column.id">
                                                <label class="custom-control-label" :for="'check' + column.id">@{{ column.text }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" @click="applyFilter">Apply</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">

                    </div> -->
                </div>
                <div class="divider"></div>
                <div class="text-center">
                    <em class="icon ni ni-share-alt" style="font-size: 5em;"></em>
                    <p class="text-soft mt-2">Report preview is under construction</p>
                </div>
                <!-- <div class="table-responsive">
                    <table class="table table-striped" id="centralSale">
                        <thead>
                            <tr class="text-center">
                                <th>No. Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Customer</th>
                                <th>Shipment</th>
                             
                                <th>Penerima</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Shipping</th>
                                <th>Grand Total</th>
                                <th>Pembayaran 1</th>
                                <th>Pembayaran 2</th>
                                <th>Sisa</th>
                                <th>Berat (gr)</th>
                                <th>Status</th>
                                <th>Status Cetak</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div> -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            generatedRequest: {},
            filter: {
                startDate: '{{ date("Y-m-01") }}',
                endDate: '{{ date("Y-m-t") }}',
                columns: [{
                        id: 'number',
                        text: 'Nomor'
                    },
                    {
                        id: 'date',
                        text: 'Tanggal'
                    },
                    {
                        id: 'customer',
                        text: 'Customer'
                    },
                    {
                        id: 'shipment',
                        text: 'Shipment',
                    },
                    {
                        id: 'recipient',
                        text: 'Penerima',
                    },
                    {
                        id: 'subtotal',
                        text: 'Sub Total',
                    },
                    {
                        id: 'discount',
                        text: 'Discount',
                    },
                    {
                        id: 'shipping_cost',
                        text: 'Shipping',
                    },
                    {
                        id: 'net_total',
                        text: 'Total',
                    },
                    {
                        id: 'receive_1',
                        text: 'Jumlah Pembayaran 1',
                    },
                    {
                        id: 'receive_2',
                        text: 'Jumlah Pembayaran 2',
                    },
                    {
                        id: 'remaining_payment',
                        text: 'Sisa Pembayaran',
                    },
                    {
                        id: 'total_weight',
                        text: 'Berat',
                    },
                    {
                        id: 'status',
                        text: 'Status',
                    },
                    {
                        id: 'is_printed',
                        text: 'Status Cetak',
                    },
                ],
                columnSelections: ['number', 'date', 'customer', 'shipment', 'recipient', 'subtotal', 'discount', 'shipping_cost', 'net_total', 'receive_1', 'receive_2', 'remaining_payment', 'total_weight', 'status', 'is_printed'],
                status: '',
                customer: '',
                shipment: '',
                sortBy: '',
                sortIn: 'asc',
            }
        },
        mounted() {
            this.applyFilter();
            console.log(this.filter.columns.map(column => column.id))
        },
        methods: {
            applyFilter: function() {
                this.generatedRequest =
                    `?start_date=${this.filter.startDate}` +
                    `&end_date=${this.filter.endDate}` +
                    `&status=${this.filter.status}` +
                    `&customer=${this.filter.customer}` +
                    `&shipment=${this.filter.shipment}` +
                    `&columns=${this.filter.columnSelections}` +
                    `&sort_by=${this.filter.sortBy}` +
                    `&sort_in=${this.filter.sortIn}` +
                    `&report_type=detail`;
            }
        },
        computed: {
            // generatedRequest: function() {
            //     return `?start_date=${this.filter.startDate}` +
            //         `&end_date=${this.filter.endDate}` +
            //         `&status=${this.filter.status}` +
            //         `&customer=${this.filter.customer}` +
            //         `&shipment=${this.filter.shipment}` +
            //         `&columns=${this.filter.columnSelections}` +
            //         `&sort_by=${this.filter.sortBy}` +
            //         `&sort_in=${this.filter.sortIn}` +
            //         `&report_type=detail`;
            // }
        }
    })
</script>
<script>
    $(function() {

    });
</script>
<script>
    // console.log(NioApp);
    $(function() {
        $('.start-date').datepicker({
            format: 'dd/mm/yyyy',
            todayBtn: false,
            clearBtn: true,
            // orientation: "bottom left",
            todayHighlight: true,
        }).on('changeDate', function(e) {
            app.$data.filter.startDate = e.format(0, 'yyyy-mm-dd');
        });

        $('.start-date').datepicker('setDate', new Date('{{ date("Y") }}', '{{ (int) date("m") - 1 }}', 1));
        $('.start-date').datepicker('update');

        $('.end-date').datepicker({
            format: 'dd/mm/yyyy',
            todayBtn: false,
            clearBtn: true,
            // orientation: "bottom left",
            todayHighlight: true,
        }).on('changeDate', function(e) {
            app.$data.filter.endDate = e.format(0, 'yyyy-mm-dd');
        });

        $('.end-date').datepicker('setDate', new Date('{{ date("Y") }}', '{{ (int) date("m") - 1 }}', '{{ date("t") }}'));
        $('.end-date').datepicker('update');

        $('.filter-customer').select2();
        $('.filter-customer').on('change', function() {
            app.$data.filter.customer = $(this).val();
        });

        $('.filter-shipment').select2();
        $('.filter-shipment').on('change', function() {
            app.$data.filter.shipment = $(this).val();
        });
    })
</script>
@endsection