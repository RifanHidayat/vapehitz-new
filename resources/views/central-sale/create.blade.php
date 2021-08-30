@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="/central-sale"><em class="icon ni ni-arrow-left"></em>
                    <span>Data Penjualan Barang</span>
                </a>
            </div>
        </div>
    </div>
</div>
<p></p>
<form @submit.prevent="submitForm">
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner card-inner-md">
                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">Tambah Data Penjualan Barang</h6>
                    </div>
                </div>
            </div>

            <div class="card-inner">
                <div class=" form-group col-md-6">
                    <label class="form-label" for="full-name-1">Nomor Invoice</label>
                    <div class="form-control-wrap">
                        <input type="text" v-model="code" class="form-control" readonly>
                    </div>
                </div>
                <div class=" form-group col-md-6">
                    <label class="form-label" for="full-name-1">Tanggal Invoice</label>
                    <div class="form-control-wrap">
                        <input type="date" v-model="date" class="form-control">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Shipment</label>
                    <div class="form-control-wrap">
                        <div class="input-group mb-3">
                            <select v-model="shipmentId" id="" class="form-control">
                                <option v-for="shipment in shipments" :value="shipment.id">@{{shipment.name}}</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#shipmentModal">
                                    <em class="fas fa-plus"></em>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Nama Pelanggan</label>
                    <div class="form-control-wrap">
                        <select v-model="customerId" class="form-control" id="customer">
                            <option v-for="customer in customers" :value="customer.id">@{{customer.name}}</option>
                        </select>
                    </div>
                </div>
                <!-- <div class="form-group col-md-6">
                    <label class="form-label" for="full-name-1">Alamat Pelanggan</label>
                    <div class="form-control-wrap">
                        <input class="form-control" type="text" readonly>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Nomor Telepon</label>
                    <div class="form-control-wrap">
                        <input class="form-control" type="number" readonly>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Nomor Handphone/Wa</label>
                    <div class="form-control-wrap">
                        <input class="form-control" type="number" readonly>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label" for="full-name-1">Email</label>
                    <div class="form-control-wrap">
                        <input class="form-control" type="email" readonly>
                    </div>
                </div> -->
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Termin Hutang</label>
                    <div class="form-control-wrap">
                        <select v-model="debt" id="" class="form-control">
                            <option value="0">0 Hari</option>
                            <option value="7">7 Hari</option>
                            <option value="15">15 Hari</option>
                            <option value="30">30 Hari</option>
                        </select>
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
                                    <tr class="text-center">
                                        <th>Jenis Barang</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Berat (gr)</th>
                                        <th>Booking</th>
                                        <th>Stok</th>
                                        <th>Harga Jual</th>
                                        <th>Qty</th>
                                        <th>Free</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                        <td>@{{product.product_category.name}}</td>
                                        <td>@{{product.code}}</td>
                                        <td>@{{product.name}}</td>
                                        <td>@{{product.weight}}</td>
                                        <td></td>
                                        <td>@{{product.central_stock}}</td>
                                        <td>
                                            <input type="text" v-model="product.agent_price" :value="currencyFormat(product.agent_price)" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" v-model="product.quantity" value="1" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" v-model="product.free" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" :value="currencyFormat(subTotalProduct(product))" class="form-control" readonly>
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
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Total Berat</span></td>
                                <td width="20%"><input type="text" v-model="totalWeight" :value="totalWeight" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Total Biaya</span></td>
                                <td width="20%"><input type="text" v-model="subTotal" :value="currencyFormat(subTotal)" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><span style="font-size: 12px; font-weight: bold;">Diskon</span></td>
                                <td><input type="text" v-model="discount" class="form-control input-sm" style="width: 45%; display: inline;">
                                    <select v-model="discount_type" class="form-control input-sm" style="width: 50%; display: inline;">
                                        <option value="nominal">Nominal</option>
                                        <option value="percentage">Persen (%)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sub Total</span></td>
                                <td width="20%">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp.</div>
                                        </div>
                                        <input type="text" v-model="calculateDiscount" :value="currencyFormat(calculateDiscount)" class="form-control text-right" readonly>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Biaya Kirim</span></td>
                                <td width="20%"><input type="number" v-model="shipping_cost" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Biaya Lainnya</span></td>
                                <td width="20%"><input type="number" v-model="other_cost" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;"></span></td>
                                <td width="20%"><input type="text" v-model="detail_other_cost" class="form-control" placeholder="Ket. Biaya Lainnya"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Deposit Customer</span></td>
                                <td width="20%"><input type="number" v-model="deposit_customer" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Net Total</span></td>
                                <td width="20%"><input type="text" v-model="netTotal" :value="currencyFormat(netTotal)" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sumber Penerimaan 1</span></td>
                                <td width="20%">
                                    <select v-model="receipt_1" class="form-control">
                                        <option v-for="(account, index) in accounts" :value="account.id">@{{ account.name }}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Rp.</span></td>
                                <td width="20%"><input type="number" v-model="receive_1" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sumber Penerimaan 2</span></td>
                                <td width="20%">
                                    <select v-model="receipt_2" class="form-control">
                                        <option v-for="(account, index) in accounts" :value="account.id">@{{ account.name }}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Rp.</span></td>
                                <td width="20%"><input type="number" v-model="receive_2" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Jumlah Pembayaran</span></td>
                                <td width="20%"><input type="text" v-model="totalPayment" :value="currencyFormat(totalPayment)" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sisa Pembayaran</span></td>
                                <td width="20%"><input type="text" v-model="remainingPayment" :value="currencyFormat(remainingPayment)" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Nama Penerima</span></td>
                                <td width="20%"><input type="text" v-model="recipient" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Alamat Penerima</span></td>
                                <td width="20%"><textarea v-model="address_recipient" cols="30" rows="1"></textarea></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" width="60%">&nbsp;</td>
                                <td width="20%"><span style="font-size: 12px; font-weight: bold;">Keterangan</span></td>
                                <td width="20%"><input type="text" v-model="detail" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <p></p>
    <div class="form-group text-right">
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
</form>
<!-- Shipment Modal -->
<div class="modal fade" id="shipmentModal" tabindex="-1" aria-labelledby="shipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shipmentModalLabel">Tambah Shipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="is_edit_shipment ? editShipment(shipment_edit_id,shipment_edit_index):addShipment()">
                <div class="modal-body">
                    <div class="form-group col-md-6">
                        <label class="form-label" for="">Nama Shipment</label>
                        <input type="text" class="form-control" v-model="shipment.name">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group text-right">
                        <button v-if="is_edit_shipment == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                            &times
                        </button>
                        <button class="btn btn-primary" type="submit" :disabled="shipment.loading">
                            <span v-if="shipment.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>@{{is_edit_shipment ? "Edit" : "Simpan" }}</span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="modal-body">
                <h5>Data Shipment</h5>
                <table class="datatable-init table table-stripped">
                    <thead>
                        <tr class="text-center">
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(shipment, index) in shipments" :value="shipment.id" class="text-center">
                            <td>@{{shipment.name}}</td>
                            <td>
                                <div class="btn-group" aria-label="Basic example">
                                    <a href="#" @click.prevent="onEditShipment(index)" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                    <a href="#" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Shipment Modal -->
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
            shipmentId: '',
            customerId: '',
            code: '{{ $code }}',
            date: '',
            debt: '',
            discount: '',
            shipping_cost: '',
            other_cost: '',
            detail_other_cost: '',
            deposit_customer: '',
            receipt_1: '',
            receive_1: '',
            receipt_2: '',
            receive_2: '',
            recipient: '',
            address_recipient: '',
            detail: '',
            free: '0',
            discount_type: 'nominal',
            customers: JSON.parse('{!! $customer !!}'),
            shipments: JSON.parse('{!! $shipment !!}'),
            accounts: JSON.parse('{!! $accounts !!}'),
            shipment: {
                name: '',
                loading: false,
            },
            shipment_edit_id: null,
            shipment_edit_index: null,
            is_edit_shipment: false,
            prefix: '',
            selectedProducts: [],
            check: [],
            loading: false,
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/central-sale', {
                        shipment_id: vm.shipmentId,
                        customer_id: vm.customerId,
                        code: vm.code,
                        date: vm.date,
                        debt: vm.debt,
                        total_weight: vm.totalWeight,
                        total_cost: vm.subTotal,
                        discount: vm.discount,
                        subtotal: vm.calculateDiscount,
                        shipping_cost: vm.shipping_cost,
                        other_cost: vm.other_cost,
                        detail_other_cost: vm.detail_other_cost,
                        deposit_customer: vm.deposit_customer,
                        net_total: vm.netTotal,
                        receipt_1: vm.receipt_1,
                        receive_1: vm.receive_1,
                        receipt_2: vm.receipt_2,
                        receive_2: vm.receive_2,
                        payment_amount: vm.totalPayment,
                        remaining_payment: vm.remainingPayment,
                        recipient: vm.recipient,
                        address_recipient: vm.address_recipient,
                        detail: vm.detail,
                        quantity: vm.quantity,
                        price: vm.agent_price,
                        free: vm.free,
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
                                window.location.href = '/central-sale';
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
            addShipment: function() {
                // console.log('submitted');
                let vm = this;
                vm.shipment.loading = true;
                axios.post('/shipment', {
                        name: this.shipment.name,
                    })
                    .then(function(response) {
                        vm.shipment.loading = false;
                        console.log(response);
                        vm.shipments.push(response.data.data);
                        vm.shipmentId = response.data.data.id
                        vm.onChangeShipment();
                        $('#shipmentModal').modal('hide')
                    })
                    .catch(function(error) {
                        vm.shipment.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            },
            onChangeShipment: function() {
                const shipment = this.shipments.filter(cat => cat.id == this.shipment_id)[0];
                if (shipment == null || typeof shipment == "undefined") {
                    this.prefix = '';
                } else {
                    this.prefix = shipment.name;
                }
            },
            editShipment: function(id, index) {
                // console.log('submitted');
                let vm = this;
                vm.shipment.loading = true;
                axios.patch('/shipment/' + id, {
                        name: this.shipment.name,
                    })
                    .then(function(response) {
                        vm.shipment.loading = false;
                        console.log(response);
                        const {
                            data
                        } = response.data
                        vm.shipments[index].name = data.name;
                        // vm.product_categories.push(response.data.data);
                        // vm.product_category_id = response.data.data.id
                        // vm.onChangeCategory();
                        // $('#categoryModal').modal('hide')
                    })
                    .catch(function(error) {
                        vm.shipment.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            },
            onEditShipment: function(index) {
                const shipment = this.shipments[index];
                this.shipment.name = shipment.name;
                this.shipment_edit_id = shipment.id;
                this.shipment_edit_index = index;
                this.is_edit_shipment = true;
            },
            onCloseEdit: function() {
                this.is_edit_shipment = false;
                this.shipment.name = "";
            },
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
            currencyFormat: function(number) {
                return Intl.NumberFormat('de-DE').format(number);
            },
            clearCurrencyFormat: function(number) {
                if (!number) {
                    return 0;
                }
                return number.replaceAll(".", "");
            },
            subTotalProduct: function(product) {
                return Number(product.quantity) * Number(product.agent_price);
            }
        },
        computed: {
            subTotal: function() {
                // return this.selectedProducts.map(product => {
                //     Number(product.purchase_price) * this.qty;
                // });
                const subTotal = this.selectedProducts.map(product => {
                        const amount = Number(product.agent_price) * Number(product.quantity);
                        return amount;
                    })
                    .reduce((acc, cur) => {
                        return acc + cur;
                    }, 0);
                return subTotal;
            },
            totalWeight: function() {
                const totalWeight = this.selectedProducts.map(product => {
                        const total = Number(product.weight) * Number(product.quantity);
                        return total;
                    })
                    .reduce((acc, cur) => {
                        return acc + cur;
                    }, 0);
                return totalWeight;
            },
            calculateDiscount: function() {
                let calculateDiscount = 0;
                if (this.discount_type === "nominal") {
                    calculateDiscount = Number(this.subTotal) - Number(this.discount);

                } else {
                    let discount = Number(this.subTotal) * (Number(this.discount) / 100);
                    calculateDiscount = Number(this.subTotal) - discount;
                }
                return calculateDiscount;
            },
            netTotal: function() {
                const netTotal = Number(this.calculateDiscount) + Number(this.shipping_cost) + Number(this.other_cost) - Number(this.deposit_customer);
                return netTotal;
            },
            totalPayment: function() {
                const totalPayment = Number(this.receive_1) + Number(this.receive_2);
                return totalPayment;
            },
            remainingPayment: function() {
                const remainingPayment = Number(this.netTotal) - Number(this.receive_1) - Number(this.receive_2);
                return remainingPayment;
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
                data['quantity'] = 1;
                check.push(data);
            }
        });
    });
</script>
@endsection