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
<div class="card card-bordered">
    <div class="card-inner-group">
        <div class="card-inner card-inner-md">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Tambah Data Penjualan Barang</h6>
                </div>
            </div>
        </div>
        <form @submit.prevent="submitForm">
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
                <div class="form-group text-right">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
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
            code: '{{ $code }}',
            date: '',
            shipmentId: '',
            customerId: '',
            debt: '',
            customers: JSON.parse('{!! $customer !!}'),
            shipments: JSON.parse('{!! $shipment !!}'),
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
                        code: this.code,
                        date: this.date,
                        shipmentId: this.shipmentId,
                        customerId: this.customerId,
                        debt: this.debt,
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
        }
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