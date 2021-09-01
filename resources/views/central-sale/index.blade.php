@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
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
                                <th>Total</th>
                                <th>Discount</th>
                                <th>Shipping</th>
                                <th>Grand Total</th>
                                <th>Pembayaran 1</th>
                                <th>Pembayaran 2</th>
                                <th>Sisa</th>
                                <th>Berat (gr)</th>
                                <th>Status</th>
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
        var centralSaleTable = $('#centralSale').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/central-sale',
                type: 'GET',
                // length: 2,
            },
            columns: [{
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'shipment_name',
                    name: 'shipments.name'
                },
                {
                    data: 'recipient',
                    name: 'recipient'
                },
                {
                    data: 'subtotal',
                    name: 'subtotal'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'shipping_cost',
                    name: 'shipping_cost'
                },
                {
                    data: 'net_total',
                    name: 'net_total'
                },
                {
                    data: 'receive_1',
                    name: 'receive_1'
                },
                {
                    data: 'receive_2',
                    name: 'receive_2'
                },
                {
                    data: 'remaining_payment',
                    name: 'remaining_payment'
                },

                {
                    data: 'total_weight',
                    name: 'total_weight'
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });
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
    });
</script>
@endsection