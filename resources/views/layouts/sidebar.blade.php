<!-- sidebar @s -->
<div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="html/index.html" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png') }}" srcset="{{ asset('assets/images/logo2x.png') }} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png') }}" srcset="{{ asset('assets/images/logo-dark2x.png') }} 2x" alt="logo-dark">
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
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Master Data</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/supplier')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Supplier</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/customer')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Customer</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/product')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Produk</span></a>
                            </li>

                        </ul><!-- .nk-menu-sub -->
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
                            <span class="nk-menu-text">Transaksi Pusat</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/central-purchase')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembelian Barang</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/supplier-payment')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pembayaran Supplier</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/retur-supplier')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Retur Barang Pembelian</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="#" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penyelesaian Retur</span></a>
                            </li>
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/central-sale')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penjualan Barang</span></a>
                            </li>
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/badstock-release')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Pengeluaran Badstok</span></a>
                            </li>
                            <li class="nk-menu-item">
                                &nbsp;
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/reqtoretail')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Permintaan ke Retail</span></a>
                            </li>
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Transaksi Retail</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/saleretail')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Penjualan Barang</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/retail-stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                            <span class="nk-menu-text">Transaksi Studio</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/studio-stock-opname')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Stok Opname</span></a>
                            </li>
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">Administrator</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/group')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data Groups</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/user')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Data User</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="#" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Ganti Password</span></a>
                            </li>
                        </ul><!-- .nk-menu-sub -->
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                            <span class="nk-menu-text">Keuangan</span>
                        </a>
                        <ul class="nk-menu-sub" style="display: none;">
                            <li class="nk-menu-item">
                                <a href="{{url('/account')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Akun</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/account-transaction')}}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">Cash in/out</span></a>
                            </li>
                        </ul><!-- .nk-menu-sub -->
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