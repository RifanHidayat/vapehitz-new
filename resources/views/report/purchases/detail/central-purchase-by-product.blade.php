@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h4 class="nk-block-title fw-normal">Transaksi Penjualan Barang</h4>
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
                            <a class="dropdown-item" :href="'/central-purchase/report-by-product/sheet' + generatedRequest" target="_blank">.xlsx</a>
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
                            </div> -->
                            <div class="form-group col-md-4">
                                <label class="form-label" for="filter-supplier">Supplier</label>
                                <div class="form-control-wrap ">
                                    <select class="form-control filter-supplier" id="filter-supplier">
                                        <option value="">All</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="form-group col-md-4">
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
                <div class="table-responsive">
                    <table class="table table-striped" id="centralSale">
                        <thead>
                            <tr class="text-center">
                                <th>No. Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Supplier</th>
                                <th>Shipment</th>
                                <!-- <th>Sales</th> -->
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
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
                        id: 'supplier',
                        text: 'Supplier'
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
                columnSelections: ['number', 'date', 'supplier', 'shipment', 'recipient', 'subtotal', 'discount', 'shipping_cost', 'net_total', 'receive_1', 'receive_2', 'remaining_payment', 'total_weight', 'status', 'is_printed'],
                status: '',
                supplier: '',
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
                    `&supplier=${this.filter.supplier}` +
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
        let urlRequest = function() {
            return `?start_date=${app.$data.filter.startDate}` +
                `&end_date=${app.$data.filter.endDate}` +
                `&status=${app.$data.filter.status}` +
                `&supplier=${app.$data.filter.supplier}` +
                `&shipment=${app.$data.filter.shipment}` +
                `&columns=${app.$data.filter.columnSelections}` +
                `&sort_by=${app.$data.filter.sortBy}` +
                `&sort_in=${app.$data.filter.sortIn}` +
                `&report_type=detail`;
        }

        // NioApp.DataTable.init = function() {
        //     NioApp.DataTable('#centralSale', {
        //         processing: true,
        //         serverSide: true,
        //         ajax: {
        //             url: '/datatables/reports/central-sale-detail' + urlRequest(),
        //             type: 'GET',
        //             // length: 2,
        //         },
        //         columns: [{
        //                 data: 'code',
        //                 name: 'central_sales.code'
        //             },
        //             {
        //                 data: 'date',
        //                 name: 'central_sales.date'
        //             },
        //             {
        //                 data: 'customer.name',
        //                 name: 'customer.name'
        //             },
        //             {
        //                 data: 'shipment_name',
        //                 name: 'shipment.name'
        //             },
        //             {
        //                 data: 'recipient',
        //                 name: 'central_sales.recipient'
        //             },
        //             {
        //                 data: 'subtotal',
        //                 name: 'central_sales.subtotal',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'discount',
        //                 name: 'central_sales.discount',
        //                 className: 'text-right',
        //                 render: function(data, type, row) {
        //                     let discount = Intl.NumberFormat('de-DE').format(data);
        //                     if (row.discount_type == 'percentage') {
        //                         discount += '%';
        //                     }
        //                     return discount;
        //                 }
        //             },
        //             {
        //                 data: 'shipping_cost',
        //                 name: 'central_sales.shipping_cost',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'net_total',
        //                 name: 'central_sales.net_total',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'receive_1',
        //                 name: 'central_sales.receive_1',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'receive_2',
        //                 name: 'central_sales.receive_2',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'remaining_payment',
        //                 name: 'central_sales.remaining_payment',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },

        //             {
        //                 data: 'total_weight',
        //                 name: 'central_sales.total_weight',
        //                 className: 'text-right',
        //                 render: function(data) {
        //                     return Intl.NumberFormat('de-DE').format(data);
        //                 }
        //             },
        //             {
        //                 data: 'status',
        //                 name: 'central_sales.status',
        //             },
        //             {
        //                 data: 'print_status',
        //                 name: 'central_sales.is_printed',
        //                 className: 'text-center',
        //             },
        //             // {
        //             //     data: 'action',
        //             //     name: 'action',
        //             //     searchable: false,
        //             // },

        //         ],
        //         // responsive: {
        //         //     details: true
        //         // }
        //     });
        //     $.fn.DataTable.ext.pager.numbers_length = 7;
        // };

        // NioApp.DataTable.init();
        // var centralSaleTable = $('#centralSale').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: {
        //         url: '/datatables/central-sale',
        //         type: 'GET',
        //         // length: 2,
        //     },
        //     columns: [{
        //             data: 'code',
        //             name: 'central_sales.code'
        //         },
        //         {
        //             data: 'date',
        //             name: 'central_sales.date'
        //         },
        //         {
        //             data: 'shipment_name',
        //             name: 'shipment.name'
        //         },
        //         {
        //             data: 'recipient',
        //             name: 'central_sales.recipient'
        //         },
        //         {
        //             data: 'subtotal',
        //             name: 'central_sales.subtotal',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'discount',
        //             name: 'central_sales.discount',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'shipping_cost',
        //             name: 'central_sales.shipping_cost',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'net_total',
        //             name: 'central_sales.net_total',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'receive_1',
        //             name: 'central_sales.receive_1',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'receive_2',
        //             name: 'central_sales.receive_2',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'remaining_payment',
        //             name: 'central_sales.remaining_payment',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },

        //         {
        //             data: 'total_weight',
        //             name: 'central_sales.total_weight',
        //             className: 'text-right',
        //             render: function(data) {
        //                 return Intl.NumberFormat('de-DE').format(data);
        //             }
        //         },
        //         {
        //             data: 'status',
        //             name: 'central_sales.status',
        //         },
        //         {
        //             data: 'print_status',
        //             name: 'is_printed',
        //             className: 'text-center',
        //         },
        //         {
        //             data: 'action',
        //             name: 'action',
        //             searchable: false,
        //         },

        //     ]
        // });
        $('#centralSale').on('click', 'tr .btn-delete', function(e) {
            e.preventDefault();
            // alert('click');
            const id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "The data will be deleted",
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.delete('/central-sale/' + id)
                        .then(function(response) {
                            console.log(response.data);
                        })
                        .catch(function(error) {
                            console.log(error.data);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops',
                                text: 'Something wrong',
                            })
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been deleted',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();

                        }
                    })
                }
            })
        })

        $('#centralSale').on('click', 'tr .btn-print', function(e) {
            e.preventDefault();
            // let a = document.createElement('a');
            // a.target = '_blank';
            // a.href = 'https://support.wwf.org.uk/';
            // a.click();
            const isPrinted = $(this).attr('data-print');
            if (isPrinted == 0) {
                // alert('click');
                const id = $(this).attr('data-id');
                Swal.fire({
                    title: 'Tandai Sudah Dicetak?',
                    text: "Data akan ditandai sudah dicetak",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    showLoaderOnConfirm: true,
                    // allowOutsideClick: false,
                    preConfirm: () => {
                        return axios.post('/central-sale/action/update-print-status/' + id)
                            .then(function(response) {
                                // console.log(response.data);
                                centralSaleTable.ajax.reload();
                            })
                            .catch(function(error) {
                                console.log(error.data);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops',
                                    text: 'Something wrong',
                                })
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result) {
                        let a = document.createElement('a');
                        a.target = '_blank';
                        a.href = '/central-sale/print/' + id;
                        a.click();
                    }
                    // if (result.isConfirmed) {
                    //     Swal.fire({
                    //         icon: 'success',
                    //         title: 'Success',
                    //         text: 'Data has been deleted',
                    //     }).then((result) => {
                    //         if (result.isConfirmed) {
                    //             window.location.reload();

                    //         }
                    //     })
                    // }
                })
            }
        })
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

        $('.filter-supplier').select2();
        $('.filter-supplier').on('change', function() {
            app.$data.filter.supplier = $(this).val();
        });

        $('.filter-shipment').select2();
        $('.filter-shipment').on('change', function() {
            app.$data.filter.shipment = $(this).val();
        });
    })
</script>
@endsection