@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title fw-normal">Retur Pembelian Barang</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <a href="#" class="btn btn-primary"><em class="fas fa-plus"></em>&nbsp;Retur Barang</a>
        <p></p>
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="returSupplier">
                        <thead>
                            <tr>
                                <th>Nomor Order</th>
                                <th>Tanggal Order</th>
                                <th>Nama Supplier</th>
                                <th>Total</th>
                                <th>Biaya Kirim</th>
                                <th>Diskon</th>
                                <th>Grand Total</th>
                                <th>Jumlah Pembayaran</th>
                                <th>Sisa Pembayaran</th>
                                <th>Status</th>
                                <th>Retur</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection