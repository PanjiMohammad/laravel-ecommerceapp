<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-gear" style="color: black;"></i>
          </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <h6 class="dropdown-header">Pengaturan</h6>
          @if(Auth::guard('web')->check())
          <div class="dropdown-item">
            <a href="{{ route('user.acountSetting', Auth::guard('web')->user()->id)}}" style="color: black;"><span class="fa fa-gear" style="color: black;"></span> {{ Auth::guard('web')->user()?->name }}</a>
          </div>
          @else
            @if(Auth::guard('seller')->check())
            <div class="dropdown-item">
              <a href="{{ route('seller.setting', Auth::guard('seller')->user()->id)}}" style="color: black;"><span class="fa fa-gear" style="color: black;"></span> {{ Auth::guard('seller')->user()->name }}</a>
            </div>
            @endif
          @endif
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </div>
      </li>
    </ul>
</nav>