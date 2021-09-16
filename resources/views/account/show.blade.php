@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
           
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
        <div class="col-lg-6 col-md-6" valign="right">

<div class="card card-bordered">
    <div class="card-inner-group">
        <div class="card-inner card-inner-md">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Info Akun</h6>
                </div>
     
            </div>
        </div><!-- .card-inner -->
        <div class="card-inner">
        <div class="card card-bordered">
        <ul class="data-list is-compact">
        <li class="data-item">
        <div class="data-col">
            <div class="data-label">Nama Akun</div>
            <div class="data-label">{{$account->name}}</div>
            
        </div>
        </li>
        <li class="data-item">
            <div class="data-col">
                <div class="data-label">Nomor AKun</div>
                <div class="data-label">{{$account->number}}</div>
            </div>
        </li>
        <li class="data-item">
            <div class="data-col">
                <div class="data-label">In</div>
                <div class="data-label">{{number_format($cash_in)}}</div>
            </div>
        </li>
        <li class="data-item">
            <div class="data-col">
                <div class="data-label">Out</div>
                <div class="data-label">{{number_format($cash_out)}}</div>
            </div>
        </li>
        <li class="data-item">
            <div class="data-col">
                <div class="data-label">Saldo</div>
                <div class="data-label">{{number_format($balance)}}</div>
            
            </div>
        </li>


    
    </ul>
    </div>
       
        </div>
    </div>
</div>
<br>
</div>
       
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="accountTransactions">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Catatan </th>
                                <!-- <th>Nomor Order</th> -->
                                <th>Type</th>
                                <th>In</th>
                                <th>Out</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- .nk-block -->
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
//  $('#accountTransactions').DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: '/datatables/account-transactions/'+<?php echo $account_id ?>,
//         columns: [
//             {data: 'id', name: 'id'},
//             {data: 'name', name: 'name'},
//             {data: 'email', name: 'email'},
//             {data: 'created_at', name: 'created_at'},
//             {data: 'updated_at', name: 'updated_at'}
//         ]
//     });
    var centralPurchaseTable = $(function() {
        $('#accountTransactions').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/account-transactions/'+<?php echo $account_id ?>,
                type: 'GET',
                // length: 2,
            },
            columns: [
            {data: 'date', name: 'date'},
            {data: 'note', name: 'note'},
            {data: 'type', name: 'type'},   
            {data: 'in', name: 'in'},
            {data: 'out', name: 'out'},
            {data: 'balance', name: 'balance'},
        ]
        });
        $('#centralPurchase').on('click', 'tr .btn-delete', function(e) {
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
    });
</script>
@endsection