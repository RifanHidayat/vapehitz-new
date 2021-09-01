@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Konfirmasi Barang</h3>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="/central-sale" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submitForm">
                <div class="card card-bordered">
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
                                <input type="datetime" v-model="date" class="form-control">
                            </div>
                        </div>
                        <div class=" form-group col-md-6">
                            <label class="form-label" for="full-name-1">Shipment</label>
                            <div class="form-control-wrap">
                                <div class="input-group mb-3">
                                    <select v-model="shipmentId" id="" class="form-control">
                                        <option v-for="shipment in shipments" :value="shipment.id">@{{shipment.name}}</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#shipmentModal">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=" form-group col-md-6">
                            <label class="form-label" for="full-name-1">Nama Pelanggan</label>
                            <div class="form-control-wrap">
                                <select v-model="customerId" class="form-control" id="customer">
                                    <option v-for="customer in customers" :value="customer.id">@{{customer.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class=" form-group col-md-6">
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
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="table-responsive">
                            <table class="table table stripped">
                                <thead>
                                    <tr class="text-center">
                                        <!-- <th>Jenis Barang</th> -->
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
                                        <!-- <td>@{{product.product_category.name}}</td> -->
                                        <td>@{{product.code}}</td>
                                        <td>@{{product.name}}</td>
                                        <td>@{{product.weight}}</td>
                                        <td><input type="text" :value="calculateBooked(product)" class="form-control" readonly></td>
                                        <td>@{{product.central_stock}}</td>
                                        <td>@{{product.agent_price}}</td>
                                        <td>
                                            <input type="number" v-model="product.quantity" class="form-control">
                                        </td>
                                        <td><input type="number" v-model="product.free" class="form-control"></td>
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
                    <div class="card-inner">
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
                                        <td>
                                            <input type="text" v-model="discount" class="form-control input-sm" style="width: 45%; display: inline;">
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
                            <p></p>
                            <div class="text-right">
                                <button class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
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
                                    <a href="#" @click.prevent="onEditShipment(index)" class="btn btn-outline-light">Edit</a>
                                    <a href="#" class="btn btn-outline-light">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- EndShipment -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{$centralSales->code}}',
            date: '{{$centralSales->date}}',
            shipmentId: '{{$centralSales->shipment_id}}',
            customerId: '{{$centralSales->customer_id}}',
            debt: '{{$centralSales->debt}}',
            discount: '{{$centralSales->discount}}',
            shipping_cost: '{{$centralSales->shipping_cost}}',
            other_cost: '{{$centralSales->other_cost}}',
            detail_other_cost: '{{$centralSales->detail_other_cost}}',
            deposit_customer: '{{$centralSales->deposit_customer}}',
            receipt_1: '{{$centralSales->receipt_1}}',
            receive_1: '{{$centralSales->receive_1}}',
            receipt_2: '{{$centralSales->receipt_2}}',
            receive_2: '{{$centralSales->receive_2}}',
            recipient: '{{$centralSales->recipient}}',
            address_recipient: '{{$centralSales->address_recipient}}',
            detail: '{{$centralSales->detail}}',
            status: '{{$centralSales->status}}',
            customers: JSON.parse('{!! $customers !!}'),
            shipments: JSON.parse('{!! $shipments !!}'),
            accounts: JSON.parse('{!! $accounts !!}'),
            selectedProducts: JSON.parse('{!! $centralSales->products !!}'),
            shipment: {
                name: '',
                loading: false,
            },
            shipment_edit_id: null,
            shipment_edit_index: null,
            is_edit_shipment: false,
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.patch('/central-sale/approve/{{$centralSales->id}}', {
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
            subTotalProduct: function(product) {
                return Number(product.quantity) * Number(product.agent_price);
            },
            calculateBooked: function(product) {
                return Number(product.quantity) + Number(product.free);
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
            removeSelectedProduct: function(index) {
                this.selectedProducts.splice(index, 1);
            },
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
                if (this.discount_type === "percentage") {
                    let discount = Number(this.subTotal) * (Number(this.discount) / 100);
                    calculateDiscount = Number(this.subTotal) - discount;

                } else {
                    calculateDiscount = Number(this.subTotal) - Number(this.discount);
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

@endsection