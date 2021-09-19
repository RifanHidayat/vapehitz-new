@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="{{url('/request-to-retail')}}"><em class="icon ni ni-arrow-left"></em>
                    <span>Permintaan Barang ke Gudang Retail</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="form-label" for="full-name-1">Nomor Request</label>
                    <div class="form-control-wrap">
                        {{$approve_central->code}}
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mt-3">
                <div class="form-group">
                    <label class="form-label" for="full-name-1">Tanggal Request</label>
                    <div class="form-control-wrap">
                        {{$approve_central->date}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="col-lg-12 mt-3">
                <div class="form-group">
                    <div class="card card-bordered">
                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok Pusat</th>
                                    <th>Stok Retail</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                    <td>@{{product.code}}</td>
                                    <td>@{{product.name}}</td>
                                    <td>@{{product.central_stock}}</td>
                                    <td>@{{product.retail_stock}}</td>
                                    <td>
                                        @{{product.quantity}}
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
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            selectedProducts: JSON.parse('{!! $approve_central->products !!}'),
            loading: false,
        },
    })
</script>
@endsection