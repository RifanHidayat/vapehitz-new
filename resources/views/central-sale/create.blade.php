@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="/stock-opname"><em class="icon ni ni-arrow-left"></em>
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
                    <label class="form-label" for="full-name-1">Tanggal Stok Opname</label>
                    <div class="form-control-wrap">
                        <input type="date" v-model="date" class="form-control">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Shipment</label>
                    <div class="form-control-wrap">
                        <select name="shipment" id="" class="form-control">
                            <option value="1">COD</option>
                            <option value="2">GOJEK</option>
                            <option value="3">KURIR</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Nama Pelanggan</label>
                    <div class="form-control-wrap">
                        <select name="customer" id="" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6">
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
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label" for="full-name-1">Termin Hutang</label>
                    <div class="form-control-wrap">
                        <select name="customer" id="" class="form-control">
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
    </div>
    </form>
</div>
@endsection