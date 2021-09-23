@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Pembayaran Supplier</h3>
            <div class="nk-block-des text-soft">
                <p>Manage Pembayaran Supplier</p>
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
        <em class="icon ni ni-alert-circle"></em> <strong>Pembayaran</strong> dapat dilakukan di dalam menu <a href="/central-purchase" style="text-decoration: underline;">Pembelian</a> dan <a href="/supplier" style="text-decoration: underline;">Supplier</a>
    </div>
</div>
<div class="nk-block nk-block-lg">

    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-striped" id="centralPurchaseTransaction">
                    <thead>
                        <tr>
                            <th>Tanggal Pembayaran</th>
                            <th>Nomor Transaksi</th>
                            <!-- <th>Nomor Order</th> -->
                            <th>Nama Supplier</th>
                            <th>Amount</th>
                            <th>Akun</th>
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
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#centralPurchaseTransaction', {
                processing: true,
                serverSide: true,
                // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
                ajax: {
                    url: '/datatables/purchases-transactions',
                    type: 'GET',
                    // length: 2,
                },
                columns: [{
                        data: 'date',
                        name: 'purchase_transactions.date'
                    },
                    {
                        data: 'code',
                        name: 'purchase_transactions.code'
                    },
                    // {
                    //     data: 'central_purchases.code',
                    //     name: 'central_purchases.code'
                    // },
                    {
                        data: 'supplier_name',
                        name: 'supplier.name',
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ]
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

        $('#centralPurchaseTransaction').on('click', 'tr .btn-delete', function(e) {
            e.preventDefault();
            // alert('click');
            const id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "The data will be deletedd",
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.delete('/purchase-transaction/' + id)
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
    })
</script>
@endsection