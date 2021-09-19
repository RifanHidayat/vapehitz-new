@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/central-purchase"><em class="icon ni ni-arrow-left"></em><span>Pembelian Barang</span></a></div>
        <h2 class="nk-block-title fw-normal">Edit Data Pembelian Barang</h2>
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
                                <h6 class="title">Daftar Pembelian</h6>
                            </div>
                            <div class="card-tools mr-n1">
                                <ul class="btn-toolbar gx-1">
                                    <li>
                                        <a class="btn btn-icon btn-trigger" data-toggle="modal" href="#exampleModal" data-backdrop="static" data-keyboard="false"><em class="icon ni ni-plus"></em></a>
                                    </li>
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-right" style="">
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
                    </div><!-- .card-inner -->
                    <div class="card-inner">
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
                            <p class="mt-3">Belum ada barang yang dipilih</p>
                        </div>
                        <div v-for="(product, index) in selectedProducts" :key="index" class="card card-bordered">
                            <div class="card-inner">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-10">
                                        <h5 class="card-title">@{{ product.name }}</h5>
                                        <div class="row justify-content-between">
                                            <p class="col-md-6 mb-0">Kode Barang</p>
                                            <p class="col-md-6 text-right mb-0"><strong>@{{ product.code }}</strong></p>
                                        </div>
                                        <div class="row justify-content-between">
                                            <p class="col-md-6 mb-0">Stok Gudang</p>
                                            <p class="col-md-6 text-right mb-0"><strong>@{{ product.central_stock }}</strong></p>
                                        </div>
                                        <div class="row justify-content-between align-items-center mt-3">
                                            <p class="col-md-6 mb-0">Harga Beli</p>
                                            <div class="col-md-6">
                                                <!-- <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="default-05" placeholder="Input placeholder">
                                                        <div class="form-text-hint">
                                                            <span class="overline-title">IDR</span>
                                                        </div>
                                                    </div> -->
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-left">
                                                        <!-- <em class="icon ni ni-user"></em> -->
                                                        <span>Rp</span>
                                                    </div>
                                                    <input type="text" v-model="product.purchase_price"  class="form-control text-right" placeholder="Harga">
                                                </div>
                                            </div>
                                            <!-- <p class="col-md-6 text-right mb-0"><strong>{{ number_format(120000) }}</strong></p> -->
                                        </div>
                                        <div class="row justify-content-between align-items-center mt-3">
                                            <p class="col-md-6 mb-0">Quantity</p>
                                            <div class="col-md-6">
                                                <!-- <input type="text" class="form-control form-control-sm text-right"> -->
                                                <div class="form-control-wrap number-spinner-wrap">
                                                    <button type="button" @click="reduceProductQuantity(product)" class="btn btn-icon btn-outline-light number-spinner-btn number-minus" :disabled="product.quantity === 1"><em class="icon ni ni-minus"></em></button>
                                                    <input type="number" v-model="product.quantity" class="form-control number-spinner" value="0">
                                                    <button type="button" @click="increaseProductQuantity(product)" class="btn btn-icon btn-outline-light number-spinner-btn number-plus"><em class="icon ni ni-plus"></em></button>
                                                </div>
                                            </div>
                                            <!-- <p class="col-md-6 text-right mb-0"><strong>{{ number_format(120000) }}</strong></p> -->
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a href="#" @click.prevent="removeSelectedProduct(index)" class="btn btn-icon btn-trigger text-danger"><em class="icon ni ni-trash"></em></a>
                                    </div>
                                </div>
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                </div><!-- .card-inner-group -->
            </div>
        </div>
        <div class="col-lg-5 col-md-12">
            <form @submit.prevent="submitForm">

            <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Invoice Supplier</h6>
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
                                <label class="form-label" for="full-name-1">Tanggal Invoice</label>
                                <div class="form-control-wrap">
                                    <input type="date" v-model="invoiceDate" class="form-control">
                                </div>
                            </div>
                            <div class="form-group ">
                                    <label class="form-label" for="full-name-1">Nomor Invoice</label>
                                    <div class="form-control-wrap">
                                        
                                        <input type="text" v-model="invoiceNumber"  class="form-control text-left" >
                                    </div>
                                </div>
                          
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
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
                    
                </div>
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Informasi Pembelian</h6>
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
                                <label class="form-label" for="full-name-1">Tanggal Order</label>
                                <div class="form-control-wrap">
                                    <input type="date" v-model="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Nama Supplier</label>
                                <div class="form-control-wrap">
                                    <select v-model="suppliersId" class="form-control" id="supplier">
                                        <option v-for="supplier in suppliers" :value="supplier.id">@{{supplier.name}}</option>
                                    </select>
                                    <!-- <div class="input-group mb-3">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-light" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                                <em class="fas fa-question"></em>
                                            </button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                                </div>
                            </div>

                            <!-- <div class="divider"></div> -->
                            <div class="form-row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Biaya Kirim</label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <!-- <em class="icon ni ni-user"></em> -->
                                            <span>Rp</span>
                                        </div>
                                        <input type="text" v-model="shippingCost" v-cleave="cleaveCurrency" class="form-control text-right" placeholder="Biaya Kirim">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Diskon</label>
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <!-- <em class="icon ni ni-user"></em> -->
                                            <span>Rp</span>
                                        </div>
                                        <input type="text" v-model="discount" v-cleave="cleaveCurrency" class="form-control text-right" placeholder="Diskon">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" v-model="isPaid" class="custom-control-input" id="customCheck1">
                                    <label class="custom-control-label" for="customCheck1">Bayar Lunas</label>
                                </div>
                            </div>
                            <div v-if="isPaid" class="form-row">
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Cara Pembayaran</label>
                                    <div class="form-control-wrap">
                                        <select v-model="paymentMethod" class="form-control">
                                            <option value="transfer">Transfer</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-12">
                                    <label class="form-label" for="full-name-1">Akun</label>
                                    <div class="form-control-wrap">
                                        <select v-model="accountId" class="form-control">
                                            <option v-for="(account, index) in accountOptions" :value="account.id">@{{ account.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="card bg-light">
                                <!-- <div class="card-header">Header</div> -->
                                <div class="card-inner">
                                    <h5 class="card-title">Summary</h5>
                                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Subtotal</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(subTotal) }}</strong></p>
                                    </div>
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Biaya Kirim</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ shippingCost }}</strong></p>
                                    </div>
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Diskon</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ discount }}</strong></p>
                                    </div>
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Net Total</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(netTotal) }}</strong></p>
                                    </div>
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Jumlah Bayar</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(payment) }}</strong></p>
                                    </div>
                                    <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(changePayment) }}</strong></p>
                                    </div>
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Barang</h5>
                    <div>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                    </div>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#cartModal">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-cart"></em>&nbsp;<span v-if="cart.length > 0" class="badge badge-pill badge-light">@{{ cart.length }}</span>
                            </div>
                        </button>
                    </div>
                    <div>
                        <table class="table" id="products-table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nama</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <button @click="moveFromCart" type="button" class="btn btn-primary" data-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Keranjang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p v-if="cart.length === 0" class="text-soft text-center">Belum ada barang</p>
                <ul class="list-group">
                    <li v-for="(product, index) in cart" class="list-group-item">
                        <div class="row">
                            <div class="col-sm-10">@{{ product.name }}</div>
                            <div class="col-sm-2 text-right">
                                <a href="#" @click.prevent="removeFromCart(index)" class="text-danger"><em class="fas fa-times"></em></a>
                            </div>
                        </div>
                    </li>
                </ul>
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
            code: '{{$central_purchases->code}}',
            date: '{{$central_purchases->date}}',
            suppliersId: '{{$central_purchases->supplier_id}}',
            shippingCost: '{{$central_purchases->shipping_cost}}',
            discount: '{{$central_purchases->discount}}',
            isPaid: false,
            paymentMethod: '{{$central_purchases->payment_method}}',
            accounts: JSON.parse('{!! $accounts !!}'),
            accountId: '',
            suppliers: JSON.parse('{!! $suppliers !!}'),
            cart: [],
            selectedProducts: JSON.parse('{!! $central_purchases->products !!}'),
            invoiceNumber:'',
            invoiceDate:'',
            cleaveCurrency: {
                delimiter: '.',
                numeralDecimalMark: ',',
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            },
            loading: false,
        },
        methods: {
            submitForm: function() {
                this.sendData();
                // console.log(this.invoiceDate);
                // console.log(this.invoiceNumber);
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.patch('/central-purchase/{{$central_purchases->id}}', {
                        code: vm.code,
                        date: vm.date,
                        supplier_id: vm.suppliersId,
                        account_id: vm.accountId,
                        total: vm.subTotal,
                        shipping_cost: vm.shippingCost,
                        discount: vm.discount,
                        netto: vm.netTotal,
                        pay_amount: vm.payment,
                        payment_method: vm.paymentMethod,
                        selected_products: vm.selectedProducts,
                        invoice_number:vm.invoiceNumber,
                        invoice_date:vm.invoiceDate

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
            removeFromCart: function(index) {
                this.cart.splice(index, 1);
            },
            moveFromCart: function() {
                const selectedProductIds = this.selectedProducts.map(product => product.id);
                const productsInCart = this.cart.filter(product => selectedProductIds.indexOf(product.id) < 0);
                this.cart.filter(product => selectedProductIds.indexOf(product.id) > -1)
                    .map(product => product.id)
                    .forEach(productId => {
                        const index = selectedProductIds.findIndex((id) => id == productId);
                        this.selectedProducts[index].quantity += 1;
                    })
                // console.log(arr);

                this.selectedProducts = this.selectedProducts.concat(productsInCart);
                this.cart = [];
            },
            removeSelectedProduct: function(index) {
                this.selectedProducts.splice(index, 1);
            },
            removeAllSelectedProducts: function() {
                this.selectedProducts = [];
            },
            increaseProductQuantity: function(product) {
                product.quantity = product.quantity + 1;
            },
            reduceProductQuantity: function(product) {
                if (product.quantity > 1) {
                    product.quantity = product.quantity - 1;
                }
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
        },
        computed: {
            subTotal: function() {
                const subTotal = this.selectedProducts.map(product => {
                    const amount = Number(product.quantity) * this.clearCurrencyFormat(product.purchase_price.toString());
                    return amount;
                }).reduce((acc, cur) => {
                    return acc + cur;
                }, 0);

                return subTotal;
            },
            netTotal: function() {
                const netTotal = Number(this.subTotal) + Number(this.clearCurrencyFormat(this.shippingCost)) - Number(this.clearCurrencyFormat(this.discount));
                return netTotal;
            },
            payment: function() {
                if (this.isPaid) {
                    return this.netTotal;
                }

                return 0;
            },
            changePayment: function() {
                return this.netTotal - this.payment;
            },
            accountOptions: function() {
                let vm = this;
                if (this.paymentMethod !== '') {
                    return this.accounts.filter(account => account.type == vm.paymentMethod);
                }

                return this.accounts;
            }
        }
    })
</script>
<script>
    $(function() {
        const productsTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            "autoWidth": false,
            // pageLength: 2,
            ajax: {
                url: '/datatables/central-purchases/products',
                type: 'GET',
                // length: 2,
            },
            columns: [{
                    data: 'product_category.name',
                    name: 'productCategory.name',
                },
                {
                    data: 'name',
                    name: 'products.name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#products-table tbody').on('click', '.btn-choose', function() {
            const rowData = productsTable.row($(this).parents('tr')).data();
            const data = {
                ...rowData
            };

            const cart = app.$data.cart;
            const productIds = cart.map(product => product.id);

            // If product already in cart or selected products
            if (productIds.indexOf(data.id) < 0) {
                data['quantity'] = 1;
                cart.push(data);
            }

            // $('#estimationModal').modal('hide');

        });

        $("#supplier").select2({
            language: {
                noResults: function() {
                    const searchText = $("#supplier").data("select2").dropdown.$search.val();
                    return `
                        <a href="#" class="d-block" id="btn-add-supplier"><i class="fas fa-plus fa-sm"></i> Tambah ${searchText} </a>
                        <div class="progress mt-2" id="loadingSupplier" style="display: none">
                            <div class="progress-bar bg-primary w-100 progress-bar-striped progress-bar-animated" data-progress="100"></div>
                        </div>
                        `;
                },
            },
            escapeMarkup: function(markup) {
                return markup;
            },
        });
        $("#supplier").on('change', function() {
            app.$data.suppliersId = $(this).val();
            // console.log(searchText);
        });

        $(document).on('click', '#btn-add-supplier', function(e) {
            e.preventDefault();
            const searchText = $("#supplier").data("select2").dropdown.$search.val();
            const data = {
                name: searchText,
                status: 1,
            }

            addSupplier(data);
            // console.log('clicked');
        })

        function hideElement(el) {
            $(el).hide();
        }

        function showElement(el) {
            $(el).show();
        }

        function addSupplier(data) {
            showElement('#loadingSupplier');
            axios.post('/supplier', data)
                .then(function(response) {
                    const {
                        data
                    } = response.data;
                    app.$data.suppliers.push(data);
                    app.$data.suppliersId = data.id;
                    $('#supplier').val(data.id);
                    $('#supplier').select2('close');
                    hideElement('#loadingSupplier');
                })
                .catch(function(error) {
                    // vm.loading = false;
                    $('#supplier').select2('close');
                    hideElement('#loadingSupplier');
                    console.log(error);
                    Swal.fire(
                        'Terjadi Kesalahan',
                        'Gagal menambahkan supplier',
                        'error'
                    )
                });
        }
    })
</script>
@endsection