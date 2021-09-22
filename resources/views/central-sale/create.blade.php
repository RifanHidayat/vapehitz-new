@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/central-sale"><em class="icon ni ni-arrow-left"></em><span>Penjualan Barang</span></a></div>
        <h2 class="nk-block-title fw-normal">Tambah Data Penjualan Barang</h2>
    </div>
</div><!-- .nk-block -->
<form @submit.prevent="submitForm">
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">Informasi Penjualan</h5>
            </div>
            <div class="row">
                <div class="col-lg-8 col-sm-12">
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Tanggal Invoice</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <input type="date" v-model="date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Shipment</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="input-group">
                                        <select v-model="shipmentId" id="" class="form-control">
                                            <option v-for="shipment in shipments" :value="shipment.id">@{{shipment.name}}</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#shipmentModal">
                                                <em class="fas fa-plus"></em>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Pelanggan</label>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <select v-model="customerId" class="form-control" id="customer">
                                        <option v-for="customer in customers" :value="customer.id">@{{customer.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Nama Penerima</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <!-- <div class="form-icon form-icon-left">
                                        <span>Rp</span>
                                    </div> -->
                                    <input type="text" v-model="recipient" class="form-control" placeholder="Nama">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Alamat Penerima</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <!-- <div class="form-icon form-icon-left">
                                        <span>Rp</span>
                                    </div> -->
                                    <input type="text" v-model="address_recipient" class="form-control" placeholder="Alamat">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <!-- <div class="form-icon form-icon-left">
                                        <span>Rp</span>
                                    </div> -->
                                    <input type="text" v-model="detail" class="form-control" placeholder="Keterangan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Termin Hutang</label>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
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
            </div>
        </div>
    </div>
    <!-- <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner card-inner-md">
                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">Tambah Data Penjualan Barang</h6>
                    </div>
                </div>
            </div>

            <div class="card-inner">
                <div class="form-row">
                    <div class=" form-group col-lg-4 col-sm-12">
                        <label class="form-label" for="full-name-1">Nomor Invoice</label>
                        <div class="form-control-wrap">
                            <input type="text" v-model="code" class="form-control" readonly>
                        </div>
                    </div>
                    <div class=" form-group col-lg-4 col-sm-12">
                        <label class="form-label" for="full-name-1">Tanggal Invoice</label>
                        <div class="form-control-wrap">
                            <input type="date" v-model="date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-3 col-sm-12">
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
                    <div class="form-group col-lg-3 col-sm-12">
                        <label class="form-label" for="full-name-1">Pelanggan</label>
                        <div class="form-control-wrap">
                            <select v-model="customerId" class="form-control" id="customer">
                                <option v-for="customer in customers" :value="customer.id">@{{customer.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-lg-2 col-sm-12">
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
    </div> -->

    <div class="card card-bordered card-full">
        <div class="card-inner">
            <!-- <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title"><span class="mr-2">Daftar Produk</span></h6>
                </div>
                <div class="card-tools">
                    <ul class="card-tools-nav">
                        <li><a href="#"><span>Paid</span></a></li>
                        <li><a href="#"><span>Pending</span></a></li>
                        <li class="active"><a href="#"><span>All</span></a></li>
                    </ul>
                </div>
            </div> -->
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
        <div class="card-inner p-0">
            <div v-if="isStockUnsufficient" class="px-4 mb-2">
                <div class="alert alert-info alert-dismissible alert-icon">
                    <em class="icon ni ni-alert-circle"></em> Jumlah <strong>stok</strong> dan <strong>booking</strong> sudah diperbaharui. Sesuaikan jumlah penjualan pada produk yang ditandai.
                    <button class="close" data-dismiss="alert"></button>
                </div>
            </div>
            <div v-if="selectedProducts.length === 0" class="text-center text-soft py-3">
                <em class="fas fa-dolly fa-4x"></em>
                <p class="mt-3">Belum ada barang yang dipilih</p>
            </div>

            <div v-if="selectedProducts.length" class="nk-tb-list nk-tb-orders">
                <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col"><span>Nama Produk</span></div>
                    <!-- <div class="nk-tb-col tb-col-sm"><span>Berat</span></div> -->
                    <div class="nk-tb-col tb-col-md"><span>Booking</span></div>
                    <div class="nk-tb-col tb-col-lg"><span>Stok</span></div>
                    <!-- <div class="nk-tb-col"><span>&nbsp;</span></div> -->
                    <div class="nk-tb-col"><span>Harga Jual <em :class="!isAuthorizedProductPrice ? 'fas fa-lock' : 'fas fa-lock-open'"></em></span></div>
                    <div class="nk-tb-col"><span class="d-none d-sm-inline">Qty</span></div>
                    <div class="nk-tb-col"><span class="d-none d-sm-inline">Free</span></div>
                    <!-- <div class="nk-tb-col"><span class="d-none d-sm-inline">&nbsp;</span></div> -->
                    <div class="nk-tb-col"><span class="d-none d-sm-inline">Amount</span></div>
                    <div class="nk-tb-col"><span class="d-none d-sm-inline">&nbsp;</span></div>
                </div>
                <div class="nk-tb-item" v-for="(product, index) in selectedProducts" :key="index" :class="product.backgroundColor">
                    <div class="nk-tb-col">
                        <span class="tb-lead" style="width: 150px;"><span class="text-muted">@{{ product.code }}</span> - @{{ product.name }}</span>
                        <!-- <div>
                            <ul>
                                <li class="tb-sub">Kategori: Liquid</li>
                                <li class="tb-sub">Berat: 1000 gr</li>
                            </ul>
                        </div> -->
                    </div>
                    <div class="nk-tb-col tb-col-sm">
                        <span class="text-warning d-block">@{{ product.booked }}</span>
                        <span class="text-success d-block">+@{{ Number(product.quantity) + Number(product.free) }}</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span>@{{ product.central_stock }}</span>
                    </div>
                    <!-- <div class="nk-tb-col tb-col-lg">
                            <span class="tb-sub text-primary">SUB-2305564</span>
                        </div> -->
                    <div class="nk-tb-col">
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left">
                                <span>Rp</span>
                            </div>
                            <input type="text" v-model="product.price" @input="calculateProductSubtotal(product)" @change="onChangeProductPrice(product, index)" class="form-control text-right" placeholder="Harga">
                        </div>
                    </div>
                    <div class="nk-tb-col">
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-right">
                                <span>Pcs</span>
                            </div>
                            <input type="text" v-model="product.quantity" @input="calculateProductSubtotal(product)" @change="onChangeQuantity(product)" class="form-control text-right" placeholder="Qty">
                        </div>
                    </div>
                    <div class="nk-tb-col">
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-right">
                                <span>Pcs</span>
                            </div>
                            <input type="text" v-model="product.free" @change="onChangeFree(product)" class="form-control text-right" placeholder="Free">
                        </div>
                    </div>
                    <!-- <div class="nk-tb-col">
                        <div class="custom-control custom-control-sm custom-checkbox">
                            <input type="checkbox" @change="calculateProductSubtotal(product)" v-model="product.editable" class="custom-control-input" :id="'customCheck' + product.id">
                            <label class="custom-control-label" :for="'customCheck' + product.id"></label>
                        </div>
                    </div> -->
                    <div class="nk-tb-col">
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left">
                                <span>Rp</span>
                            </div>
                            <input type="text" v-model="product.subTotal" class="form-control text-right" placeholder="Subtotal" :readonly="!product.editable">
                        </div>
                    </div>
                    <div class="nk-tb-col">
                        <a href="#" @click.prevent="removeSelectedProduct(index)" class="btn btn-icon btn-trigger text-danger"><em class="icon ni ni-trash"></em></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4" v-if="selectedProducts.length">
        <div class="col-lg-7 col-sm-12">
            <div class="card card-bordered h-100">
                <div class="card-inner">
                    <div class="card-head">
                        <h6 class="card-title">Informasi Penjualan</h6>
                    </div>
                    <div class="row align-center mb-1">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Diskon</label>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <span v-if="discount_type =='nominal'">Rp</span>
                                            <span v-else>%</span>
                                        </div>
                                        <input type="text" v-model="discount" class="form-control text-right" placeholder="Diskon">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <select class="form-control" v-model="discount_type">
                                            <option value="nominal">Nominal</option>
                                            <option value="percentage">Persentase</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Biaya Kirim</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <span>Rp</span>
                                    </div>
                                    <input type="text" v-model="shipping_cost" class="form-control text-right" placeholder="Biaya Kirim">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-1">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Biaya Lainnya</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <span>Rp</span>
                                        </div>
                                        <input type="text" v-model="other_cost" class="form-control text-right" placeholder="Biaya Lainnya">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="detail_other_cost" class="form-control" placeholder="Keterangan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Deposit Pelanggan</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <span>Rp</span>
                                    </div>
                                    <input type="text" v-model="deposit_customer" class="form-control text-right" placeholder="Deposit">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-1">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Sumber Penerimaan 1</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <span>Rp</span>
                                        </div>
                                        <input type="text" v-model="receive_1" class="form-control text-right" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <select v-model="receipt_1" class="form-control" :required="receive_1 !== '' && receive_1 !== null">
                                            <option v-for="(account, index) in accounts" :value="account.id">@{{ account.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-center mb-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label">Sumber Penerimaan 2</label>
                                <!-- <span class="form-note">Specify the name of your website.</span> -->
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <span>Rp</span>
                                        </div>
                                        <input type="text" v-model="receive_2" class="form-control text-right" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-control-wrap">
                                        <select v-model="receipt_2" class="form-control" :required="receive_2 !== '' && receive_2 !== null">
                                            <option v-for="(account, index) in accounts" :value="account.id">@{{ account.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p v-if="totalPayment > netTotal" class="text-soft"><em class="icon ni ni-info text-warning align-middle" style="font-size: 1.2em;"></em> Total penerimaan lebih besar dari net total</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit" :disabled="loading || totalPayment > netTotal">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Simpan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-sm-12">
            <div class="card card-bordered h-100 bg-light">
                <div class="card-inner">
                    <h6 class="card-title mb-5">Summary</h6>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Total Berat (Gram)</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(totalWeight) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Total Amount</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(totalAmount) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Diskon</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(finalDiscount) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Subtotal</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(subTotal) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Biaya Kirim</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(shipping_cost) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Biaya Lainnya</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(other_cost) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Deposit Pelanggan</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(deposit_customer) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Net Total</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(netTotal) }}</strong></p>
                    </div>
                    <div class="row justify-content-between mb-2 border-bottom pb-2">
                        <p class="col-md-6 card-text mb-0">Jumlah Bayar</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(totalPayment) }}</strong></p>
                    </div>
                    <div class="row justify-content-between pb-2">
                        <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(remainingPayment) }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
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
            <div class="modal-body">
                <form @submit.prevent="is_edit_shipment ? editShipment(shipment_edit_id,shipment_edit_index):addShipment()">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="">Nama Shipment</label>
                            <input type="text" class="form-control" v-model="shipment.name">
                        </div>
                    </div>
                    <div class="mt-2">
                        <button v-if="is_edit_shipment == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                            &times
                        </button>
                        <button class="btn btn-primary" type="submit" :disabled="shipment.loading">
                            <span v-if="shipment.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>@{{is_edit_shipment ? "Edit" : "Simpan" }}</span>
                        </button>
                    </div>
                </form>
                <div class="divider"></div>
                <div>
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
                                        <a href="#" @click.prevent="deleteShipment(shipment.id)" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- <div class="bg-white" style="height: 50px; width: 100%; position: fixed; bottom: 0; left: 0">Cookies</div> -->
<!-- End Shipment Modal -->
<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Pilih Produk</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#checkModal">
                        <div class="d-flex align-items-center">
                            <em class="icon ni ni-cart"></em>&nbsp;<span v-if="check.length > 0" class="badge badge-pill badge-light">@{{ check.length }}</span>
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
<!-- Auth Product Price Modal -->
<div class="modal fade" id="authPriceModal" tabindex="-1" role="dialog" aria-labelledby="authPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authPriceModalLabel">Otorisasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="sendAuthProductPrice">
                <div class="modal-body">
                    <p class="text-muted"><em>Masukkan username dan password user yang memiliki izin untuk melakukan perubahan harga</em></p>
                    <div class="form-group">
                        <label for="">Username</label>
                        <input type="text" v-model="authProductPriceModel.username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" v-model="authProductPriceModel.password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn btn-primary" :disabled="authProductPriceModel.loading">
                        <span v-if="authProductPriceModel.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Kirim</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            productPriceLocked: true,
            priceAuthProductIndex: null,
            isAuthorizedProductPrice: false,
            authProductPriceModel: {
                username: '',
                password: '',
                loading: false,
            },
            isStockUnsufficient: false,
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
                        total_cost: vm.totalAmount,
                        discount: vm.discount,
                        discount_type: vm.discount_type,
                        subtotal: vm.subTotal,
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
                        // quantity: vm.quantity,
                        // price: vm.agent_price,
                        // free: vm.free,
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
                        if (error.response.data.error_type == 'unsufficient_stock') {
                            Swal.fire(
                                'Oops!',
                                'Jumlah Penjualan Melebihi Stok',
                                'warning'
                            );
                            vm.selectedProducts = error.response.data.data.selected_products;
                            vm.isStockUnsufficient = true;
                        } else {
                            console.log(error);
                            Swal.fire(
                                'Oops!',
                                'Something wrong',
                                'error'
                            )
                        }
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
            deleteShipment: function(id) {
                console.log(id)
                let vm = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "The data will be deleted",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.delete('/shipment/' + id)
                            .then(function(response) {
                                console.log(response.data);
                            })
                            .catch(function(error) {
                                console.log(error.data);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops',
                                    text: 'Something wrong',
                                })
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    vm.shipments = vm.shipments.filter(shipment => shipment.id !== id)
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data has been deleted',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // window.location.reload();
                                // invoicesTable.ajax.reload();
                            }
                        })
                    }
                });
            },
            onSelectedProduct: function() {
                const selectedProductIds = this.selectedProducts.map(product => product.id);
                const productsInCheck = this.check.filter(product => selectedProductIds.indexOf(product.id) < 0);
                this.check.filter(product => selectedProductIds.indexOf(product.id) > -1)
                    .map(product => product.id)
                    .forEach(productId => {
                        const index = selectedProductIds.findIndex((id) => id == productId);
                        let selectedProduct = this.selectedProducts[index];
                        //check if stock still available
                        let taken = Number(selectedProduct.booked) + Number(selectedProduct.quantity) + Number(selectedProduct.free);
                        if (taken < Number(selectedProduct.central_stock)) {
                            // selectedProduct.quantity = Number(selectedProduct.quantity) - 1;
                            selectedProduct.quantity = Number(selectedProduct.quantity) + 1;
                        }
                        selectedProduct.subTotal = Number(selectedProduct.quantity) * Number(selectedProduct.price);

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
            },
            calculateBooked: function(product) {
                return Number(product.quantity) + Number(product.free);
            },
            increaseProductQuantity: function(product) {
                product.quantity = product.quantity + 1;
            },
            reduceProductQuantity: function(product) {
                if (product.quantity > 1) {
                    product.quantity = product.quantity - 1;
                }
            },
            calculateProductSubtotal: function(product) {
                if (!product.editable) {
                    product.subTotal = Number(product.price) * Number(product.quantity);

                    if (isNaN(product.subTotal)) {
                        product.subTotal = 0;
                    }
                }
            },
            getProductSubtotal: function(product) {
                if (!product.editable) {
                    let subTotal = Number(product.price) * Number(product.quantity);
                    if (isNaN(product.subTotal)) {
                        return 0;
                    }

                    return subTotal;
                } else {
                    return product.subTotal;
                }
            },
            onChangeProductPrice: function(product, index) {
                let vm = this;
                if (!vm.isAuthorizedProductPrice) {
                    if (product.is_changeable !== 1) {
                        if (product.price < product.agent_price) {
                            // console.log('price is lower')
                            $('#authPriceModal').modal('show')
                            this.priceAuthProductIndex = index;
                            // this.priceAuthProductPrice = price;
                        }
                    }
                }
            },
            sendAuthProductPrice: function() {
                let vm = this;
                vm.authProductPriceModel.loading = true;
                axios.post('/central-sale/action/auth-product-price', {
                        username: vm.authProductPriceModel.username,
                        password: vm.authProductPriceModel.password,
                    })
                    .then(function(response) {
                        vm.authProductPriceModel.loading = false;
                        vm.isAuthorizedProductPrice = true;
                        $('#authPriceModal').modal('hide');
                    })
                    .catch(function(error) {
                        vm.authProductPriceModel.loading = false;
                        Swal.fire(
                            'Kesalahan',
                            error.response.data.message,
                            'warning'
                        );
                    });
            },
            onChangeQuantity: function(product) {
                const {
                    booked,
                    central_stock,
                    quantity,
                    free
                } = product;
                let taken = Number(booked) + Number(quantity) + Number(free);
                if (taken > Number(central_stock)) {
                    const maxAvailable = Number(central_stock) - (Number(booked) + Number(free));
                    product.quantity = maxAvailable;
                    product.subTotal = this.getProductSubtotal(product);
                }
            },
            onChangeFree: function(product) {
                const {
                    booked,
                    central_stock,
                    quantity,
                    free
                } = product;
                let taken = Number(booked) + Number(quantity) + Number(free);
                console.log(taken);
                if (taken > Number(central_stock)) {
                    const maxAvailable = Number(central_stock) - (Number(booked) + Number(quantity));
                    // console.log(central_stock, booked, quantity, maxAvailable);
                    product.free = maxAvailable;
                    product.subTotal = this.getProductSubtotal(product);
                }
            },
        },
        computed: {
            totalAmount: function() {
                // return this.selectedProducts.map(product => {
                //     Number(product.purchase_price) * this.qty;
                // });
                const totalAmount = this.selectedProducts.map(product => {
                        // const amount = Number(product.price) * Number(product.quantity);
                        // return amount;
                        return Number(product.subTotal)
                    })
                    .reduce((acc, cur) => {
                        return acc + cur;
                    }, 0);
                return totalAmount;
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
            finalDiscount: function() {
                let finalDiscount = this.discount;
                if (this.discount_type === "nominal") {
                    // finalDiscount = Number(this.totalAmount) - Number(this.discount);
                } else {
                    finalDiscount = Number(this.totalAmount) * (Number(this.discount) / 100);
                    // finalDiscount = Number(this.totalAmount) - discount;
                }
                return finalDiscount;
            },
            subTotal: function() {
                return this.totalAmount - this.finalDiscount;
            },
            netTotal: function() {
                const netTotal = Number(this.subTotal) + Number(this.shipping_cost) + Number(this.other_cost) - Number(this.deposit_customer);
                return netTotal;
            },
            totalPayment: function() {
                const totalPayment = Number(this.receive_1) + Number(this.receive_2);
                return totalPayment;
            },
            remainingPayment: function() {
                const remainingPayment = this.netTotal - this.totalPayment;
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
                data['free'] = 0;
                data['editable'] = 0;
                data['price'] = data.agent_price;
                data['subTotal'] = data.agent_price;
                data['backgroundColor'] = 'bg-white';
                check.push(data);
            }
        });

        $('#authPriceModal').on('hide.bs.modal', function(e) {
            const isAuthorized = app.$data.isAuthorizedProductPrice;
            const selectedIndex = app.$data.priceAuthProductIndex;
            if (!isAuthorized) {
                // let product = app.$data.selectedProducts(product => product.id == priceAuthProductId)[0];
                // if(product && typeof(product) !== "undefined") {

                // }
                const product = app.$data.selectedProducts[selectedIndex];
                if (typeof product !== "undefined") {
                    product.price = product.agent_price;
                    if (!product.editable) {
                        product.subTotal = product.price * product.quantity;
                    }
                }
            }

            app.$data.priceAuthProductIndex = null;
        })
    });
</script>
@endsection