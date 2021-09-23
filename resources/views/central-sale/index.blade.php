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
        <a href="/central-sale/create" class="btn btn-primary">Tambah</a>
        <p></p>
        <p></p>
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-striped" id="centralSale">
                        <thead>
                            <tr class="text-center">
                                <th>No. Invoice</th>
                                <th>Tanggal Invoice</th>
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
                                <th>Action</th>
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
        methods: {
            deleteRow: function(id) {
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
                                // invoicesTable.ajax.reload();
                            }
                        })
                    }
                })
            }
        }
    })
</script>
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#centralSale', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/datatables/central-sale',
                    type: 'GET',
                    // length: 2,
                },
                columns: [{
                        data: 'code',
                        name: 'central_sales.code'
                    },
                    {
                        data: 'date',
                        name: 'central_sales.date'
                    },
                    {
                        data: 'shipment_name',
                        name: 'shipment.name'
                    },
                    {
                        data: 'recipient',
                        name: 'central_sales.recipient'
                    },
                    {
                        data: 'subtotal',
                        name: 'central_sales.subtotal',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'discount',
                        name: 'central_sales.discount',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'shipping_cost',
                        name: 'central_sales.shipping_cost',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'net_total',
                        name: 'central_sales.net_total',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'receive_1',
                        name: 'central_sales.receive_1',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'receive_2',
                        name: 'central_sales.receive_2',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'remaining_payment',
                        name: 'central_sales.remaining_payment',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },

                    {
                        data: 'total_weight',
                        name: 'central_sales.total_weight',
                        className: 'text-right',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        }
                    },
                    {
                        data: 'status',
                        name: 'central_sales.status',
                    },
                    {
                        data: 'print_status',
                        name: 'is_printed',
                        className: 'text-center',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                    },

                ],
                // responsive: {
                //     details: true
                // }
            });
            $.fn.DataTable.ext.pager.numbers_length = 7;
        };

        NioApp.DataTable.init();
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
            } else {
                let a = document.createElement('a');
                a.target = '_blank';
                a.href = '/central-sale/print/' + id;
                a.click();
            }
        })
    });
</script>
<script>
    console.log(NioApp);
</script>
@endsection