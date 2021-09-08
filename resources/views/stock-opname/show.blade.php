@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="/stock-opname"><em class="icon ni ni-arrow-left"></em>
                    <span>Data Stok Opname</span>
                </a>
            </div>
            <h3 class="nk-block-title fw-normal">Detail Stok Opname</h3>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner card-inner-md">
                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">Informasi Stok Opname</h6>
                    </div>
                </div>
            </div>
            <div class="card-inner">
                <div class="row">
                    <div class="col-sm">
                        <div class=" form-group col-md-12">
                            <label class="form-label" for="full-name-1">Nomor Stok Opname</label>
                            <div class="form-control-wrap">
                                {{$stockOpname->code}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class=" form-group col-md-12">
                            <label class="form-label" for="full-name-1">Tanggal Stok Opname</label>
                            <div class="form-control-wrap">
                                {{$stockOpname->date}}
                            </div>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="form-group col-md-6">
                    <label class="form-label" for="full-name-1">Keterangan</label>
                    <div class="form-control-wrap">
                        {{$stockOpname->note}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<p></p>
<div class="card card-bordered h-100">
    <div class="card-inner-group">
        <div class="card-inner card-inner-md">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Daftar Produk</h6>
                </div>
            </div>
        </div>
        <div class="card-inner">
            <div v-if="selectedProducts.length === 0" class="text-center text-soft">
                <p class="mt-3">Belum ada barang yang dipilih</p>
            </div>
            <div v-else class="card">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="datatable-init table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Stok Retail</th>
                                        <th>Real Stock</th>
                                        <th>Selisih</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                        <td>@{{product.code}}</td>
                                        <td>@{{product.name}}</td>
                                        <td>@{{product.central_stock}}</td>
                                        <td>
                                            @{{product.good_stock}}
                                        </td>
                                        <td>
                                            @{{totalDifference(product)}}
                                        </td>
                                        <td>
                                            @{{product.description}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{$stockOpname->code}}',
            date: '{{$stockOpname->date}}',
            note: '{{$stockOpname->note}}',
            goodStock: '{{$stockOpname->goodStock}}',
            badStock: '{{$stockOpname->badStock}}',
            selectedProducts: JSON.parse('{!! $stockOpname->products !!}'),
            check: [],
            loading: false,
        },
        methods: {
            onSelectedProduct: function() {
                const selectedProductIds = this.selectedProducts.map(product => product.id);
                const productsInCheck = this.check.filter(product => selectedProductIds.indexOf(product.id) < 0);
                this.check.filter(product => selectedProductIds.indexOf(product.id) > -1)
                    .map(product => product.id)
                    .forEach(productId => {
                        const index = selectedProductIds.findIndex((id) => id == productId);
                        this.selectedProducts[index].quantity += 1;
                    })
                // console.log(arr);

                this.selectedProducts = this.selectedProducts.concat(productsInCheck);
                this.check = [];
            },
            totalDifference: function(product) {
                return Number(product.central_stock) - Number(product.good_stock);
            },
        },
        computed: {
            badGoodStock: function() {
                const data = Number(this.badStock) + Number(this.goodStock);
                return data;
            },
            centralStock: function() {
                const centralStock = this.selectedProducts.map(product => {
                    const amount = Number(product.central_stock);
                    return amount;
                }).reduce((acc, cur) => {
                    return acc + cur;
                }, 0);

                return centralStock;
            },
            total: function() {
                const total = Number(this.centralStock) - Number(this.badGoodStock);
                return total;
            },
        },
    })
</script>
@endsection