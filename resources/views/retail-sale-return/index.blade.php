@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h4 class="nk-block-title fw-normal">Retur Penjualan Barang Retail</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <!-- <a href="/central-sale/create" class="btn btn-primary">Tambah</a> -->
        <!-- <p></p>
        <p></p> -->
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-striped" id="retailSaleReturn">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <!-- <th>Sisa Pembayaran</th> -->
                                <th>No. Invoice</th>
                                <!-- <th>Akun</th> -->
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
                        return axios.delete('/retail-sale-return/' + id)
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
        var datatable = $('#retailSaleReturn').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/retail-sale-returns',
                type: 'GET',
                // length: 2,
            },
            columns: [{
                    data: 'code',
                    name: 'retail_sale_returns.code',
                    className: 'text-center',
                },
                {
                    data: 'date',
                    name: 'retail_sale_returns.date',
                    className: 'text-center',
                },
                {
                    data: 'amount',
                    name: 'retail_sale_returns.amount',
                    render: function(data) {
                        return Intl.NumberFormat('de-DE').format(data);
                    },
                    className: 'text-right',
                },
                // {
                //     data: 'retur_sale_return_transactions',
                //     render: function(data, type, row) {
                //         const paid = data.map(transaction => transaction.amount).reduce((acc, cur) => {
                //             return acc + cur;
                //         }, 0);
                //         const unpaid = row.amount - paid;
                //         return Intl.NumberFormat('de-DE').format(unpaid);
                //     },
                //     className: 'text-right',
                // },
                {
                    data: 'retail_sale.code',
                    name: 'retailSale.code',
                    className: 'text-center',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    // ordering: false,
                },

            ]
        });
        $('#retailSaleReturn').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/retail-sale-return/' + id)
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