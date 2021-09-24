@extends('layouts.app')

@section('title', 'Vapehitz')
@section('pagestyle')
<style>
    #customers tr th,
    #customers tr td {
        font-size: 0.875rem;
    }
</style>
@endsection
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Pembelian</h3>
            <div class="nk-block-des text-soft">
                <p>Manage Pembelian</p>
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
                        @if(in_array("add_purchase_product", $permission))
                        <li>
                            <a href="/central-purchase/create" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em>
                                <span>New Order</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div>
<div class="nk-block nk-block-lg">
    <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
    <!-- <a href="{{url('/central-purchase/create')}}" class="btn btn-outline-success">Tambah Pembelian Barang</a> -->
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
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#centralPurchase', {
                processing: true,
                serverSide: true,
                // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
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
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

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
    })
</script>
@endsection