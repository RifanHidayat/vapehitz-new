@extends('layouts.app')

@section('title', 'Vapehitz')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Retur Barang Pembelian</h3>
            <div class="nk-block-des text-soft">
                <p>Manage Retur Barang Pembelian</p>
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
        <em class="icon ni ni-alert-circle"></em> <strong>Retur</strong> dapat dilakukan di dalam menu <a href="/central-purchase" style="text-decoration: underline;">Pembelian</a>
    </div>
</div>
<div class="nk-block nk-block-lg">
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-striped" id="purchaseReturn">
                    <thead>
                        <tr>
                            <!-- <th>Nomor Order</th> -->
                            <th>Nomor retur</th>
                            <th>Tanggal retur</th>
                            <th>Nomor Order</th>
                            <th>Nama Supplier</th>
                            <th>Quantity Retur</th>
                            <th>Nominal retur</th>
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
</div>

@endsection
@section('pagescript')
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#purchaseReturn', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/datatables/purchase-returns',
                    type: 'GET',
                    // length: 2,
                },

                columns: [{
                        data: 'code',
                        name: 'purchase_returns.code'
                    },
                    {
                        data: 'date',
                        name: 'purchase_returns.date'
                    },

                    {
                        data: 'central_purchase_code',
                        name: 'central_purchase_code'
                    },

                    {
                        data: 'supplier_name',
                        name: 'supplier.name',
                    },
                    {
                        data: 'quantity',
                        name: 'purchase_returns.quantity'
                    },

                    {
                        data: 'amount',
                        name: 'amount'
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
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

        $('#purchaseReturn').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/purchase-return/' + id)
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