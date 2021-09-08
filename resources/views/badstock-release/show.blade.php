@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="/badstock-release"><em class="icon ni ni-arrow-left"></em><span>Pengeluaran Badstock</span></a></div>
            <h4 class="nk-block-title fw-normal">Detail Data Pengeluaran Badstock</h4>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="form-label" for="full-name-1">Kode</label>
                    <div class="form-control-wrap">
                        <input type="text" v-model="code" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <p></p>
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="form-label" for="full-name-1">Tanggal Proses</label>
                    <div class="form-control-wrap">
                        <input type="date" v-model="date" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <p></p>
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="form-label" for="full-name-1">Gambar</label>
                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <img src="{{asset($badstock->image)}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <!-- <div class="form-group">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">
                            Tambah Barang
                        </button>
                    </div> -->
                <div class="form-group">
                    <div class="card card-bordered">
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
            <p></p>
            <div class="col-md-12 text-right">
                <a href="/badstock-release" class="btn btn-outline-warning">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Kembali</span>
                </a>
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