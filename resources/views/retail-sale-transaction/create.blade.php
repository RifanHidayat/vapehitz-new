@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/retail-sale-transaction"><em class="icon ni ni-arrow-left"></em><span>Pembayaran Retail</span></a></div>
        <h2 class="nk-block-title fw-normal">Pembayaran Penjualan Retail</h2>
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
                                <em class="icon ni ni-report-profit mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Total Invoice</span>
                                    <p class="amount"><strong>0</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-coins mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Jumlah Pembayaran</span>
                                    <p class="text-lg"><strong>@{{ payAmount == '' ? 0 : payAmount }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-list-check mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Total Invoice Dipilih</span>
                                    <p class="amount"><strong>@{{ currencyFormat(totalSelectedSales) }}</strong></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div><!-- .nk-block -->
    <div class="mt-4">
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
                    <!-- <div class="mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" checked="" id="customSwitch2">
                            <label class="custom-control-label" for="customSwitch2">Pilih Manual</label>
                        </div>
                    </div> -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="retail-sales-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                            <input type="checkbox" v-model="isCheckedAll" @change="toggleCheckAll($event)" class="custom-control-input" id="customCheckAll">
                                            <label class="custom-control-label" for="customCheckAll"></label>
                                        </div>
                                    </th>
                                    <th>No. Invoice</th>
                                    <th>Tanggal</th>
                                    <th class="text-right">Sisa Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                            <input type="checkbox" v-model="checkedSales" value="{{ $sale->id }}" class="custom-control-input" id="customCheck{{ $index }}">
                                            <label class="custom-control-label" for="customCheck{{ $index }}"></label>
                                        </div>
                                    </td>
                                    <td><a href="#">{{ $sale->code }}</a></td>
                                    <td>{{ date("d-m-Y", strtotime($sale->date)) }}</td>
                                    <td class="text-right">{{ number_format($sale->net_total - $sale->total_payment) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            transactions: [],
            sales: JSON.parse(String.raw `{!! json_encode($sales) !!}`),
            loading: false,
            cleaveCurrency: {
                delimiter: '.',
                numeralDecimalMark: ',',
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            },
            checkedSales: [],
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/retail-sale-transaction/action/bulk-store', {
                        date: vm.date,
                        // customer_id: vm.customerId,
                        account_id: vm.accountId,
                        sale_id: vm.saleId,
                        payment_method: vm.paymentMethod,
                        amount: vm.payAmount,
                        note: vm.note,
                        selected_sales: vm.selectedSales,
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
                                // window.location.href = '/customer';
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
                const totalSelectedSales = Number(this.totalSelectedSales);
                if (payAmount > totalSelectedSales) {
                    this.payAmount = totalSelectedSales;
                }
            },
            toggleCheckAll: function(e) {
                const isChecked = e.target.checked;
                // If Unchecked
                if (!isChecked) {
                    this.checkedSales = [];
                } else { // If Checked
                    this.checkedSales = this.sales.map(sale => sale.id.toString());
                }
            },
        },
        computed: {
            selectedSales: function() {
                let vm = this;
                return vm.sales.filter(sale => vm.checkedSales.indexOf(sale.id.toString()) > -1);
            },
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
            totalSelectedSales: function() {
                return this.selectedSales.map(sale => Number(sale.net_total)).reduce((acc, cur) => {
                    return acc + cur;
                }, 0);
            },
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
            },
            isCheckedAll: {
                // getter
                get: function() {
                    return this.checkedSales.length == this.sales.length;
                },
                // setter
                set: function(newValue) {
                    return newValue;
                }
            }
        }
    })
</script>
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#retail-sales-table', {
                order: [
                    [2, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();
    })
</script>
@endsection