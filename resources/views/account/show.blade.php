@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<!-- <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Akun {{$account->name}} ({{$account->number}})</h3>
            <div class="nk-block-des text-soft">
                <ul class="list-inline">



                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/purchase-transaction" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>
</div> -->

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Detail Akun</h3>
            <div class="nk-block-des text-soft">
                <p>Akun {{$account->name}} ({{$account->number}})</p>
            </div>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="/account/export/{{$account->id}}"  class="btn btn-white btn-dim btn-outline-primary" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-download-cloud"></em>
                                <span>Export</span>
                            </a>
                        </li>
                        <li>
                            <a href="/account/reports/{{$account->id}}" class="btn btn-white btn-dim btn-outline-primary" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-reports"></em>
                                <span>Reports</span>
                            </a>
                        </li>
                      
                        <li>
                        <a href="/account" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        </li>
                     
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div>
<div class="nk-block nk-block-lg">

    <div class="card card-bordered mb-3">
        <div class="card-inner">
            <div class="row" >
                <div class="col" >
                    <div class="d-flex align-items-center" >
                        <em class="icon ni ni-coin mr-2" style="font-size: 2em;"></em>
                        <div class="info">
                            <span class="title">Total In</span>
                            <p class="amount"><strong>{{number_format($cash_in)}}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col" >
                    <div class="d-flex justify-content-center align-items-center">
                        <em class="icon ni ni-coin mr-2" style="font-size: 2em;"></em>
                        <div class="info">
                            <span class="title">Total Out</span>
                            <p class="amount"><strong>{{number_format($cash_out)}}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-end align-items-end">
                        <em class="icon ni ni-coin mr-2" style="font-size: 2em;"></em>
                        <div class="info">
                            <span class="title">Balance</span>
                            <p class="amount"><strong>{{number_format($balance)}}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">



       
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-striped" id="accountTransactions">
                    <thead>
                        <tr>
                            <th style="width:6%">Tanggal</th>
                            <th style="width:20%">Deskripsi </th>
                            <!-- <th>Nomor Order</th> -->
                            <th style="width:25%">Catatan</th>
                            <th style="width:13%" style="text-align: right;">In</th>
                            <th style="width:13%" style="text-align: right;">Out</th>
                            <th style="width:15%" style="text-align: right;">Saldo</th>
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
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#accountTransactions', {
            processing: true,
            serverSide: true,
            
            ajax: {
                url: '/datatables/account-transactions/' + <?php echo $account_id ?>,
                type: 'GET',
                // length: 2,
            },
            columns: [{
                    data: 'date',
                    name: 'date'
                },
               
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'note',
                    name: 'note'
                },
                {
                    data: 'in',
                    name: 'in'
                },
                {
                    data: 'out',
                    name: 'out'
                },
                {
                    data: 'balance',
                    name: 'balance'
                },
            ]
        });
        $.fn.DataTable.ext.pager.numbers_length = 7;
    }
    NioApp.DataTable.init();
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