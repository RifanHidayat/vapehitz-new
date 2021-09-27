@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head">
                <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">
                            Penjualan No. <strong class="text-primary small">#{{ $sale->code }}</strong>
                        </h3>
                        <div class="nk-block-des text-soft">
                            <ul class="list-inline">
                                <li>Created At: <span class="text-base">{{ \Carbon\Carbon::parse($sale->created_at)->isoFormat('LLL') }}</span></li>
                                <li>Created By:
                                    @if($sale->createdBy !== null)
                                    <span class="text-base">{{ $sale->createdBy->name }}</span>
                                    @else
                                    <span class="text-base">-</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="/retail-sale" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        <a href="html/invoice-list.html" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <div class="invoice">
                    <div class="invoice-action">
                        <a class="btn btn-icon btn-lg btn-white btn-dim btn-outline-primary" href="/retail-sale/print/{{ $sale->id }}" target="_blank"><em class="icon ni ni-printer-fill"></em></a>
                    </div><!-- .invoice-actions -->
                    <div class="invoice-wrap">
                        <div class="row py-3">
                            <div class="col-md-6">
                                <strong class="text-soft">TANGGAL : </strong>
                                <strong>{{ $sale->date }}</strong>
                            </div>
                        </div>
                        <div class="invoice-bills">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="w-150px">Item ID</th>
                                            <th class="w-60">Description</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Free</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalAmount = 0; ?>
                                        @foreach($sale->products as $product)
                                        <tr>
                                            <td>{{ $product->code }}</td>
                                            <td>
                                                {{ $product->name }}
                                            </td>
                                            <td>{{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                                            <td>{{ $product->pivot->quantity }}</td>
                                            <td>{{ $product->pivot->free }}</td>
                                            <?php $amount = $product->pivot->quantity * $product->pivot->price ?>
                                            <td>{{ number_format($amount, 0, ',', '.') }}</td>
                                        </tr>
                                        <?php $totalAmount += $amount; ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Total Amount</td>
                                            <td>{{ number_format($totalAmount, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Diskon</td>
                                            <td>
                                                @if($sale->discount !== null)
                                                @if($sale->discount_type == 'nominal')
                                                {{ number_format(0 - $sale->discount, 0, ',', '.') }}
                                                @else
                                                @php
                                                $discount = $sale->total_cost * ($sale->discount / 100)
                                                @endphp
                                                {{ number_format(0 - $discount, 0, ',', '.') }}
                                                @endif
                                                @else
                                                0
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Subtotal</td>
                                            <td>{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Biaya Kirim</td>
                                            <td>{{ number_format($sale->shipping_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">
                                                Biaya Lainnya
                                                @if($sale->detail_other_cost !== null)
                                                {{ '(' . $sale->detail_other_cost . ')' }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($sale->other_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Deposit Pelanggan</td>
                                            <td>{{ number_format($sale->customer_deposit, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Total</td>
                                            <td>{{ number_format($sale->net_total, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <!-- <div class="nk-notes ff-italic fs-12px text-soft"> Invoice was created on a computer and is valid without the signature and seal. </div> -->
                            </div>
                        </div><!-- .invoice-bills -->
                    </div><!-- .invoice-wrap -->
                </div><!-- .invoice -->
            </div><!-- .nk-block -->
            <div class="row mt-4">
                <div class="col-lg-6 col-sm-12">
                    <div class="card card-bordered">
                        <div class="card-inner-group">
                            <div class="card-inner card-inner-md">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h6 class="title">Riwayat Pembayaran</h6>
                                    </div>
                                </div>
                            </div>
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
                                                <td><a href="/retail-sale-transaction/show/{{ $transaction->id }}" target="_blank">{{ $transaction->code }}</a></td>
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
                                                <td colspan="3" style="border-top: none;">Total Penjualan</td>
                                                <td class="text-right" style="border-top: none;">{{ number_format($sale->net_total) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold;">
                                                <td colspan="3">Sisa Hutang</td>
                                                <td class="text-right">{{ number_format(abs($subTotal - $sale->net_total)) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card card-bordered">
                        <div class="card-inner-group">
                            <div class="card-inner card-inner-md">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h6 class="title">Riwayat Retur</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-inner">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Kode Retur</th>
                                                <th>Metode</th>
                                                <th class="text-right">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($returns as $return)
                                            <tr>
                                                <td>{{ $return->date }}</td>
                                                <td><a href="/retail-sale-return/show/{{ $return->id }}" target="_blank">{{ $return->code }}</a></td>
                                                <td class="text-capitalize">{{ $return->payment_method }}</td>
                                                <td class="text-right">{{ $return->quantity }}</td>
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
        </div>
    </div>
</div>
@endsection
@section('pagescript')

@endsection