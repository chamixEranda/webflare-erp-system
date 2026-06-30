<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
          <!-- ========   Change your logo from here   ============ -->
          <img src="{{ asset('assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
        </a>
      </div>
      <div class="navbar-content">
        <ul class="pc-navbar">
          <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
              <span class="pc-mtext">Dashboard</span>
            </a>
          </li>

          <li class="pc-item pc-caption">
            <label>Product Catalog</label>
            <i class="ti ti-brand-chrome"></i>
          </li>
          <li class="pc-item pc-hasmenu {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.units.*') ? 'pc-trigger active' : '' }}">
            <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-box"></i></span><span
                class="pc-mtext">Products</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
            <ul class="pc-submenu">
              <li class="pc-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.categories.index') }}">Categories</a></li>
              <li class="pc-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.brands.index') }}">Brands</a></li>
              <li class="pc-item {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.units.index') }}">Units</a></li>
            </ul>
          </li>

          @if(auth()->user()->can('view users') || auth()->user()->can('view roles'))
            <li class="pc-item pc-caption">
              <label>User Management</label>
              <i class="ti ti-users"></i>
            </li>
            @can('view users')
              <li class="pc-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="pc-link">
                  <span class="pc-micon"><i class="ti ti-user"></i></span>
                  <span class="pc-mtext">Users</span>
                </a>
              </li>
            @endcan
            @can('view roles')
              <li class="pc-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.roles.index') }}" class="pc-link">
                  <span class="pc-micon"><i class="ti ti-settings"></i></span>
                  <span class="pc-mtext">Roles & Permissions</span>
                </a>
              </li>
            @endcan
          @endif

        </ul>
      </div>
    </div>
</nav>
  <!-- [ Sidebar Menu ] end -->