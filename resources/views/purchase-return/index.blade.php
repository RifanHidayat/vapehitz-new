@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <h2 class="nk-block-title fw-normal">Retur Pembelian Barang</h2>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        
        
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table style="width: 100%;"  class="table table-striped" id="purchaseReturn">
                        <thead>
                            <tr>
                                <!-- <th>Nomor Order</th> -->
                                <th>Nomor retur</th>
                                <th>Tanggal retur</th>
                                <th>Nama Supplier</th>
                                <th>quantity Retur</th>
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
</div>
@endsection
@section('pagescript')
<script>

var centralPurchaseTable = $(function() {
        $('#purchaseReturn').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
            ajax: {
                url: '/datatables/purchase-returns',
                type: 'GET',
                // length: 2,
            },
            columns: [
                {
                    data: 'code',
                    name: 'purchase_returns.code'
                },
                {
                    data: 'date',
                    name: 'purchase_returns.date'
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
        });
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
    });

</script>
@endsection