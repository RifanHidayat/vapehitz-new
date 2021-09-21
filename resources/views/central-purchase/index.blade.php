@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .dataTables_filter {
        text-align: right;
        width: 90%;
    }

    table tr th {
        font-size: 15px;
        /* color: black; */
    }

    table tr td {
        font-size: 13px;
        /* color: black; */
    }

    .pull-left {
        float: left !important;
    }

    .pull-right {
        float: right !important;
        margin-bottom: 20px;
    }

    .bottom {
        float: right !important;
    }
</style>
@section('content')

<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
        <h2 class="nk-block-title fw-normal">Transaksi Pembelian Barang</h2>
        <!-- <div class="nk-block-des">
                <p class="lead">Manage Supplier</p>
            </div> -->
    </div>
</div><!-- .nk-block -->
<div class="nk-block nk-block-lg">
    <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
    <a href="{{url('/central-purchase/create')}}" class="btn btn-outline-success">Tambah Pembelian Barang</a>
    <p></p>
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-striped" id="centralPurchase">
                    <thead>
                        <tr>
                            <th>Tanggal Order</th>
                            <th>Nomor Order</th>
                            <th>Nomor Invoice</th>
                            <th>Nama Supplier</th>
                            <th>Net Total</th>
                            <th>Jumlah Bayar</th>
                            <th>Sisa bayar</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- .nk-block -->

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
                        return axios.delete('/central-purchase/' + id)
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
    var centralPurchaseTable = $(function() {
        $('#centralPurchase').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
            ajax: {
                url: '/datatables/central-purchases',
                type: 'GET',
                // length: 2,
            },
            columns: [{
                    data: 'date',
                    name: 'central_purchases.date'
                },
                {
                    data: 'code',
                    name: 'central_purchases.code'
                },
                {
                    data: 'invoice_number',
                    name: 'central_purchases.invoice_number'
                },
                {
                    data: 'supplier_name',
                    name: 'supplier_name',
                },

                {
                    data: 'netto',
                    name: 'netto'
                },
                {
                    data: 'payAmount',
                    name: 'payAmount'
                },



                {
                    data: 'remainingAmount',
                    name: 'remainingAmount'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });
        $('#centralPurchase').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/central-purchase/' + id)
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