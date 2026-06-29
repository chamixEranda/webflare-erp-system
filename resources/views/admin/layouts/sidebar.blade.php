<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
          <!-- ========   Change your logo from here   ============ -->
          <img src="../assets/images/logo-dark.svg" class="img-fluid logo-lg" alt="logo">
        </a>
      </div>
      <div class="navbar-content">
        <ul class="pc-navbar">
          <li class="pc-item">
            <a href="{{ route('admin.dashboard') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
              <span class="pc-mtext">Dashboard</span>
            </a>
          </li>

          <li class="pc-item pc-caption">
            <label>Product Catalog</label>
            <i class="ti ti-brand-chrome"></i>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-box"></i></span><span
                class="pc-mtext">Products</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
            <ul class="pc-submenu">
              <li class="pc-item"><a class="pc-link" href="{{ route('admin.categories.index') }}">Categories</a></li>
              <li class="pc-item"><a class="pc-link" href="{{ route('admin.brands.index') }}">Brands</a></li>
              <li class="pc-item"><a class="pc-link" href="{{ route('admin.units.index') }}">Units</a></li>
            </ul>
          </li>
          <li class="pc-item">
            <a href="{{ route('admin.units.index') }}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-ruler"></i></span>
              <span class="pc-mtext">Units</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
</nav>
  <!-- [ Sidebar Menu ] end -->