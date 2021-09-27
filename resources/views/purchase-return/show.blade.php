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
                    <li>Tanggal Retur: <span class="text-base">{{$purchaseReturn->date}}</span></li>
        
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
    <div class="card card-bordered mb-4">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Supplier</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-layers mr-2" style="font-size: 2em;"></em>
                                <div class="info">
                                    <span class="title">Kode</span>
                                    <p class="amount" ><strong>{{$purchaseReturn->supplier->code}}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="far fa-building" style="font-size: 2em; margin-right:10px"></em>
                                <div class="info">
                                    <span class="title">Nama</span>
                                    <p class="amount" ><strong>{{$purchaseReturn->supplier->name}}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    

                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Detail Pembelian</h6>
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
                            <div class="data-label">Nomor Order</div>
                            <div class="data-value">{{$purchaseReturn->centralPurchase->code }}</div>
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
                            <div class="data-label">Tanggal Order</div>
                            <div class="data-value">{{ $purchaseReturn->centralPurchase->date }}</div>
                        </div>
                    </li>
                
                    <!-- <li class="data-item">
                        <div class="data-col">
                            <div class="data-label">Sisa Hutang</div>
                            <div class="data-value">{{number_format($purchaseReturn->centralPurchase->netto-$payAmount)}}</div>
                        </div>
                    </li> -->
                    
                </ul>
            </div>
                       
                        </div>
                    </div>
                    
                </div>

                <div class="card card-bordered h-100">
                <div class="card-inner-group">
                    <div class="card-inner card-inner-md">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Detail Produk</h6>
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
                    <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;" align="left">Nomor Produk</th>
                                        <th style="width: 20%;" align="left">Nama Produk</th>
                                        <td style="width: 15%;" align="left"><b>Alasan</b></td>
                                        <td style="width: 15%;" align="right"><b>Quantity</b></td>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                    @foreach($purchaseReturn->products as $product)
                                    <tr>
                                        <td class="text-left">{{ $product->code}}</td>
                                        <td class="text-left">{{ $product->name}}</td>
                                        <td class="text-left">{{ $product->pivot->cause=="defective"?
                                        "Barang Cacat / Rusak":
                                        "Barang Tidak Sesuai"
                                        }}</td>
                                        <td class="text-right">{{ $product->pivot->quantity}}</td>
                                    </tr>
                                   
                                    @endforeach
                                </tbody>
                               
                            </table>
  
                </div><!-- .card-inner-group -->
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
            <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner card-inner-md">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Riwayat Pembayaran Retur</h6>
                                </div>
                           
                                
                            </div>
                        </div><!-- .card-inner -->
                        <div class="card-inner">
                        <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th class="text-left">Metode pembayaran</th>
                                        <th class="text-right">Jumlah bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    @php $subTotal = 0; $remainingPay=0; @endphp
                                    @foreach($transactions as $transaction)
                                 
                                    <tr>
                                        <td>{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                                        <td><a href="/purchase-return-transaction/show/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
                                        <td class="text-left">{{ $transaction->payment_method}}</td>
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
  