@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <div class="nk-block-head-sub"><a class="back-to" href="/central-purchase"><em class="icon ni ni-arrow-left"></em><span>Pembelian Barang</span></a></div>
        <h2 class="nk-block-title fw-normal">Pembayaran Supplier</h2>
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
                                <h6 class="title">Informasi Pembelian</h6>
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
                                <em class="icon ni ni-alert-circle"></em> Informasi yang tercantum merupakan informasi ketika pembelian
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
                            <div class="card bg-light">
                            <!-- <div class="card-header">Header</div> -->
                            <div class="card-inner">
                                <h5 class="card-title">Summary</h5>
                              

                               
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Subtotal</p>
                                    <p class="col-md-6 text-right card-text mb-0" id="checkedPayAmount" ><strong>0</strong></p>
                                </div>
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Jumlah Bayar</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>@{{payAmount}}</strong></p>
                                </div>
                                <hr>
                                
                                <div class="row justify-content-between">
                                    <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                                    <p class="col-md-6 text-right card-text mb-0"><strong>{{ number_format($payRemaining) }}</strong></p>
                                </div>
                               
                                <!-- <div class="row justify-content-between">
                                        <p class="col-md-6 card-text mb-0">Sisa Pembayaran</p>
                                        <p class="col-md-6 text-right card-text mb-0"><strong>@{{ currencyFormat(changePayment) }}</strong></p>
                                    </div> -->
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
                        <button  class="btn btn-primary" type="submit" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Simpan</span>
                        </button>
                       
                    </div>
                </div>
            </form>
        </div>
        
    </div><!-- .nk-block -->
    <br>
    <br>
    <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="supplierCentralPurchases">
                        <thead>
                            <tr >
                                <th >Id</th>
                                <th>Tanggal Order</th>
                                <th>Nomor Order</th>
                                <th>Net Total</th>
                                <th>Jumlah Bayar</th>
                                <th>Sisa bayar</th>
                                <th></th>
                               
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endsection
    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    @endsection
    @section('pagescript')
    <script>
    //varible global
    var centralPurchase;
    var centralPurchaseSelected;
    var checkedPayAmount;

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
               
                date: '{{ date("Y-m-d") }}',
                payAmount: 0,
                suppliersId:   '{{ $supplier_id }}',
                shippingCost: '',
                discount: '',
                isPaid: false,
                paymentMethod: '',
                accounts: JSON.parse('{!! $accounts !!}'),
                accountId: '',
                purchaseId: '',
                netto: '',
                suppliers: [],
                cart: [],
                note: '',
                selectedProducts: '',
                transactions: '',
                centralPurchaseSelected:centralPurchase,
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

                  this.payCalculated();
                },
                sendData: function() {
                    // console.log('submitted');
                    let vm = this;
                    vm.loading = true;
                    axios.post('/supplier/purchase-transactions', {
                            date: vm.date,
                            supplier_id: vm.suppliersId,
                            account_id: vm.accountId,
                            purchase_id: vm.purchaseId,
                            payment_method: vm.paymentMethod,
                            amount: vm.payAmount,
                            note: vm.note,
                            central_purchase_selected:centralPurchaseSelected
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
                                   // window.location.href = '/central-purchase';
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
                // totalpayAmount:function(){
                   
                // },
                validatePayAmount: function() {
                   
                    const payAmount = this.payAmount.replaceAll(".", "");
                    const checkedPayAmount=$('#checkedPayAmount').text().replaceAll(".", "");
                    
                    if (payAmount > Number(checkedPayAmount)) {
                        this.payAmount = checkedPayAmount;
                    }
                },

                payCalculated:function(){
                    let vm=this;
                    var payAmount=vm.payAmount.replaceAll(".", "");
                    centralPurchaseSelected=[];

                    console.log(payAmount);
                    centralPurchase.map((value,index)=>{ 
                    if (payAmount>0){
                        if (Number(payAmount) > Number(value.payRemaining)){
                          
                        var data={id:value.id,amount:Number(value.payRemaining)}
                        payAmount=payAmount-Number(value.payRemaining);
                        centralPurchaseSelected.push(data);
                    }else if (Number(payAmount) <= Number(value.payRemaining)){
                        var data={id:value.id,amount:payAmount}
                        payAmount=0;
                        centralPurchaseSelected.push(data);
                    }

                    }
                    if (index+1>=centralPurchase.length){
                        this.sendData()
                    }      
                    
                

                    })

                }
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
                // totalPayments: function() {
                //     return this.transactions.map(transaction => Number(transaction.pivot.amount)).reduce((acc, cur) => {
                //         return acc + cur;
                //     }, 0);
                //     // return this.transactions;
                //     // return totalPayments;
                // }
            }
        })
    </script>


<script>
//global variable


$(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#supplierCentralPurchases', {
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/suppliers/pay/'+<?php echo $supplier_id ?>,
                type: 'GET',
                // length: 2,
            },
            columns: [
                {
                data: 'id',
                    name: 'id'
                },
                {
                data: 'date',
                    name: 'date'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'netto',
                    name: 'netto'
                },
                {
                    data: 'payAmount',
                    name: 'payAmount'
                },
                {
                    data: 'remainingAmount',
                    name: 'remainingAmount'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                
               

            ]
        });
        $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();
        $('#supplierCentralPurchases').on('click', 'tr .checked-central-purchase', function(e) {
        // e.preventDefault();

        
        var subtotal=0;
        checkedPayAmount=0;
        centralPurchase=[];
        $('#supplierCentralPurchases tr').each(function(){
            var checked = $(this).find('td:nth-child(7) input:checked').val();
            var id = $(this).find('td:nth-child(1)').text();
            var netto = $(this).find('td:nth-child(4)').text().replace(/[^\w\s]/gi, '');
            var payTotal = $(this).find('td:nth-child(5)').text().replace(/[^\w\s]/gi, '');
            var payRemaining = $(this).find('td:nth-child(6)').text().replace(/[^\w\s]/gi, '');
    
            if (checked=="true"){
                subtotal=Number(subtotal)+Number(payRemaining); 
                checkedPayAmount=Number(subtotal)+Number(payRemaining);     
                var data={id:id,netto:netto,payTotal:payTotal,payRemaining:payRemaining}
                centralPurchase.push(data);
            }       
            })
        $('#checkedPayAmount').text(subtotal.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1."))
      
        })
    });
</script>
@endsection