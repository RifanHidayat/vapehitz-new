@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Transaksi Pembelian</h3>
            <div class="nk-block-des text-soft">
                <ul class="list-inline">
                    <li>Nomor Transaksi: <span class="text-base">{{$purchaseTransaction->code}}</span></li>
                    <li>Tanggal Transaksi: <span class="text-base">{{$purchaseTransaction->date}}</span></li>
                   
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/purchase-transaction" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>
</div>

<div class="row g-gs align-items-start">
        <div class="col-lg-6 col-md-12">
            <div class="card card-bordered h-100">
            <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Supplier</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="icon ni ni-layers mr-2" style="font-size: 2em;"></i>
                                <div class="info">
                                    <span class="title">Kode</span>
                                    <p class="amount" ><strong>{{$purchaseTransaction->supplier->code}}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                            <em class="far fa-building" style="font-size: 2em;margin-right:10px"></em>
                                <div class="info">
                                    <span class="title">Nama</span>
                                    <p class="text-lg"><strong>{{$purchaseTransaction->supplier->code}}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Detail Harga</h6>
                            </div>
                            <!-- <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="modal" href="#exampleModal" data-backdrop="static" data-keyboard="false"><em class="icon ni ni-plus"></em></a>
                                    </li>
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" @click.prevent="removeAllSelectedProducts"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
                        </div>
                    </div><!-- .card-inner -->
                    <ul class="data-list is-compact">
                    
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Jumlah Bayar</div>
                            <div class="data-value">{{ number_format($purchaseTransaction->amount) }}</div>
                       
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Akun Pembayaran</div>
                            <div class="data-value">{{ $purchaseTransaction->payment_method }} ({{ $purchaseTransaction->account->name }})</div>
                        </div>
                    </li>
                    
                </ul>
  
                </div><!-- .card-inner-group -->
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            
                

                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Detail Pembelian</h6>
                                </div>
                                
                                <!-- <div class="card-tools mr-n1">
                                <div class="drodown">
                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><em class="icon ni ni-plus"></em><span>Tambah</span></a></li>
                                            <li><a href="#"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                            </div>
                        </div><!-- .card-inner -->
                       
                        <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Tanggal</th>
                                        <th>Nomor Order</th>
                                        <td align="right" ><b>Jumlah Bayar</b></td>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subTotal = 0; @endphp
                                    @foreach($purchaseTransaction->centralPurchases as $transaction)
                                    <tr>
                                        <td>{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                                        <td><a href="/central-purchase/show/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
                                        <td align="right" class="text-right">{{ number_format($transaction->pivot->amount) }}</td>
                                    </tr>
                                    @php $subTotal += $transaction->pivot->amount; @endphp
                                    @endforeach
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
        data: {
            selectedProducts: JSON.parse('{!! $purchaseTransaction->products !!}'),
            loading: false,
        },
    })
</script>
@endsection