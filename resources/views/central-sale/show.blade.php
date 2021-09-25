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
                            @if($centralSale->status == 'approved')
                            <em class="icon ni ni-check-circle-fill text-success"></em>
                            @elseif($centralSale->status == 'rejected')
                            <em class="icon ni ni-cross-circle-fill text-danger"></em>
                            @else
                            <em class="icon ni ni-clock-fill text-warning"></em>
                            @endif
                            Penjualan No. <strong class="text-primary small">#{{ $centralSale->code }}</strong>
                        </h3>
                        <div class="nk-block-des text-soft">
                            <ul class="list-inline">
                                <li>Created At: <span class="text-base">{{ \Carbon\Carbon::parse($centralSale->created_at)->isoFormat('LLL') }}</span></li>
                                <li>Created By:
                                    @if($centralSale->createdBy !== null)
                                    <span class="text-base">{{ $centralSale->createdBy->name }}</span>
                                    @else
                                    <span class="text-base">-</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="/central-sale" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                        <a href="html/invoice-list.html" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <div class="invoice">
                    <div class="invoice-action">
                        <a class="btn btn-icon btn-lg btn-white btn-dim btn-outline-primary" href="/central-sale/print/{{ $centralSale->id }}" target="_blank"><em class="icon ni ni-printer-fill"></em></a>
                    </div><!-- .invoice-actions -->
                    <div class="invoice-wrap">
                        <div class="invoice-brand text-center">
                            <img src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="">
                        </div>
                        <div class="invoice-head">
                            <div class="invoice-contact">
                                <!-- <span class="overline-title">Invoice To</span> -->
                                <div class="invoice-contact-info">
                                    @if($centralSale->customer !== null)
                                    <h4 class="title">{{ $centralSale->customer->name }}</h4>
                                    <ul class="list-plain">
                                        <li><em class="icon ni ni-map-pin-fill"></em><span>{{ $centralSale->customer->address }}</span></li>
                                        <li><em class="icon ni ni-call-fill"></em><span>{{ $centralSale->customer->handphone }}</span></li>
                                    </ul>
                                    @else
                                    <h4 class="title text-danger">Deleted Customer</h4>
                                    @endif
                                </div>
                            </div>
                            <div class="invoice-desc">
                                <!-- <h3 class="title">Invoice</h3> -->
                                <ul class="list-plain">
                                    <li class="invoice-id"><span>Sale ID</span>:<span>{{ $centralSale->code }}</span></li>
                                    <li class="invoice-date"><span>Date</span>:<span>{{ \Carbon\Carbon::parse($centralSale->date)->isoFormat('L') }}</span></li>
                                    @if($centralSale->shipment !== null)
                                    <li class="invoice-date"><span>Shipment</span>:<span>{{ $centralSale->shipment->name }}</span></li>
                                    @endif
                                    <li class="invoice-date"><span>Penerima</span>:<span>{{ $centralSale->recipient }}</span></li>
                                    <!-- <li class="invoice-date"><span>Alamat Penerima</span>:<span>26 Jan, 2020</span></li> -->
                                    <li class="invoice-date"><span>Due Date</span>:<span>{{ \Carbon\Carbon::parse($centralSale->due_date)->isoFormat('L') }}</span></li>
                                </ul>
                            </div>
                        </div><!-- .invoice-head -->
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
                                        @foreach($centralSale->products as $product)
                                        <tr>
                                            <td>{{ $product->code }}</td>
                                            <td>
                                                {{ $product->name }}
                                            </td>
                                            <td>{{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                                            <td>{{ $product->pivot->quantity }}</td>
                                            <td>{{ $product->pivot->free }}</td>
                                            <td>{{ number_format($product->pivot->amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Total Amount</td>
                                            <td>{{ number_format($centralSale->total_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Diskon</td>
                                            <td>
                                                @if($centralSale->discount !== null)
                                                @if($centralSale->discount_type == 'nominal')
                                                {{ number_format(0 - $centralSale->discount, 0, ',', '.') }}
                                                @else
                                                @php
                                                $discount = $centralSale->total_cost * ($centralSale->discount / 100)
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
                                            <td>{{ number_format($centralSale->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Biaya Kirim</td>
                                            <td>{{ number_format($centralSale->shipping_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">
                                                Biaya Lainnya
                                                @if($centralSale->detail_other_cost !== null)
                                                {{ '(' . $centralSale->detail_other_cost . ')' }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($centralSale->other_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Deposit Pelanggan</td>
                                            <td>{{ number_format($centralSale->customer_deposit, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="3">Total</td>
                                            <td>{{ number_format($centralSale->net_total, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <!-- <div class="nk-notes ff-italic fs-12px text-soft"> Invoice was created on a computer and is valid without the signature and seal. </div> -->
                            </div>
                        </div><!-- .invoice-bills -->
                    </div><!-- .invoice-wrap -->
                </div><!-- .invoice -->
            </div><!-- .nk-block -->
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{$centralSale->code}}',
            date: '{{$centralSale->date}}',
            shipmentId: '{{$centralSale->shipment_id}}',
            customerId: '{{$centralSale->customer_id}}',
            total_weight: '{{$centralSale->total_weight}}',
            total_cost: '{{$centralSale->total_cost}}',
            subtotal: '{{$centralSale->subtotal}}',
            net_total: '{{$centralSale->net_total}}',
            debt: '{{$centralSale->debt}}',
            discount: '{{$centralSale->discount}}',
            shipping_cost: '{{$centralSale->shipping_cost}}',
            other_cost: '{{$centralSale->other_cost}}',
            detail_other_cost: '{{$centralSale->detail_other_cost}}',
            deposit_customer: '{{$centralSale->deposit_customer}}',
            receipt_1: '{{$centralSale->receipt_1}}',
            receive_1: '{{$centralSale->receive_1}}',
            receipt_2: '{{$centralSale->receipt_2}}',
            receive_2: '{{$centralSale->receive_2}}',
            payment_amount: '{{$centralSale->payment_amount}}',
            remaining_payment: '{{$centralSale->remaining_payment}}',
            recipient: '{{$centralSale->recipient}}',
            address_recipient: '{{$centralSale->address_recipient}}',
            detail: '{{$centralSale->detail}}',
            status: '{{$centralSale->status}}',
        }
    })
</script>

@endsection