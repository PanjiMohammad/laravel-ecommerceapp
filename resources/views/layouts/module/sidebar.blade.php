  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="{{ route('home') }}" class="brand-link">
        <div class="image">
            <img src="{{ asset('ecommerce/img/logo_pasar_jaya.jpg')}}" alt="User Image" class="mh-100 mw-100 mx-auto d-block" style="height: 100px; width: 100%;">
        </div>
    </a> -->
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('admin-lte/dist/img/rifkidev.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">Panji</a>
        </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            @if(Auth::guard('web')->check())
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{Request::path() == 'administrator' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('seller.dashboard') }}" class="nav-link {{Request::path() == 'seller' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            @endif

            @if(Auth::guard('web')->check())
                <li class="nav-header">DATA MASTER</li>
                <li class="nav-item">
                    <a href="{{ route('consumen.index') }}" class="nav-link {{Request::path() == 'administrator/customer' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Konsumen</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('seller.newIndex') }}" class="nav-link {{Request::path() == 'administrator/seller' ? 'active' : ''}}">
                        <i class="nav-icon fa fa-user-tag"></i>
                        <p>Penjual</p>
                    </a>
                </li>
            @endif

            <li class="nav-header">MANAJEMEN PRODUK</li>
            @if(Auth::guard('web')->check())
                <li class="nav-item">
                    <a href="{{ route('category.index') }}" class="nav-link {{Request::path() == 'administrator/category' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('product.index') }}" class="nav-link {{Request::path() == 'administrator/product' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-carrot"></i>
                        <p>Produk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{Request::path() == 'administrator/orders' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-business-time"></i>
                        <p>Pesanan</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Laporan<i class="right fas fa-angle-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report.order') }}" class="nav-link {{Request::path() == 'administrator/reports/order' ? 'active' : ''}}">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>Pesanan</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report.return') }}" class="nav-link {{Request::path() == 'administrator/reports/return' ? 'active' : ''}}">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>Pengembalian Pesanan</p>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                @if(Auth::guard('seller')->check())
                    <li class="nav-item">
                        <a href="{{ route('category.newIndex') }}" class="nav-link {{Request::path() == 'seller/category' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Kategori</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('product.newIndex') }}" class="nav-link {{Request::path() == 'seller/product' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-carrot"></i>
                            <p>Produk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.newIndex') }}" class="nav-link {{Request::path() == 'seller/orders' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-business-time"></i>
                            <p>Pesanan</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Laporan<i class="right fas fa-angle-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('report.newOrder') }}" class="nav-link {{Request::path() == 'seller/reports/order' ? 'active' : ''}}">
                                    <i class="nav-icon fa-regular fa-file"></i>
                                    <p>Pesanan</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('report.newReturn') }}" class="nav-link {{Request::path() == 'seller/reports/return' ? 'active' : ''}}">
                                    <i class="nav-icon fas fa-warehouse"></i>
                                    <p>Pengembalian Pesanan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endif
            <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Pengaturan<i class="right fas fa-angle-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-warehouse"></i>
                    <p>Toko</p>
                </a>
                </li>
            </ul>
            </li>
        </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <!-- Main Sidebar Container -->