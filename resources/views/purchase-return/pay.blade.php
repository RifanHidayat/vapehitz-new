@extends('layouts.app')

@section('title', 'Vapehitz')

@section('pagestyle')

@endsection

@section('content')

<div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Retur Pembelian</h3>
            <div class="nk-block-des text-soft">
                <ul class="list-inline">
                    <li>Nomor Retur: <span class="text-base">{{$purchaseReturn->code}}</span></li>
                    <li>Nomor Order: <span class="text-base">{{$purchaseReturn->centralPurchase->code}}</span></li>
        
                </ul>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="/purchase-return" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
    </div>


<!-- .nk-block -->
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
    <div class="col-lg-6 col-md-12">

                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Detail retur</h6>
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
                        <div class="card card-bordered">
                <ul class="data-list is-compact">
                <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Tanggal retur</div>
                            <div class="data-value">{{ $purchaseReturn->date }}</div>
                        </div>
                    </li>
                    <!-- <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Nomor retur</div>
                            <div class="data-value"></div>
                        </div>
                    </li>
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Nomor Order</div>
                            <div class="data-value"></div>
                        </div>
                    </li> -->
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Nama Suppiler</div>
                            <div class="data-value">{{ $purchaseReturn->supplier->name }}</div>
                        </div>
                    </li>
                
                    <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Sisa Hutang</div>
                            <div class="data-value">{{number_format($purchaseReturn->centralPurchase->netto-$payAmount)}}</div>
                        </div>
                    </li>
                    
                </ul>
            </div>
                       
                        </div>
                    </div>
                    
                </div>
            
            
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Riwayat pembayaran retur</h6>
                                </div>
                           
                                
                            </div>
                        </div><!-- .card-inner -->
                        <div class="card-inner">
                        <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th>Metode pembayaran</th>
                                        <th class="text-right">Jumlah bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subTotal = 0; $remainingPay=0; @endphp
                                    @foreach($transactions as $transaction)
                                    
                                    <tr>
                                        <td>{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                                        <td><a href="/purchase-return-transaction/show/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td class="text-right">{{ number_format($transaction->amount) }}</td>
                                    </tr>
                                    @php $subTotal += $transaction->amount; $remainingPay=$subTotal - $purchaseReturn->amount; @endphp
                                
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">Subtotal</td>
                                        <td class="text-right">{{ number_format($subTotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-top: none;">Total Retur</td>
                                        <td class="text-right" style="border-top: none;">{{ number_format($purchaseReturn->amount) }}</td>
                                    </tr>
                                    <tr style="font-weight: bold;">
                                        <td colspan="3">Sisa Pembayaran Retur</td>
                                        <td class="text-right">{{ number_format(abs($subTotal - $purchaseReturn->amount)) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        
                        </div>
                    </div>
                    
                </div>
          
            
        </div>
        
        <div class="col-lg-6 col-md-12">
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
                                    <p class="amount" ><strong>{{$purchaseReturn->quantity}}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-coin mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Nominal Retur</span>
                                    <p class="amount" ><strong>{{number_format($purchaseReturn->amount)}}</strong></p>
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
                                    <input type="text" v-model="payAmount" @input="validatePayAmount"  v-cleave="cleaveCurrency" @input class="form-control text-right" placeholder="Jumlah Bayar">
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
                   
                        <button v-if="Math.abs(totalPayments - totalReturn) >0"  class="btn btn-primary" type="submit" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Simpan</span>
                            
                        </button>
                        <span v-else class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span>
                        <!-- <span v-else class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span> -->
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-6 col-md-12">
           
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
              
                date: '{{ date("Y-m-d") }}',
                payAmount: 0,
                suppliersId: '{{ $purchaseReturn->supplier->id }}',
                purchaseReturnId: '{{ $purchaseReturn->id }}',
                shippingCost: '',       
                isPaid: false,
                paymentMethod: '',
                accounts: JSON.parse('{!! $accounts !!}'),
                accountId: '',
                debt:'{{$purchaseReturn->centralPurchase->netto-$payAmount}}',
                netto: '',
                totalReturn:'{{$purchaseReturn->amount}}',
                suppliers: [],
                cart: [],
                note: '',
                selectedProducts: '',
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
                    let vm=this;
                    console.log(vm.purchaseId)
                    console.log("Tanggal pembayaran",vm.date)
                    console.log("Note",vm.note)
                    console.log("Account Id",vm.accountId)
                    console.log("pembayran",vm.payAmount)
                    console.log("purchase id",vm.suppliersId)
                    console.log("supplier  id",vm.purchaseId)

        
                    vm.loading = true;
                    axios.post('/purchase-return-transaction', {
                            date: vm.date,
                            supplier_id: vm.suppliersId,
                            purchase_return_id:vm.purchaseReturnId,
                            account_id: vm.accountId,
                            purchase_id: vm.purchaseId,
                            payment_method: vm.paymentMethod,
                            amount: vm.payAmount,
                            note: vm.note,
                            debt:vm.debt
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
                                    //window.location.href = '/central-purchase';
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
                    const totalReturn = Number(this.totalReturn);
                    if (payAmount > totalReturn) {
                        this.payAmount = totalReturn - this.totalPayments;
                    }
                    
                },
            },
            computed: {
                accountOptions: function() {
                    let vm = this;
                    if (this.paymentMethod !== '') {
                        return this.accounts.filter(account => account.type == vm.paymentMethod);
                    }

                    return this.accounts;
                },
                totalPayments: function() {
                    return this.transactions.map(transaction => Number(transaction.amount)).reduce((acc, cur) => {
                        return acc + cur;
                    }, 0);
                    // return this.transactions;
                    // return totalPayments;
                }
                // selectedProducts: function() {
                //     let vm = this;
                //     let selectedProducts = this.products.filter(product => vm.checkedProducts.indexOf(product.id) > -1);
                //     return selectedProducts;
                // },
                // totalReturnQuantity: function() {
                //     let totalReturnQuantity = this.selectedProducts.map(product => Number(product.return_quantity)).reduce((acc, cur) => {
                //         return acc + cur;
                //     }, 0)

                //     return totalReturnQuantity;
                // },
                // totalReturnNominal: function() {
                //     let totalReturnNominal = this.selectedProducts.map(product => Number(product.pivot.price) * Number(product.return_quantity)).reduce((acc, cur) => {
                //         return acc + cur;
                //     }, 0)

                //     return totalReturnNominal;
                // }
            }
        })
    </script>
        @endsection
  