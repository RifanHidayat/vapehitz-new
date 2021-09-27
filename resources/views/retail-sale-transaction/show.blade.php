@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/retail-sale-transaction"><em class="icon ni ni-arrow-left"></em><span>Pembayaran Retail</span></a></div>
        <h2 class="nk-block-title fw-normal">Detail Transaksi</h2>
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
    <div class="row g-gs align-items-start">
        <div class="col-lg-7 col-md-12">

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Informasi Penjualan</h6>
                            </div>
                            <div class="card-tools mr-n1">
                                <!-- <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="collapse" href="#collapseSaleInfo" role="button" aria-expanded="false" aria-controls="collapseExample"><em class="icon ni ni-downward-ios"></em></a>
                                    </li>
                                </ul> -->
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                    <div id="collapseSaleInfo">
                        <div class="card-inner">
                            <!-- <div class="row"> -->
                            <!-- <div class="col-lg-6 col-md-12"> -->
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Nomor Transaksi</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $transaction->code }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Tanggal Transaksi</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $transaction->date }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Cara Pembayaran</p>
                                @if($transaction->payment_method == 'hutang')

                                <p class="col-md-6 text-right mb-0 text-capitalize">
                                    <strong>Retur</strong>
                                    @if($transaction->centralSaleReturn !== null)
                                    <strong><a href="/central-sale-return/show/{{$transaction->centralSaleReturn->id}}" target="_blank">{{$transaction->centralSaleReturn->code}}</a></strong>
                                    @endif
                                </p>
                                @else
                                <p class="col-md-6 text-right mb-0 text-capitalize"><strong>{{ $transaction->payment_method }}</strong></p>

                                @endif
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Akun</p>
                                @if($transaction->account !== null)
                                <p class="col-md-6 text-right mb-0"><strong>{{ $transaction->account->name }}</strong></p>
                                @endif
                            </div>
                            <div class="row justify-content-between">
                                <p class="col-md-6 mb-0 text-soft">Note</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $transaction->note }}</strong></p>
                            </div>
                        </div><!-- .card-inner -->
                    </div>
                </div><!-- .card-inner-group -->
            </div>

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Detail Pembayaran</h6>
                            </div>
                            <div class="card-tools mr-n1">
                                <!-- <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="collapse" href="#collapsePaymentHistory" role="button" aria-expanded="false" aria-controls="collapsePaymentHistory"><em class="icon ni ni-downward-ios"></em></a>
                                    </li>
                                </ul> -->
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                    <div id="collapsePaymentHistory">
                        <div class="card-inner">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <!-- <th>Tanggal</th> -->
                                            <th>Nomor Penjualan</th>
                                            <th class="text-right">Total Penjualan</th>
                                            <th class="text-right">Jumlah Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subTotal = 0; @endphp
                                        @foreach($transaction->retailSales as $sale)
                                        <tr>
                                            <td><a href="/central-sale/show/{{ $sale->id }}">{{ $sale->code }}</a></td>
                                            <td class="text-right">{{ number_format($sale->net_total, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($sale->pivot->amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @php $subTotal += $sale->pivot->amount; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">Total</td>
                                            <td class="text-right">{{ number_format($subTotal) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><!-- .card-inner -->
                    </div>
                </div><!-- .card-inner-group -->
            </div>
        </div>
    </div><!-- .nk-block -->

    @endsection
    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    @endsection
    @section('pagescript')
    @endsection