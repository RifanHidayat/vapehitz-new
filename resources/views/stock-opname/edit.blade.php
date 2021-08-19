@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="/stock-opname"><em class="icon ni ni-arrow-left"></em>
                    <span>Data Stock Opname</span>
                </a>
            </div>
            <h2 class="nk-block-title fw-normal">Tambah Data Stock Opname</h2>
        </div>
    </div>
</div>
<p></p>
<div class="nk-block nk-block-lg">
    <div class="row g-gs align-items-start">
        <div class="col-lg-5 col-md-12">
            <div class="card card-bordered">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Informasi Pembelian</h6>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="card-inner">
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Nomor Stok Opname</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="code" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Tanggal Stok Opname</label>
                                <div class="form-control-wrap">
                                    <input type="date" v-model="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Keterangan</label>
                                <div class="form-control-wrap">
                                    <textarea v-model="note" class="form-control" id="note" cols="30" rows="5" placeholder="Keterangan"></textarea>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
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
                <div class="card-tools mr-n1">
                    <ul class="btn-toolbar gx-1">
                        <li>
                            <a class="btn btn-icon btn-trigger" data-toggle="modal" href="#addProduct" data-backdrop="static" data-keyboard="false"><em class="icon ni ni-plus"></em></a>
                        </li>
                        <li>
                            <div class="drodown">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="link-list-opt no-bdr">
                                        <!-- <li><a href="#"><em class="icon ni ni-plus"></em><span>Tambah</span></a></li> -->
                                        <li><a href="#" @click.prevent="removeAllSelectedProducts"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-inner">
            <div v-if="selectedProducts.length === 0" class="text-center text-soft">
                <em class="fas fa-dolly fa-4x"></em>
                <p class="mt-3">Belum ada barang yang dipilih</p>
            </div>
            <div v-for="(product, index) in selectedProducts" :key="index" class="card card-bordered">
                <div class="card-inner">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-10">
                            <h5 class="card-title">Data Produk</h5>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Kode</p>
                                <p class="col-md-6 text-right mb-0"><strong>@{{product.code}}</strong></p>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Nama</p>
                                <p class="col-md-6 text-right mb-0"><strong>@{{product.name}}</strong></p>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Stok Gudang</p>
                                <p class="col-md-6 text-right mb-0"><strong>@{{product.central_stock}}</strong></p>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Good Stock</p>
                                <div class="col-md-6">
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="product.good_stock" class="form-control text-right" placeholder="Good Stock">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Bad Stock</p>
                                <div class="col-md-6">
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="product.bad_stock" class="form-control text-right" placeholder="Bad Stock">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Selisih</p>
                                <p class="col-md-6 text-right mb-0"><strong>@{{total}}</strong></p>
                            </div>
                            <div class="row justify-content-between align-items-center mt-3">
                                <p class="col-md-6 mb-0">Keterangan</p>
                                <div class="col-md-6">
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="product.description" class="form-control text-right" placeholder="Keterangan">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <a href="#" @click.prevent="removeSelectedProduct(index)" class="btn btn-icon btn-trigger text-danger"><em class="icon ni ni-trash"></em></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addProduct" tabindex="-1" role="dialog" aria-labelledby="addProductLabel" aria-hidden="true">
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
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/stock-opname', {
                        code: vm.code,
                        date: vm.date,
                        note: vm.note,
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
                                window.location.href = '/stock-opname';
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
            ]
        });

        $('#products-table tbody').on('click', '.btn-choose', function() {
            const rowData = productTable.row($(this).parents('tr')).data();
            const data = {
                ...rowData
            };
            const check = app.$data.check;
            const productIds = check.map(product => product.id);

            if (productIds.indexOf(data.id) < 0) {
                data['good_stock'] = 0;
                data['bad_stock'] = 0;
                data['description'] = "";
                check.push(data);
            }
        });
    });
</script>
@endsection