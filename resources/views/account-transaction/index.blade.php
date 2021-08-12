@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')

<div class="nk-block-head nk-block-head-lg wide-lg">
    <div class="nk-block-head-content">
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Nomor</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="number" class="form-control" placeholder="Nomor">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Akun Masuk</label>
                            <div class="form-control-wrap">
                                <input type="number" v-model="in" class="form-control" placeholder="In">
                            </div>
                        </div>
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Akun Keluar</label>
                            <div class="form-control-wrap">
                                <input type="number" v-model="out" class="form-control" placeholder="Out">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Tanggal Transaksi</label>
                            <div class="form-control-wrap">
                                <input type="date" v-model="transaction_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Nominal</label>
                            <div class="form-control-wrap">
                                <input type="number" v-model="nominal" class="form-control text-right" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Catatan</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="note" class="form-control" placeholder="Catatan">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group col-md-6">
                        <label class="form-label" for="full-name-1">Jenis Pembayaran</label>
                        <div class="form-control-wrap">
                            <select v-model="type" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="Hutang">Hutang</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="form-group col-md-12">
                            <div class="text-right">
                                <button class="btn btn-primary">Simpan</button>
                                <!-- <button v-if="is_edit_account == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                                &times;
                            </button>
                            <button class="btn btn-primary" type="submit" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>@{{is_edit_account ? "Edit" : "Simpan" }}</span>
                            </button> -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- .nk-block -->
<div class="nk-block nk-block-lg">
    <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <div class="table-responsive">
                <table class="datatable-init table table-striped" id="table-account">
                    <thead>
                        <tr class="text-center">
                            <th>Nomor</th>
                            <th>Nama</th>
                            <th>Saldo</th>
                            <th>Jenis Transaksi</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- .nk-block -->
</div>
@endsection