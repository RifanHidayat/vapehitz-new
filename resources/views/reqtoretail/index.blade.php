@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title fw-normal">Permintaan Barang ke Gudang Retail</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <a href="/reqtoretail/create" class="btn btn-primary"><em class="fas fa-plus"></em>&nbsp;Buat Baru</a>
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
                                <th>Nomor Proses</th>
                                <th>Tanggal Proses</th>
                                <th>Status</th>
                                <th>Action</th>
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