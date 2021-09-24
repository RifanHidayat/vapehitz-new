@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="card card-bordered">
    <div class="card-inner-group">
        <div class="card-inner card-inner-md">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Transaksi Penjualan Barang</h6>
                </div>
            </div>
        </div>
        <div class="card-inner">
            <div class=" form-group col-md-6">
                <label class="form-label" for="full-name-1">Nomor Order</label>
                <div class="form-control-wrap">
                    <input type="text" v-model="code" class="form-control" readonly>
                </div>
            </div>
            <div class=" form-group col-md-6">
                <label class="form-label" for="full-name-1">Tanggal Order</label>
                <div class="form-control-wrap">
                    <input type="date" v-model="code" class="form-control">
                </div>
            </div>
        </div>
    </div>
</div>
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
                            <a class="btn btn-icon btn-trigger" data-toggle="modal" href="#productModal" data-backdrop="static" data-keyboard="false"><em class="icon ni ni-plus"></em></a>
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
        <p></p>
        <div v-if="selectedProducts.length === 0" class="text-center text-soft">
            <em class="fas fa-dolly fa-4x"></em>
            <p class="mt-3">Belum ada barang yang dipilih</p>
        </div>
        <div v-else class="card">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped" id="centralPurchase">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                    <th>Harga Jual</th>
                                    <th>Discount</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                    <td>@{{product.code}}</td>
                                    <td>@{{product.name}}</td>
                                    <td>@{{product.retail_stock}}</td>
                                    <td><input type="text" v-model="product.retail_price" :value="currencyFormat(product.retail_price)" class="form-control"></td>
                                    <td>
                                        <input type="number" v-model="product.discount" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" v-model="product.quantity" class="form-control">
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
            <div class="table-responsive">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td colspan="5" width="60%">&nbsp;</td>
                            <td width="20%"><span style="font-size: 12px; font-weight: bold;">Total Biaya</span></td>
                            <td width="20%"><input type="text" v-model="totalCost" :value="totalCost" class="form-control" readonly></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5" width="60%">&nbsp;</td>
                            <td width="20%"><span style="font-size: 12px; font-weight: bold;">Cara Pembayaran</span></td>
                            <td width="20%">
                                <select v-model="paymentMethod" class="form-control">
                                    <option value="transfer">Transfer</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Pilih Produk</h5>
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
                    <em class="fas fa-check"></em>&nbsp;<span class="badge badge-pill badge-light"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- end Product Modal -->
<!-- checkModal -->
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
<!-- endcheckModal -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{$code}}',
            selectedProducts: [],
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
                return (Number(product.retail_price) * Number(product.quantity)) - Number(product.discount);
            },
            currencyFormat: function(number) {
                return Intl.NumberFormat('de-DE').format(number);
            },
        },
        computed: {
            totalCost: function() {
                const totalCost = this.selectedProducts.map(product => {
                    const amount = (Number(product.retail_price) * Number(product.quantity)) - Number(product.discount);
                    return amount;
                }).reduce((acc, cur) => {
                    return acc + cur;
                }, 0);
                return totalCost;
            },
        }
    })
</script>
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#products-table', {
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
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

        $('#products-table tbody').on('click', '.btn-choose', function() {
            const rowData = $('#products-table').DataTable().row($(this).parents('tr')).data();
            const data = {
                ...rowData
            };
            const check = app.$data.check;
            const productIds = check.map(product => product.id);

            if (productIds.indexOf(data.id) < 0) {
                data['quantity'] = 1;
                data['discount'] = 0;
                check.push(data);
            }
        });
    })
</script>
@endsection