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
                    <li>Created At: <span class="text-base">{{$purchaseTransaction->created_at}}</span></li>
                   
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/purchase-transaction" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>
</div>
<div class="nk-block">
    <div class="row gy-5">
        <div class="col-lg-7">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail pembelian</h4>
                </div>
            </div>
            <div v-for="product in selectedProducts" class="card card-bordered">
            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nomor Order</th>
                                        <th align="right" ><center>Jumlah Bayar</center></th>
                                        
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
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail Harga</h4>
                </div>
            </div>
            <div class="card card-bordered">
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
            </div>
        </div><!-- .col -->
        <div class="col-lg-5">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail Supplier</h4>
                </div>
            </div><!-- .nk-block-head -->
            <div class="card card-bordered">
                <ul class="data-list is-compact">
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Nama Supplier</div>
                            <div class="data-value text-right">{{$purchaseTransaction->supplier->name}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Kode Supplier</div>
                            <div class="data-value">{{$purchaseTransaction->supplier->code}}</div>
                        </div>
                    </li>
            </div><!-- .card -->
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail Transaksi</h4>
                </div>
            </div><!-- .nk-block-head -->
            <div class="card card-bordered">
                <ul class="data-list is-compact">
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Kode Transaksi</div>
                            <div class="data-value">{{$purchaseTransaction->code}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Tanggal Transaksi</div>
                            <div class="data-value">{{ $purchaseTransaction->date }}</div>
                        </div>
                    </li>
                </ul>
            </div><!-- .card -->
        </div><!-- .col -->
    </div><!-- .row -->
</div>
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