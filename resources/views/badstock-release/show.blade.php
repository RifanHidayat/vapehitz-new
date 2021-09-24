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
                            <h3 class="nk-block-title page-title">Detail Data Pengeluaran Badstock</h3>
                            <div class="nk-block-des text-soft">
                                <ul class="list-inline">
                                    <li>Kode Order: <span class="text-base">{{$badstock->code}}</span></li>
                                    <li>Submited At: <span class="text-base">{{$badstock->date}}</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{url('/badstock-release')}}" class="btn btn-outline-warning">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </div>
                </div><!-- .nk-block-head -->
                <div class="nk-block">
                    <div class="row gy-5">
                        <div class="col-lg-8">
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                    <h5 class="nk-block-title title">Order Info</h5>
                                </div>
                            </div><!-- .nk-block-head -->
                            <div class="card card-bordered">
                                <ul class="data-list is-compact">
                                    <li class="data-item">
                                        <div class="data-col">
                                            <div class="data-label">Kode Order</div>
                                            <div class="data-value">{{$badstock->code}}</div>
                                        </div>
                                    </li>
                                    <li class="data-item">
                                        <div class="data-col">
                                            <div class="data-label">Ditambahkan Pada</div>
                                            <div class="data-value">{{$badstock->date}}</div>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- .card -->
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                </div>
                            </div><!-- .nk-block-head -->
                            <div class="card">
                                <!-- <div id="preview">
                                    <a href=""><img v-if="url" :src="url" /></a>
                                </div> -->
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-lg-4">
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                    <!-- <h5 class="nk-block-title title">Gambar Yang di Upload</h5> -->
                                    &nbsp;
                                </div>
                            </div>
                            <div class="card card-bordered">
                                <div id="preview">
                                    <a href=""><img v-if="url" :src="url" /></a>
                                </div>
                            </div>
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .nk-block -->
                <div class="card card-bordered mt-3">
                    <div class="card-inner">
                        <table class="datatable-init table table-stripped">
                            <thead>
                                <tr class="text-center">
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok Pusat</th>
                                    <th>Bad Stock</th>
                                    <th>Qty</th>
                                    <th>Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                    <td>@{{product.code}}</td>
                                    <td>@{{product.name}}</td>
                                    <td>@{{product.central_stock}}</td>
                                    <td>@{{product.bad_stock}}</td>
                                    <td>
                                        @{{product.quantity}}
                                        <!-- <input type="number" v-model="product.quantity" value="1" class="form-control"> -->
                                    </td>
                                    <td>
                                        @{{subTotalProduct(product)}}
                                        <!-- <input type="number" :value="subTotalProduct(product)" class="form-control" readonly> -->
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
            code: '{{$badstock->code}}',
            date: '{{$badstock->date}}',
            productId: '',
            selectedProducts: JSON.parse('{!! $badstock->products !!}'),
            check: [],
            url: '{{asset($badstock->image)}}',
            quantity: '0',
            // bad_stock: '',
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
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/badstock-release', {
                        productId: vm.productId,
                        code: vm.code,
                        date: vm.date,
                        image: vm.image,
                        selected_products: vm.selectedProducts,
                    })
                    .then(function(response) {
                        vm.loading = false;
                        Swal.fire({
                            title: 'Success',
                            text: 'Data has been saved',
                            icon: 'success',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/badstock-release';
                            }
                        })
                        // console.log(response);
                    })
                    .catch(function(error) {
                        vm.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            },
            removeSelectedProduct: function(index) {
                this.selectedProducts.splice(index, 1);
            },
            removeAllSelectedProducts: function() {
                this.selectedProducts = [];
            },
            removeFromCheck: function(index) {
                this.check.splice(index, 1);
            },
            subTotalProduct: function(product) {
                return Number(product.bad_stock) - Number(product.quantity);
            }
        },
    })
</script>
@endsection