@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .data-value {
        text-align: right;
    }
</style>
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Detail Produk {{$products->name}}</h3>
            <div class="nk-block-des text-soft">
                <ul class="list-inline">
                    <li>Kode Produk: <span class="text-base">{{$products->code}}</span></li>
                    <li>Created At: <span class="text-base">{{$products->created_at}}</span></li>
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/product" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>
</div>
<div class="nk-block">
    <div class="row gy-5">
        <div class="col-lg-5">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Stok Produk</h4>
                </div>
            </div><!-- .nk-block-head -->
            <div class="card card-bordered">
                <ul class="data-list is-compact">
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Stok Pusat</div>
                            <div class="data-value text-right">{{$products->central_stock}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Stok Retail</div>
                            <div class="data-value">{{$products->retail_stock}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Stok Studio</div>
                            <div class="data-value">{{$products->studio_stock}}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Bad Stok</div>
                            <div class="data-value">{{$products->bad_stock}}</div>
                        </div>
                    </li>
                </ul>
            </div><!-- .card -->
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Harga Produk</h4>
                </div>
            </div><!-- .nk-block-head -->
            <div class="card card-bordered">
                <ul class="data-list is-compact">
                    <li class="data-item">
                        <div class="data-col justify-content-between">
                            <div class="data-label">Harga Beli</div>
                            <div class="data-value">Rp. {{ number_format($products->purchase_price) }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col justify-content-between">
                            <div class="data-label">Harga Jual Agen</div>
                            <div class="data-value">Rp. {{ number_format($products->agent_price) }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col justify-content-between">
                            <div class="data-label">Harga Jual Retail</div>
                            <div class="data-value">
                                Rp. {{ number_format($products->retail_price) }}
                            </div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col justify-content-between">
                            <div class="data-label">Harga Jual WS</div>
                            <div class="data-value">Rp. {{ number_format($products->ws_price) }}</div>
                        </div>
                    </li>
                </ul>
            </div><!-- .card -->
        </div><!-- .col -->
        <div class="col-lg-7">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title title">Detail Produk</h4>
                </div>
            </div>
            <div class="card card-bordered">
                <ul class="data-list is-compact">
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Nama Produk</div>
                            <div class="data-value">{{ $products->name }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Kategori</div>
                            @if($products->productCategory !== null)
                            <div class="data-value">{{ $products->productCategory->name }}</div>
                            @else
                            <div class="data-value"></div>
                            @endif
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Subkategori</div>
                            @if($products->productSubcategory !== null)
                            <div class="data-value">{{ $products->productSubcategory->name }}</div>
                            @else
                            <div class="data-value"></div>
                            @endif
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Berat (gr)</div>
                            <div class="data-value">{{ $products->weight }}</div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Status</div>
                            <div class="data-value">
                                @if($products->status == 1)
                                <span class="badge badge-dim badge-sm badge-outline-success">
                                    Active
                                </span>
                                @else
                                <span class="badge badge-dim badge-sm badge-outline-success">
                                    Inactive
                                </span>
                                @endif
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .col -->
    </div><!-- .row -->
</div>
@endsection