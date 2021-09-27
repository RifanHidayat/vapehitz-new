@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/studio-sale-return"><em class="icon ni ni-arrow-left"></em><span>Retur Barang Penjualan</span></a></div>
        <h2 class="nk-block-title fw-normal">Detail Retur Penjualan</h2>
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
                                <h6 class="title">Informasi Retur</h6>
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
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Nomor Retur</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $return->code }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Tanggal Transaksi</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $return->date }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Cara Pembayaran</p>
                                <p class="col-md-6 text-right mb-0 text-capitalize"><strong>{{ $return->payment_method }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Akun</p>
                                @if($return->account !== null)
                                <p class="col-md-6 text-right mb-0"><strong>{{ $return->account->name }}</strong></p>
                                @endif
                            </div>
                            <div class="row justify-content-between">
                                <p class="col-md-6 mb-0 text-soft">Note</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $return->note }}</strong></p>
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
                                <h6 class="title">Produk Retur</h6>
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                    <div class="card-inner">
                        <!-- <div class="nk-wg-action">
                                <div class="nk-wg-action-content">
                                    <em class="icon ni ni-cc-alt-fill"></em>
                                    <div class="title">Alacarte Black Strawberry</div>
                                    <p>We have still <strong>40 buy orders</strong> and <strong>12 sell orders</strong>, thats need to review &amp; take necessary action.</p>
                                </div>
                                <a href="#" class="btn btn-icon btn-trigger mr-n2"><em class="icon ni ni-trash"></em></a>
                            </div> -->
                        @if(count($return->products) < 1) <div class="text-center text-soft">
                            <em class="fas fa-dolly fa-4x"></em>
                            <p class="mt-3">Tidak ada barang</p>
                    </div>
                    @else
                    @foreach($return->products as $product)
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-lg-12">
                                    <h5 class="card-title">{{ $product->code . ' - ' }}{{ $product->name }}</h5>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12">
                                            <div class="row justify-content-between">
                                                <p class="col-md-6 mb-0">Qty Retur</p>
                                                <p class="col-md-6 text-right mb-0"><strong>{{ $product->pivot->quantity }}</strong></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="row justify-content-between">
                                                <p class="col-md-6 mb-0">Alasan</p>
                                                <p class="col-md-6 text-right mb-0">
                                                    @if($product->pivot->cause == 'defective')
                                                    <strong>Cacat/Rusak</strong>
                                                    @elseif($product->pivot->cause == 'wrong')
                                                    <strong>Tidak Sesuai</strong>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                        </div>
                    </div>
                    @endforeach
                    @endif
                    <!-- End:Summary -->
                </div><!-- .card-inner -->
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