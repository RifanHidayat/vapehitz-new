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
                    <li>Tanggal Order: <span class="text-base">{{$centralPurchase->date}}</span></li>
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/central-purchase" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
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
                                <p class="amount"><strong>{{$centralPurchase->supplier->code}}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <em class="far fa-building" style="font-size: 2em;margin-right:10px"></em>
                            <div class="info">
                                <span class="title">Nama</span>
                                <p class="text-lg"><strong>{{$centralPurchase->supplier->name}}</strong></p>
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
                            <h6 class="title">Detail Produk</h6>
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
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 15%;" align="left">Nomor Produk</th>
                            <th style="width: 20%;" align="left">Nama Produk</th>
                            <td style="width: 15%;" align="right"><b>Harga</b></td>
                            <td style="width: 15%;" align="right"><b>Quantity</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($centralPurchase->products as $product)
                        <tr>
                            <td class="text-left">{{ $product->code}}</td>
                            <td class="text-left">{{ $product->name}}</td>
                            <td class="text-right">{{ number_format($product->pivot->price)}}</td>
                            <td class="text-right">{{ $product->pivot->quantity-$product->pivot->return_quantity}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- .card-inner-group -->
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner card-inner-md">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">Detail harga</h6>
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
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner card-inner-md">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">Riwayat Pembayaran</h6>
                        </div>
                        <br>
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
                <br>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th class="text-right">Jumlah Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subTotal = 0; @endphp
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                            <td><a href="/purchase-transaction/show/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
                            <td class="text-right">{{ number_format($transaction->pivot->amount) }}</td>
                        </tr>
                        @php $subTotal += $transaction->pivot->amount; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Subtotal</td>
                            <td class="text-right">{{ number_format($subTotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-top: none;">Total Pembelian</td>
                            <td class="text-right" style="border-top: none;">{{ number_format($centralPurchase->netto) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td colspan="2">Sisa Hutang</td>
                            <td class="text-right">{{ number_format(abs($subTotal - $centralPurchase->netto)) }}</td>
                        </tr>
                    </tfoot>
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
            selectedProducts: JSON.parse('{!! $centralPurchase->products !!}'),
            loading: false,
        },
    })
</script>
@endsection