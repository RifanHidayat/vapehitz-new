<!-- sidebar @s -->
@php $user = Auth::user();@endphp
@php $permission = json_decode(Auth::user()->group->permission);@endphp

<?php
$sidebarClass = '';
$sidebarMenuClass = '';
if (isset($sidebar_class)) {
    if ($sidebar_class == 'compact') {
        $sidebarClass = 'is-compact';
        $sidebarMenuClass = 'compact-active';
    }
}
?>
<div class="nk-sidebar nk-sidebar-fixed is-dark {{ $sidebarClass }}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex {{ $sidebarMenuClass }}" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>

        </div>
        <div class="nk-sidebar-brand">
            <a href="{{url('/dashboard')}}" class="logo-link nk-sidebar-logo">
                <h4><b style="color: white;">VAPE</b><b style="color: #4ECDC4;">HITZ</b></h4>
                <!-- <img class="logo-light logo-img" src="{{ asset('/images/VH.png') }}" srcset="{{ asset('assets/images/logo2x.png') }} 2x" alt="logo"> -->
                <!-- <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png') }}" srcset="{{ asset('assets/images/logo-dark2x.png') }} 2x" alt="logo-dark"> -->
            </a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="/dashboard" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashlite"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li><!-- .nk-menu-item -->
                    @if(in_array("view_supplier",$permission) || in_array("view_customer",$permission) || in_array("view_product",$permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Master Data</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            @if(in_array("view_supplier", $permission))
                            <li class="nk-menu-item {{ request()->is('supplier*') ? 'active current-page' : '' }}">
                                <a href="{{url('/supplier')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Supplier</span></a>
                            </li>
                            @endif
                            @if(in_array("view_customer", $permission))
                            <li class="nk-menu-item {{ request()->is('customer*') ? 'active current-page' : '' }}">
                                <a href="{{url('/customer')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Customer</span></a>
                            </li>
                            @endif
                            @if(in_array("view_product", $permission))
                            <li class="nk-menu-item {{ request()->is('product*') ? 'active current-page' : '' }}">
                                <a href="{{url('/product')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Produk</span></a>
                            </li>
                            @endif
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    @endif
                    @if(in_array("view_purchase_product", $permission) || in_array("view_payment_supplier", $permission) || in_array("view_return_product_purchase", $permission) || in_array("view_product_payment", $permission) || in_array("view_product_sell", $permission) || in_array("view_customer_payment", $permission) || in_array("view_return_product_sell", $permission) || in_array("view_sell_return_settlement", $permission) || in_array("view_stock_opname", $permission) || in_array("view_badstock_release", $permission) || in_array("view_request_to_retail", $permission) || in_array("view_request_to_studio", $permission) || in_array("view_confirm_request", $permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
                            <span class="nk-menu-text">Transaksi Pusat</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            @if(in_array("view_purchase_product", $permission))
                            <li class="nk-menu-item {{ request()->is('central-purchase*') ? 'active current-page' : '' }}">
                                <a href="{{url('/central-purchase')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembelian Barang</span></a>
                            </li>
                            @endif
                            @if(in_array("view_payment_supplier", $permission))
                            <li class="nk-menu-item">
                                <a href="/purchase-transaction" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembayaran Supplier</span></a>
                            </li>
                            @endif
                            @if(in_array("view_return_product_purchase", $permission))
                            <li class="nk-menu-item">

                                <a href="/purchase-return" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Retur Barang Pembelian</span></a>
                            </li>
                            @endif
                            @if(in_array("view_product_payment", $permission))
                            <li class="nk-menu-item">
                                <a href="/purchase-return-transaction" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembayaran Retur</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            @if(in_array("view_product_sell", $permission))
                            <li class="nk-menu-item {{ request()->is('central-sale') || request()->is('central-sale/*') ? 'active current-page' : '' }}">
                                <a href="{{url('/central-sale')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penjualan Barang</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item {{ request()->is('central-sale-transaction') || request()->is('central-sale-transaction/*') ? 'active current-page' : '' }}">
                                <a href="/central-sale-transaction" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembayaran Pelanggan</span></a>
                            </li>
                            <li class="nk-menu-item {{ request()->is('central-sale-return') || request()->is('central-sale-return/*') ? 'active current-page' : '' }}">
                                <a href="/central-sale-return" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Retur Barang Penjualan</span></a>
                            </li>
                            <li class="nk-menu-item {{ request()->is('central-sale-return-transaction') || request()->is('central-sale-return-transaction/*') ? 'active current-page' : '' }}">
                                <a href="/central-sale-return-transaction" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembayaran Retur</span></a>
                            </li>
                            <li class="nk-menu-item">

                                &nbsp;
                            </li>
                            @if(in_array("view_stock_opname",$permission))
                            <li class="nk-menu-item {{ request()->is('stock-opname') || request()->is('stock-opname/*') ? 'active current-page' : '' }}">
                                <a href="{{url('/stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                            @endif
                            @if(in_array("view_badstock_release", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/badstock-release')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pengeluaran Badstok</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            @if(in_array("view_request_to_retail", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/request-to-retail')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan ke Retail</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                <a href="{{url('/request-to-studio')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan ke Studio</span></a>
                            </li>
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            @if(in_array("view_confirm_request", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/approve-central-retail')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Dari Retail</span></a>
                            </li>
                            @endif
                            @if(in_array("view_confirm_request", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/approve-central-studio')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Dari Studio</span></a>
                            </li>
                            @endif
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    @endif
                    @if(in_array("view_retail_sell",$permission) || in_array("view_request_to_central_retail", $permission) || in_array("view_return_retail_sell", $permission) || in_array("view_sop_retail", $permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Transaksi Retail</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            @if(in_array("view_confirm_request_retail", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/approve-retail')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Dari Pusat</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                <a href="{{url('/retail-request-to-central')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Ke Pusat</span></a>
                            </li>
                            @if(in_array("view_retail_sell", $permission))
                            <li class="nk-menu-item">
                                <a href="/retail-sale" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penjualan Barang</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                <a href="/retail-sale-return" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Retur Penjualan Barang</span></a>
                            </li>
                            @if(in_array("view_sop_retail", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/retail-stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                            @endif
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    @endif
                    @if(in_array("view_sop_studio", $permission) || in_array("view_studio_sell", $permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Transaksi Studio</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/approve-studio')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Dari Pusat</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/studio-request-to-central')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan Ke Pusat</span></a>
                            </li>
                            @if(in_array("view_studio_sell", $permission))
                            <li class="nk-menu-item">
                                <a href="/studio-sale" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penjualan Barang</span></a>
                            </li>
                            @endif
                            <li class="nk-menu-item">
                                <a href="/studio-sale-return" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Retur Penjualan Barang</span></a>
                            </li>
                            @if(in_array("view_sop_studio", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/studio-stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if(in_array("view_data_group", $permission) || in_array("view_data_user", $permission) || in_array("view_password_change", $permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">Administrator</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            @if(in_array("view_data_group", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/group')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Groups</span></a>
                            </li>
                            @endif
                            @if(in_array("view_data_user", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/user')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data User</span></a>
                            </li>
                            @endif
                            <!-- @if(in_array("view_password_change", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/user/edit/'.$user->id)}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Ganti Password</span></a>
                            </li>
                            @endif -->
                        </ul>
                    </li>
                    @endif
                    @if(in_array("view_account_finance", $permission) || in_array("view_cash_in_out_finance", $permission))
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                            <span class="nk-menu-text">Keuangan</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            @if(in_array("view_account_finance", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/account')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Akun</span></a>
                            </li>
                            @endif
                            @if(in_array("view_cash_in_out_finance", $permission))
                            <li class="nk-menu-item">
                                <a href="{{url('/account-transaction')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Cash in/out</span></a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <li class="nk-menu-item">
                        <a href="/report" class="nk-menu-link" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-reports"></em></span>
                            <span class="nk-menu-text">Laporan</span>
                        </a>
                    </li>
                    <!-- <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-tile-thumb"></em></span>
                            <span class="nk-menu-text">Product</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="/product" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Product</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="/product-category" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Category</span></a>
                            </li>
                        </ul>
                    </li> -->
                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
<!-- sidebar @e -->