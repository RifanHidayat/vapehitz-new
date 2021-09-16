@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/central-purchase"><em class="icon ni ni-arrow-left"></em><span>Pembelian Barang</span></a></div>
        <h2 class="nk-block-title fw-normal">Retur Pembelian Barang</h2>
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
                                <h6 class="title">Pilih Barang Retur</h6>
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
                                                    <li><a href="#" @click.prevent="removeAllproducts"><em class="icon ni ni-notify"></em><span>Hapus Semua</span></a></li>
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
                            <div class="alert alert-primary alert-icon alert-dismissible">
                                <em class="icon ni ni-alert-circle"></em> Informasi yang tercantum merupakan informasi ketika pembelian
                                <button class="close" data-dismiss="alert"></button>
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
                        <div v-if="products.length === 0" class="text-center text-soft">
                            <em class="fas fa-dolly fa-4x"></em>
                            <p class="mt-3">Tidak ada barang</p>
                        </div>
                        <div v-for="(product, index) in products" :key="index" class="card card-bordered">
                            <div class="card-inner">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-lg-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" v-model="checkedProducts" class="custom-control-input" :value="product.id" :id="'customCheck' + product.id">
                                            <label class="custom-control-label" :for="'customCheck' + product.id"></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-11">
                                        <h5 class="card-title">@{{ product.name }}</h5>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Kode Barang</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ product.code }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Stok Gudang</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ product.central_stock }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Harga</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.price) }}</strong></p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Quantity</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.quantity-product.pivot.return_quantity) }}</strong></p>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <p class="col-md-6 mb-0">Amount</p>
                                                    <p class="col-md-6 text-right mb-0"><strong>@{{ currencyFormat(product.pivot.quantity * product.pivot.price) }}</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="isChecked(product.id)">
                                            <div class="divider"></div>
                                            <div class="form-row">
                                                <div class="form-group col-lg-6 col-md-12">
                                                    <label class="form-label" for="full-name-1">Quantity</label>
                                                    <div class="form-control-wrap number-spinner-wrap">
                                                        <button type="button" @click="reduceProductQuantity(product)" class="btn btn-icon btn-outline-light number-spinner-btn number-minus" :disabled="product.return_quantity === 1"><em class="icon ni ni-minus"></em></button>
                                                        <input type="number" v-model="product.return_quantity" @input="validateReturnQuantity(product)" class="form-control number-spinner" value="0">
                                                        <button type="button" @click="increaseProductQuantity(product)" class="btn btn-icon btn-outline-light number-spinner-btn number-plus" :disabled="product.return_quantity == (product.pivot.quantity)-product.pivot.return_quantity"><em class="icon ni ni-plus"></em></button>
                                                    </div>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-12">
                                                    <label class="form-label" for="full-name-1">Alasan</label>
                                                    <div class="form-control-wrap">
                                                        <select v-model="product.cause" class="form-control" required>
                                                            <option value="defective">Barang Cacat / Rusak</option>
                                                            <option value="wrong">Barang Tidak Sesuai</option>
                                                        </select>
                                                    </div>
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
             
                        <!-- End:Summary -->
                    </div><!-- .card-inner -->
                </div><!-- .card-inner-group -->
            </div>
        </div>
        <div class="col-lg-5 col-md-12">
            <div class="card card-bordered mb-4">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Summary Retur</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-layers mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Quantity Retur</span>
                                    <p class="amount" ><strong>@{{ currencyFormat(totalReturnQuantity) }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-coin mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Nominal Retur</span>
                                    <p class="text-lg"><strong>@{{ currencyFormat(totalReturnNominal) }}</strong></p>
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
                                    <h6 class="title">Informasi Retur</h6>
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
                                <label class="form-label" for="full-name-1">Tanggal Retur</label>
                                <div class="form-control-wrap">
                                    <input type="date" v-model="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Cara Pembayaran</label>
                                    <div class="form-control-wrap">
                                        <select v-model="paymentMethod" class="form-control" required>
                                            <option value="transfer">Transfer</option>
                                            <option value="cash">Cash</option>
                                            <option value="hutang" v-if="remainingPay>0">Hutang</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Akun</label>
                                    <div class="form-control-wrap">
                                        <select v-model="accountId" class="form-control" >
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
                            <hr>
                                             <!-- Summary -->
                        <div class="card bg-light">
                            <!-- <div class="card-header">Header</div> -->
                            <div class="card-inner">
                                <h5 class="card-title">Summary Pembelian</h5>
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Supplier</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong><a href="/supplier/detail/{{ $purchase->supplier->id }}" target="_blank">{{ $purchase->supplier->name }}</a></strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Subtotal</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($purchase->total) }}</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Biaya Kirim</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($purchase->shipping_cost) }}</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Diskon</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($purchase->discount) }}</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Net Total</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($purchase->netto) }}</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Jumlah Bayar</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($payAmountPurchase) }}</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Sisa bayar</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format(($purchase->netto-$payAmountPurchase)) }}</strong></p>
                                </div>
                                <!-- <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(changePayment) }}</strong></p>
                                    </div> -->
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
                        <button class="btn btn-primary" type="submit" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Simpan</span>
                        </button>
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
                // code: '{{ $purchase->code }}',
                date: '{{ date("Y-m-d") }}',
                payAmount: 0,
                suppliersId: '{{ $purchase->supplier->id }}',
                shippingCost: '{{ $purchase->shipping_cost }}',
                discount: '{{ $purchase->discount }}',
                isPaid: false,
                paymentMethod: '{{ $purchase->payment_method }}',
                accounts: JSON.parse('{!! $accounts !!}'),
                accountId: '',
                purchaseId: '{{ $purchase->id }}',
                netto: '{{ $purchase->netto }}',
                remainingPay: '{{ $purchase->netto-$purchase->pay_amount }}',
                
                suppliers: [],
                cart: [],
                note: '',
                products: JSON.parse(String.raw `{!! $selected_products !!}`),
               
                checkedProducts: [],
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
                
                   console.log('{{ $purchase->netto-$purchase->pay_amount }}')
                    vm.loading = true;
                    axios.post('/purchase-return', {
                            date: vm.date,
                            supplier_id: vm.suppliersId,
                            account_id: vm.paymentMethod=='hutang'?"3":vm.accountId,
                            purchase_id: vm.purchaseId,
                            payment_method: vm.paymentMethod,
                            amount: vm.payAmount,
                            note: vm.note,
                            total_return_quantity:vm.totalReturnQuantity,
                            total_return_amount:vm.totalReturnNominal,
                            products:vm.products,
                            remaining_pay:vm.remainingPay,
                            

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
                                    window.location.href = '/central-purchase';
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
                increaseProductQuantity: function(product) {
                    product.return_quantity = Number(product.return_quantity) + 1;
                },
                reduceProductQuantity: function(product) {
                    if (product.return_quantity > 1) {
                        product.return_quantity = Number(product.return_quantity )- 1;
                    }
                },
                currencyFormat: function(number) {
                    return Intl.NumberFormat('de-DE').format(number);
                },
                isChecked: function(id) {
                    const index = this.checkedProducts.indexOf(id);
                    if (index > -1) {
                        return true;
                    }
                    return false;
                },
                validateReturnQuantity: function(product) {
                    console.log('validating');
                    let returnQuantity = Number(product.return_quantity);
                    let quantity = Number(product.pivot.quantity)
                    if (returnQuantity > quantity) {
                        console.log('greater than quantity');
                        product.return_quantity = product.pivot.quantity;
                    }

                    if (returnQuantity < 0) {
                        product.return_quantity = 1;
                    }
                    // return;
                }
            },
            computed: {
                accountOptions: function() {
                    let vm = this;
                    if (this.paymentMethod !== '') {
                        return this.accounts.filter(account => account.type == vm.paymentMethod);
                    }

                    return this.accounts;
                },
                selectedProducts: function() {
                    let vm = this;
                    let selectedProducts = this.products.filter(product => vm.checkedProducts.indexOf(product.id) > -1);
                    return selectedProducts;
                },
                totalReturnQuantity: function() {
                    let totalReturnQuantity = this.selectedProducts.map(product => Number(product.return_quantity)).reduce((acc, cur) => {
                        return acc + cur;
                    }, 0)

                    return totalReturnQuantity;
                },
                totalReturnNominal: function() {
                    let totalReturnNominal = this.selectedProducts.map(product => Number(product.pivot.price) * Number(product.return_quantity)).reduce((acc, cur) => {
                        return acc + cur;
                    }, 0)

                    return totalReturnNominal;
                }
            }
        })
    </script>
    @endsection