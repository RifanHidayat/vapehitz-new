@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Detail Transaksi Pembelian</h3>
            <div class="nk-block-des text-soft">
                <ul class="list-inline">
                    <li>Nomor Order: <span class="text-base">{{$centralPurchase->code}}</span></li>
                    <li>Created At: <span class="text-base">{{$centralPurchase->created_at}}</span></li>
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/central-purchase" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>
</div>
<div class="nk-block">
    <div class="row gy-5">
        <div class="col-lg-7">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail Produk</h4>
                </div>
            </div>
            <div v-for="product in selectedProducts" class="card card-bordered">
            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;" align="left">Nomor Produk</th>
                                        <th style="width: 20%;" align="left">Nama Produk</th>
                                        <th style="width: 15%;" align="left">Harga</th>
                                        <th style="width: 15%;" align="left">Quantity</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                    @foreach($centralPurchase->products as $product)
                                    <tr>
                                        <td class="text-left">{{ $product->code}}</td>
                                        <td class="text-left">{{ $product->name}}</td>
                                        <td class="text-right">{{ number_format($product->pivot->price)}}</td>
                                        <td class="text-right">{{ $product->pivot->quantity}}</td>
                                    </tr>
                                   
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
                            <div class="data-label">Biaya Pengiriman</div>
                            <div class="data-value">{{ number_format($centralPurchase->shipping_cost) }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">diskon</div>
                            <div class="data-value">{{ number_format($centralPurchase->discount) }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Net</div>
                            <div class="data-value">{{ number_format($centralPurchase->netto) }}</div>
                        </div>
                    </li>

                    <!-- <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Total</div>
                            <div class="data-value">{{ number_format($centralPurchase->total) }}</div>
                        </div>
                    </li> -->
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Jumlah Bayar</div>
                            <div class="data-value">{{ number_format($payAmount) }}</div>
                        </div>
                    </li>
                   
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Sisa</div>
                            <div class="data-value">{{ number_format($centralPurchase->netto-$payAmount)}}</div>
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
                            <div class="data-value text-right">{{$centralPurchase->supplier->name}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Kode Supplier</div>
                            <div class="data-value">{{$centralPurchase->supplier->code}}</div>
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
                            <div class="data-value">{{$centralPurchase->code}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Tanggal Transaksi</div>
                            <div class="data-value">{{ $centralPurchase->date }}</div>
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
            selectedProducts: JSON.parse('{!! $centralPurchase->products !!}'),
            loading: false,
        },
    })
</script>
@endsection