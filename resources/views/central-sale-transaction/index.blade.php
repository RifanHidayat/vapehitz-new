@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Pembayaran Pelanggan</h3>
            <div class="nk-block-des text-soft">
                <p>Manage Pembayaran Pelanggan</p>
            </div>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-download-cloud"></em>
                                <span>Export</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-reports"></em>
                                <span>Reports</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div>
<div class="nk-block">
    <div class="alert alert-primary alert-icon">
        <em class="icon ni ni-alert-circle"></em> <strong>Pembayaran</strong> dapat dilakukan di dalam menu <a href="/central-sale" style="text-decoration: underline;">Penjualan Pusat</a> dan <a href="/customer" style="text-decoration: underline;">Customer</a>
    </div>
</div>
<div class="nk-block nk-block-lg">
    <!-- <a href="/central-sale/create" class="btn btn-primary">Tambah</a> -->
    <!-- <p></p>
        <p></p> -->
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div class="table-responsive">
                <table class="table table-striped" id="centralSaleTransaction">
                    <thead>
                        <tr class="text-center">
                            <th>No. Transaksi</th>
                            <th>Tanggal Transaksi</th>
                            <th>Keterangan</th> 
                            <th>Invoice</th> 
                            <th>Jumlah</th>
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
                        return axios.delete('/central-sale-transaction/' + id)
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
            NioApp.DataTable('#centralSaleTransaction', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/datatables/central-sale-transactions',
                    type: 'GET',
                    // length: 2,
                },
                columns: [{
                        data: 'code',
                        name: 'central_sale_transactions.code',
                        className: 'text-center',
                    },
                    {
                        data: 'date',
                        name: 'central_sale_transactions.date',
                        className: 'text-center',
                    },
                    {
                        data: 'note',
                        name: 'central_sale_transactions.note',
                        className: 'text-center',
                    },
                    {
                        data: 'invoice_number',
                        name: 'centralSales.code',
                        // render: function(data, type) {
                        //     return `<div class="text-dark-75 font-weight-bolder font-size-lg mb-0">${data.length > 0 && data.map(item => `<span class="label label-light-info label-pill label-inline text-capitalize">${item.number}</span>`).join('')}</div>`;
                        // },
                    },
                    {
                        data: 'amount',
                        name: 'central_sale_transactions.amount',
                        render: function(data) {
                            return Intl.NumberFormat('de-DE').format(data);
                        },
                        className: 'text-right',
                    },
                    // {
                    //     data: 'account.name',
                    //     name: 'central_sale_transactions.amount',
                    //     className: 'text-center',
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        // ordering: false,
                    },

                ]
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

        $('#centralSaleTransaction').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/central-sale-transaction/' + id)
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
    });
</script>
@endsection