@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Detail Penjualan Barang</h3>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="/central-sale" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                    </div>
                </div>
            </div>
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
                            <input type="text" v-model="date" class="form-control" readonly>
                        </div>
                    </div>
                    <div class=" form-group col-md-6">
                        <label class="form-label" for="full-name-1">Shipment</label>
                        <div class="form-control-wrap">
                            <input type="text" v-model="shipmentId" class="form-control" readonly>
                        </div>
                    </div>
                    <div class=" form-group col-md-6">
                        <label class="form-label" for="full-name-1">Nama Pelanggan</label>
                        <div class="form-control-wrap">
                            <input type="text" v-model="customerId" class="form-control" readonly>
                        </div>
                    </div>
                    <div class=" form-group col-md-6">
                        <label class="form-label" for="full-name-1">Termin Hutang</label>
                        <div class="form-control-wrap">
                            <input type="text" v-model="debt" class="form-control" readonly>
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
                                    <td width="20%"><input type="text" v-model="total_weight" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Total Biaya</span></td>
                                    <td width="20%"><input type="text" v-model="total_cost" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td><span style="font-size: 12px; font-weight: bold;">Diskon</span></td>
                                    <td><input type="text" v-model="discount" class="form-control input-sm" readonly>
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
                                            <input type="text" v-model="subtotal" class="form-control text-right" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Biaya Kirim</span></td>
                                    <td width="20%"><input type="number" v-model="shipping_cost" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Biaya Lainnya</span></td>
                                    <td width="20%"><input type="number" v-model="other_cost" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;"></span></td>
                                    <td width="20%"><input type="text" v-model="detail_other_cost" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Deposit Customer</span></td>
                                    <td width="20%"><input type="number" v-model="deposit_customer" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Net Total</span></td>
                                    <td width="20%"><input type="text" v-model="net_total" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sumber Penerimaan 1</span></td>
                                    <td width="20%">
                                        <input type="text" v-model="receipt_1" class="form-control" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Rp.</span></td>
                                    <td width="20%"><input type="number" v-model="receive_1" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sumber Penerimaan 2</span></td>
                                    <td width="20%">
                                        <input type="text" v-model="receipt_2" class="form-control" readonly>
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
                                    <td width="20%"><input type="text" v-model="payment_amount" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Sisa Pembayaran</span></td>
                                    <td width="20%"><input type="text" v-model="remaining_payment" class="form-control" readonly></td>
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
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5" width="60%">&nbsp;</td>
                                    <td width="20%"><span style="font-size: 12px; font-weight: bold;">Status</span></td>
                                    <td width="20%"><input type="text" v-model="status" class="form-control"></td>
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
        </div>
    </div>
</div>
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
            total_weight: '{{$centralSales->total_weight}}',
            total_cost: '{{$centralSales->total_cost}}',
            subtotal: '{{$centralSales->subtotal}}',
            net_total: '{{$centralSales->net_total}}',
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
            payment_amount: '{{$centralSales->payment_amount}}',
            remaining_payment: '{{$centralSales->remaining_payment}}',
            recipient: '{{$centralSales->recipient}}',
            address_recipient: '{{$centralSales->address_recipient}}',
            detail: '{{$centralSales->detail}}',
            status: '{{$centralSales->status}}',
        }
    })
</script>

@endsection