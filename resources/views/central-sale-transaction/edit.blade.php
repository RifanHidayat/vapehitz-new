@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/central-sale"><em class="icon ni ni-arrow-left"></em><span>Penjualan Barang</span></a></div>
        <h2 class="nk-block-title fw-normal">Pembayaran Penjualan Barang</h2>
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
    <div class="row g-gs align-items-start">
        <div class="col-lg-7 col-md-12">

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Informasi Penjualan</h6>
                            </div>
                            <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="collapse" href="#collapseSaleInfo" role="button" aria-expanded="false" aria-controls="collapseExample"><em class="icon ni ni-downward-ios"></em></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                    <div class="collapse" id="collapseSaleInfo">
                        <div class="card-inner">
                            <!-- <div class="row"> -->
                            <!-- <div class="col-lg-6 col-md-12"> -->
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Nomor Invoice</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->code }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Tanggal Invoice</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->date }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Customer</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->customer->name }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Pengiriman</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->shipment->name }}</strong></p>
                            </div>
                            <!-- </div> -->
                            <!-- <div class="col-lg-6 col-md-12"> -->
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Nama Penerima</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->recipient }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Alamat Penerima</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->recipient_address }}</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Termin Hutang</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->debt }} Hari</strong></p>
                            </div>
                            <div class="row justify-content-between mb-2">
                                <p class="col-md-6 mb-0 text-soft">Keterangan</p>
                                <p class="col-md-6 text-right mb-0"><strong>{{ $sale->detail }}</strong></p>
                            </div>
                            <!-- </div> -->
                            <!-- </div> -->
                        </div><!-- .card-inner -->
                    </div>
                </div><!-- .card-inner-group -->
            </div>

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Riwayat Pembayaran</h6>
                            </div>
                            <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="collapse" href="#collapsePaymentHistory" role="button" aria-expanded="false" aria-controls="collapsePaymentHistory"><em class="icon ni ni-downward-ios"></em></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                    <div class="collapse" id="collapsePaymentHistory">
                        <div class="card-inner">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kode Transaksi</th>
                                            <th>Metode</th>
                                            <th class="text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subTotal = 0; @endphp
                                        @if(count($transactions) == 0)
                                        <tr>
                                            <td colspan="4" class="text-center"><em class="text-soft">Belum ada pembayaran</em></td>
                                        </tr>
                                        @else
                                        @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                                            <td><a href="/central-sale-transaction/detail/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
                                            <td class="text-capitalize">{{ $transaction->payment_method }}</td>
                                            <td class="text-right">{{ number_format($transaction->pivot->amount) }}</td>
                                        </tr>
                                        @php $subTotal += $transaction->pivot->amount; @endphp
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">Subtotal</td>
                                            <td class="text-right">{{ number_format($subTotal) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="border-top: none;">Total Pembelian</td>
                                            <td class="text-right" style="border-top: none;">{{ number_format($sale->net_total) }}</td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td colspan="3">Sisa Hutang</td>
                                            <td class="text-right">{{ number_format(abs($subTotal - $sale->net_total)) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><!-- .card-inner -->
                    </div>
                </div><!-- .card-inner-group -->
            </div>

            <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Daftar Produk</h6>
                            </div>
                            <!-- <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="modal" href="#exampleModal" data-backdrop="static" data-keyboard="false"><em class="icon ni ni-plus"></em></a>
                                    </li>
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" @click.prevent="removeAllSelectedProducts"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
                        </div>
                    </div><!-- .card-inner -->
                    <div class="card-inner">
                        <div class="mb-3">
                            <div class="alert alert-primary alert-icon">
                                <em class="icon ni ni-alert-circle"></em> Informasi yang tercantum merupakan informasi ketika penjualan (Update terakhir: {{ $sale->updated_at }})
                            </div>
                        </div>
                        <!-- <div class="nk-wg-action">
                                <div class="nk-wg-action-content">
                                    <em class="icon ni ni-cc-alt-fill"></em>
                                    <div class="title">Alacarte Black Strawberry</div>
                                    <p>We have still <strong>40 buy orders</strong> and <strong>12 sell orders</strong>, thats need to review &amp; take necessary action.</p>
                                </div>
                                <a href="#" class="btn btn-icon btn-trigger mr-n2"><em class="icon ni ni-trash"></em></a>
                            </div> -->
                        <div v-if="selectedProducts.length === 0" class="text-center text-soft">
                            <em class="fas fa-dolly fa-4x"></em>
                            <p class="mt-3">Tidak ada barang</p>
                        </div>
                        <div v-for="(product, index) in selectedProducts" :key="index" class="card card-bordered">
                            <div class="card-inner">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-12">
                                        <h5 class="card-title">@{{ product.name }}</h5>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Kode Barang</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ product.code }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Stok Gudang</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ product.pivot.stock }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Booking</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ product.pivot.booked }}</strong></p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Harga</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.price) }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Quantity</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.quantity) }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Amount</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.amount) }}</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-2 text-right">
                                        <a href="#" @click.prevent="removeSelectedProduct(index)" class="btn btn-icon btn-trigger text-danger"><em class="icon ni ni-trash"></em></a>
                                    </div> -->
                                </div>
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                            </div>
                        </div>
                        <div class="card bg-light">
                            <!-- <div class="card-header">Header</div> -->
                            <div class="card-inner">
                                <h5 class="card-title">Summary</h5>
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                                <!-- <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Customer</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong><a href="/customer/{{ $sale->customer->id }}" target="_blank">{{ $sale->customer->name }}</a></strong></p>
                                </div> -->
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Total Berat (Gram)</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->total_weight) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Total Amount</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->total_cost) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Diskon</p>
                                    <p class="col-md-6 text-right card-text mb-0">
                                        @if($sale->discount_type == 'percentage')
                                        <strong>{{ number_format($sale->discount) }}% ({{ number_format($sale->discount * ($sale->total_cost / 100)) }})</strong>
                                        @else
                                        <strong>{{ number_format($sale->discount) }}</strong>
                                        @endif
                                    </p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Subtotal</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->subtotal) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Biaya Kirim</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->shipping_cost) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Biaya Lainnya</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->other_cost) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Deposit Pelanggan</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->deposit_customer) }}</strong></p>
                                </div>
                                <div class="row justify-content-between mb-2 pb-2">
                                    <p class="col-md-6 card-text mb-0">Net Total</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->net_total) }}</strong></p>
                                </div>
                                <!-- <div class="row justify-content-between mb-2 border-bottom pb-2">
                                    <p class="col-md-6 card-text mb-0">Jumlah Bayar</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->total_payment) }}</strong></p>
                                </div>
                                <div class="row justify-content-between pb-2">
                                    <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($sale->remaining_payment) }}</strong></p>
                                </div> -->
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                </div><!-- .card-inner-group -->
            </div>
        </div>
        <div class="col-lg-5 col-md-12">
            <div class="card card-bordered mb-4">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Summary Pembayaran</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-wallet-in mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Total Dibayarkan</span>
                                    <p class="amount"><strong>{{ number_format($total_paid, 0, ',', '.') }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-coins mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Sisa Pembayaran</span>
                                    <p class="text-lg"><strong>{{ number_format($sale->net_total -  $total_paid, 0, ',', '.') }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <form @submit.prevent="submitForm">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Informasi Pembayaran</h6>
                                </div>
                                <!-- <div class="card-tools mr-n1">
                                <div class="drodown">
                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><em class="icon ni ni-plus"></em><span>Tambah</span></a></li>
                                            <li><a href="#"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                            </div>
                        </div><!-- .card-inner -->
                        <div class="card-inner">
                            <!-- <div class="form-group col-md-12">
                            <label class="form-label" for="full-name-1">Nomor Order</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="code" class="form-control" readonly>
                            </div>
                        </div> -->

                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Tanggal Pembayaran</label>
                                <div class="form-control-wrap">
                                    <input type="date" v-model="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Jumlah Bayar</label>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <!-- <em class="icon ni ni-user"></em> -->
                                        <span>Rp</span>
                                    </div>
                                    <input type="text" v-model="payAmount" @input="validatePayAmount" v-cleave="cleaveCurrency" class="form-control text-right" placeholder="Jumlah Bayar">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Cara Pembayaran</label>
                                    <div class="form-control-wrap">
                                        <select v-model="paymentMethod" class="form-control" required>
                                            <option value="transfer">Transfer</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Akun</label>
                                    <div class="form-control-wrap">
                                        <select v-model="accountId" class="form-control" required>
                                            <option v-for="(account, index) in accountOptions" :value="account.id">@{{ account.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Note</label>
                                <div class="form-control-wrap">
                                    <textarea v-model="note" class="form-control" rows="3" style="min-height: auto;"></textarea>
                                </div>
                            </div>
                            <!-- <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div> -->
                        </div>
                    </div>
                    <div class="card-footer border-top bg-white text-right">
                        <button v-if="totalPayments < netto" class="btn btn-primary" type="submit" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Simpan</span>
                        </button>
                        <span v-else class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- .nk-block -->

    @endsection
    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    @endsection
    @section('pagescript')
    <script>
        Vue.directive('cleave', {
            inserted: (el, binding) => {
                el.cleave = new Cleave(el, binding.value || {})
            },
            update: (el) => {
                const event = new Event('input', {
                    bubbles: true
                });
                setTimeout(function() {
                    el.value = el.cleave.properties.result
                    el.dispatchEvent(event)
                }, 100);
            }
        });
        let app = new Vue({
            el: '#app',
            data: {
                // code: '{{ $sale->code }}',
                date: '{{ date("Y-m-d") }}',
                payAmount: 0,
                customerId: '{{ $sale->customer->id }}',
                shippingCost: '{{ $sale->shipping_cost }}',
                discount: '{{ $sale->discount }}',
                isPaid: false,
                paymentMethod: '{{ $sale->payment_method }}',
                accounts: JSON.parse('{!! $accounts !!}'),
                accountId: '',
                saleId: '{{ $sale->id }}',
                netto: '{{ $sale->net_total }}',
                suppliers: [],
                cart: [],
                note: '',
                selectedProducts: JSON.parse(String.raw `{!! $sale->products !!}`),
                transactions: JSON.parse(String.raw `{!! json_encode($transactions) !!}`),
                loading: false,
                cleaveCurrency: {
                    delimiter: '.',
                    numeralDecimalMark: ',',
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand'
                },
            },
            methods: {
                submitForm: function() {
                    this.sendData();
                },
                sendData: function() {
                    // console.log('submitted');
                    let vm = this;
                    vm.loading = true;
                    axios.post('/central-sale-transaction', {
                            date: vm.date,
                            customer_id: vm.customerId,
                            account_id: vm.accountId,
                            sale_id: vm.saleId,
                            payment_method: vm.paymentMethod,
                            amount: vm.payAmount,
                            note: vm.note,
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
                currencyFormat: function(number) {
                    return Intl.NumberFormat('de-DE').format(number);
                },
                validatePayAmount: function() {
                    const payAmount = this.payAmount.replaceAll(".", "");
                    const netto = Number(this.netto);
                    if (payAmount > netto) {
                        this.payAmount = netto - this.totalPayments;
                    }
                },
            },
            computed: {
                // subTotal: function() {
                //     const subTotal = this.selectedProducts.map(product => {
                //         const amount = Number(product.pivot.quantity) * Number(product.pivot.price);
                //         return amount;
                //     }).reduce((acc, cur) => {
                //         return acc + cur;
                //     }, 0);

                //     return subTotal;
                // },
                // netTotal: function() {
                //     const netTotal = Number(this.subTotal) + Number(this.shippingCost) - Number(this.discount);
                //     return netTotal;
                // },
                // payment: function() {
                //     if (this.isPaid) {
                //         return this.netTotal;
                //     }

                //     return 0;
                // },
                // changePayment: function() {
                //     return this.netTotal - this.payment;
                // },
                accountOptions: function() {
                    let vm = this;
                    if (this.paymentMethod !== '') {
                        return this.accounts.filter(account => account.type == vm.paymentMethod);
                    }

                    return this.accounts;
                },
                totalPayments: function() {
                    return this.transactions.map(transaction => Number(transaction.pivot.amount)).reduce((acc, cur) => {
                        return acc + cur;
                    }, 0);
                    // return this.transactions;
                    // return totalPayments;
                }
            }
        })
    </script>
    @endsection