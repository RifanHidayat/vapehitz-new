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
                            <a href="/account/export/{{$account->id}}?start_date={{$start_date}}&end_date={{$end_date}}"  class="btn btn-white btn-dim btn-outline-primary" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-download-cloud"></em>
                                <span>Export</span>
                            </a>
                        </li>
                        <li>
                            <a href="/account/reports/{{$account->id}}?start_date={{$start_date}}&end_date={{$end_date}}" class="btn btn-white btn-dim btn-outline-primary" data-toggle="tooltip" data-placement="top" title="On Development">
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
<div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title" style="width:100%">
                                <div style="float:left" >
                                   
                               </div>
                                <div style="float:right" align="right">
                               
                                <a class="btn btn-primary" data-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter"><em class="icon ni ni-setting align-middle"></em>&nbsp; Filter</a>
                               
                               </div>
                            </div>
                            
                        </div>
                    </div><!-- .card-inner -->
                    <div class="collapse row" id="collapseFilter">
                    <div class="col-md-12">
                    
                        <div class="form-group">
                            
                            <div class="row" style="margin-left: 5px;">
                                
                                <div class="col-md-4">
                                
                                
                                    <div class="form-control-wrap">
                                    <label class="form-label" for="default-06">Start Date</label>
                                       
                                        <input type="date" class="form-control start-date" v-model="startDate">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control-wrap">
                                    <label class="form-label" for="default-06">End Date</label>
                                        
                                        <input type="date" class="form-control end-date" v-model="endDate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        
                        
                        </div>
                       
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button  style="margin-right: 20px;margin-bottom:10px" type="button" class="btn btn-primary" @click="applyFilter">Apply</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">

                    </div> -->
                </div>
                   
                </div><!-- .card-inner-group -->
            </div>


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
    <div class="nk-block nk-block-lg">
       
        
    </div>

    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">

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
            },
            applyFilter: function() {
                window.location.href = '/account/show/'+<?php echo $account_id ?>+`?start_date=${this.startDate}&end_date=${this.endDate}`; 
                tes()

                
            }
        }
    })
</script>
 


<script>
 
    var centralPurchaseTable = $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#accountTransactions', {
            processing: true,
            serverSide: true,
            
            ajax: {
                url: '/datatables/account-transactions/<?php echo $account_id?>?start_date=<?php echo $start_date ?>&end_date=<?php echo $end_date ?>',
                type: 'GET',
                // length: 2,
            },
            columns: [
                {
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
 
    });
   
</script>
@endsection