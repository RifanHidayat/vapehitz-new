@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="/badstock-release"><em class="icon ni ni-arrow-left"></em><span>Pengeluaran Badstock</span></a></div>
            <h4 class="nk-block-title fw-normal">Edit Data Pengeluaran Badstock</h4>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <form @submit.prevent="submitForm">
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
                            <input type="date" v-model="date" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Upload Gambar</label>
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" v-model="image" class="custom-file-input" id="inputGroupFile02">
                                <label class="custom-file-label" for="inputGroupFile02">Pilih Gambar</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">
                            Tambah Barang
                        </button>
                    </div>
                    <div class="form-group">
                        <div class="card card-bordered">
                            <table class="table table-stripped table-bordered">
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
                                            <input type="number" v-model="product.quantity" value="1" class="form-control">
                                        </td>
                                        <td>
                                            <input type="number" :value="subTotalProduct(product)" class="form-control" readonly>
                                        </td>
                                        <td>
                                            <a href="#" @click.prevent="removeSelectedProduct(index)" class="btn btn-icon btn-trigger text-danger"><em class="icon ni ni-trash"></em></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductLabel">List Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#checkModal">
                        <div class="d-flex align-items-center">
                            <strong>Data Dipilih</strong>&nbsp;<span v-if="check.length > 0" class="badge badge-pill badge-light">@{{ check.length }}</span>
                        </div>
                    </button>
                </div>
                <p></p>
                <div>
                    <table class="table table-stripped" id="products-table">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="onSelectedProduct" type="button" class="btn btn-primary" data-dismiss="modal">
                    <div data-dismiss="modal">
                        <em class="fas fa-check"></em>&nbsp;<span class="badge badge-pill badge-light"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkModalLabel">Keranjang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p v-if="check.length === 0" class="text-soft text-center">Belum ada barang dipilih.</p>
                <ul class="list-group">
                    <li v-for="(product, index) in check" class="list-group-item">
                        <div class="row">
                            <div class="col-sm-10">@{{ product.name }}</div>
                            <div class="col-sm-2 text-right">
                                <a href="#" @click.prevent="removeFromCheck(index)" class="text-danger"><em class="fas fa-times"></em></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Close Modal -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{$badstock->code}}',
            image: '',
            date: '{{$badstock->date}}',
            productId: '',
            selectedProducts: [],
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
<script>
    $(function() {
        const productTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            "autoWidth": false,
            ajax: {
                url: '/datatables/stock-opname/products',
                type: 'GET',
            },
            columns: [{
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
        });
        $('#products-table tbody').on('click', '.btn-choose', function() {
            const rowData = productTable.row($(this).parents('tr')).data();
            const data = {
                ...rowData
            };
            const check = app.$data.check;
            const productIds = check.map(product => product.id);

            if (productIds.indexOf(data.id) < 0) {
                data['description'] = "";
                check.push(data);
            }
        });
    })
</script>
@endsection