<div class="card" style="background-color: #f9f9ff">
    <div class="card-body">
        <h3>Main Menu</h3>
        <!-- <ul class="menu-sidebar-area">
            <li class="icon-dashboard">
                <a href="{{ route('customer.dashboard') }}">Dashboard</a>
            </li>
            <li class="icon-customers">
                <a href="{{ route('customer.orders') }}">Pesanan</a>
            </li>
            <li class="icon-wishlists">
                <a href="{{ route('customer.wishlist') }}">Wishlists</a>
            </li>
            <li class="icon-users">
                <a href="{{ route('customer.settingForm') }}">Pengaturan</a>
            </li>
        </ul> -->
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{ route('customer.dashboard') }}" style="color: black;">
                    <i class="fa-solid fa-house"></i>
                    <span class="font-weight-bold">Beranda</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.orders') }}" style="color: black;">
                    <span class="fa-solid fa-cart-shopping"></span>
                    <span class="font-weight-bold">Pesanan</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.wishlist') }}" style="color: black;">
                    <i class="fa-solid fa-heart"></i>
                    <span class="font-weight-bold">Daftar Keinginan</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.settingForm') }}" style="color: black;">
                    <i class="fa-solid fa-gear"></i>
                    <span class="font-weight-bold">Pengaturan</span>
                </a>
            </li>
        </ul>
    </div>
</div>