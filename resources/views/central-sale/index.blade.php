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
                    <table class="table table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>No. Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Shipment</th>
                                <th>Sales</th>
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
                            @foreach($centralSale as $centralSales)
                            <tr class="text-center">
                                <td>{{$centralSales->code}}</td>
                                <td>{{$centralSales->date}}</td>
                                <td>{{$centralSales->shipment->name}}</td>
                                <td></td>
                                <td>{{$centralSales->recipient}}</td>
                                <td>{{$centralSales->subtotal}}</td>
                                <td>{{$centralSales->discount}}</td>
                                <td>{{$centralSales->shipping_cost}}</td>
                                <td>{{$centralSales->net_total}}</td>
                                <td>{{$centralSales->receive_1}}</td>
                                <td>{{$centralSales->receive_2}}</td>
                                <td>{{$centralSales->remaining_payment}}</td>
                                <td>{{$centralSales->total_weight}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
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
<!-- <script>
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
                    name: 'central_sales.code'
                },
                {
                    data: 'date',
                    name: 'central_sales.date'
                },
                {
                    data: 'shipments',
                    name: 'shipments.name'
                },
                {
                    data: 'recipient',
                    name: 'central_sales.recipient'
                },
                {
                    data: 'discount',
                    name: 'central_sales.discount'
                },
                {
                    data: 'shipping_cost',
                    name: 'central_sales.shipping_cost'
                },
                {
                    data: 'net_total',
                    name: 'central_sales.net_total'
                },
                {
                    data: 'receive_1',
                    name: 'central_sales.receive_1'
                },
                {
                    data: 'receive_2',
                    name: 'central_sales.receive_2'
                },
                {
                    data: 'remaining_payment',
                    name: 'central_sales.remaining_payment'
                },

                {
                    data: 'total_weight',
                    name: 'central_sales.total_weight'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(row) {
                        if (row == '1')
                            return '<span class="badge badge-outline-success">Active</span>'
                        else
                            return '<span class="badge badge-outline-danger">Inactive</span>'
                    },
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });
    });
</script> -->
@endsection