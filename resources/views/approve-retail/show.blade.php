@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between g-3">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Detail Data Permintaan Barang Dari Pusat</h3>
                            <div class="nk-block-des text-soft">
                                <ul class="list-inline">
                                    <li>Kode Order: <span class="text-base">{{$approve_retail->code}}</span></li>
                                    <li>Submited At: <span class="text-base">{{$approve_retail->date}}</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{url('/approve-retail')}}" class="btn btn-outline-warning">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card card-bordered mt-3">
                    <div class="card-inner">
                        <table class="datatable-init table table-stripped">
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
            selectedProducts: JSON.parse('{!! $approve_retail->products !!}'),
            loading: false,
        },
    })
</script>
@endsection