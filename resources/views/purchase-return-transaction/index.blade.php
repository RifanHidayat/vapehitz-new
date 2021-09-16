@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <h2 class="nk-block-title fw-normal">History Penyelesain retur</h2>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        
        <p></p>
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="purchaseReturnTransaction">
                        <thead>
                            <tr>
                                <!-- <th>Nomor Order</th> -->
                                <th>Tanggal retur</th>
                                <th>Nomor Retur</th>
                               
                              
                               
                                <th>Nama Supplier</th>
                                <th>Jumlah bayar</th>
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
    </div>
</div>
@endsection
@section('pagescript')
<script>

var centralPurchaseTable = $(function() {
        $('#purchaseReturnTransaction').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/purchase-return-transactions',
                type: 'GET',
                // length: 2,
            },
            columns: [
                {
                    data: 'date',
                    name: 'purchase_return_transactions.date'
                },
                {
                    data: 'code',
                    name: 'purchase_return_transactions.code'
                },
               
                
              
                
                {
                    data: 'supplier_name',
                    name: 'supplier_name'
                },
                // {
                //     data: 'amount',
                //     name: 'purchase_returns.amount'
                // },
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
        });
        $('#purchaseReturnTransaction').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/purchase-return-transaction/' + id)
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