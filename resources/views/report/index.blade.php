@extends('layouts.app')

@section('title', 'Vapehitz')
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
        <h2 class="nk-block-title fw-normal">Laporan</h2>
        <div class="nk-block-des">
            <p class="lead">List Laporan</p>
        </div>
    </div>
</div><!-- .nk-block -->
<div class="nk-block nk-block-lg">
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div>
                <h4>Sales</h4>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-sale/customer/detail">
                                        <p><strong>Central Sales By Customer Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-sale/customer/summary">
                                        <p><strong>Central Sales By Customer Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-sale/product/detail">
                                        <p><strong>Central Sales By Product Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-sale/product/summary">
                                        <p><strong>Central Sales By Product Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/retail-sale/detail">
                                        <p><strong>Retail Sales</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/studio-sale/detail">
                                        <p><strong>Studio Sales</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="mt-4">Purchase</h4>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-purchase/supplier/detail">
                                        <p><strong>Central Purchase By Supplier Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-purchase/supplier/summary">
                                        <p><strong>Central Purchase By Supplier Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-purchase/product/detail">
                                        <p><strong>Central Purchase By Product Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-purchase/product/summary">
                                        <p><strong>Central Purchase By Product Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div>
                <h4 class="mt-4">Supplier</h4>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                <a href="/report/central-purchase/supplier/detail">
                                        <p><strong>Central Purchase debt By Supplier Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                 
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/central-purchase/supplier/summary">
                                        <p><strong>Central Purchase debt By Supplier Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="mt-4">Customer</h4>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/customer/piutang/detail">
                                        <p><strong>Piutang Detail</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <div class="border py-1 px-3 round">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-9">
                                    <a href="/report/customer/piutang/summary">
                                        <p><strong>Piutang Summary</strong></p>
                                    </a>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a class="btn btn-icon btn-trigger"><em class="icon ni ni-star" style="font-size: 1.4rem;"></em></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .nk-block -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        methods: {

        }
    })
</script>
@endsection