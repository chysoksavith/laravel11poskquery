@php
    $user = Auth::user();
@endphp
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="javascript:;}" class="brand-link">
            <img src="{{ asset('dist/assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">{{ $user->name }}</span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!-- Sidebar Menu -->

            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                @if ($user->is_role === 1)
                    <li class="nav-item ">
                        <a href="{{ url('admin/dashboard') }}"
                            class="nav-link @if (Request::segment(2) == 'dashboard') active @endif">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-header">Master</li>
                    <li class="nav-item ">
                        <a href="{{ url('admin/category') }}"
                            class="nav-link @if (Request::segment(2) == 'category') active @endif">
                            <i class="nav-icon fa fa-cube"></i>
                            <p>Category</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/brand') }}"
                            class="nav-link @if (Request::segment(2) == 'brand') active @endif">
                            <i class="nav-icon fa fa-cube"></i>
                            <p>brand</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ url('admin/product') }}"
                            class="nav-link @if (Request::segment(2) == 'product') active @endif">
                            <i class="nav-icon fa fa-cubes"></i>
                            <p>Product</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/member') }}"
                            class="nav-link @if (Request::segment(2) == 'member') active @endif">
                            <i class="nav-icon fa fa-id-card"></i>
                            <p>Members</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/pos') }}" class="nav-link">
                            <i class="nav-icon fa fa-truck"></i>
                            <p>Pos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/supplier') }}"
                            class="nav-link @if (Request::segment(2) == 'supplier') active @endif">
                            <i class="nav-icon fa fa-truck"></i>
                            <p>Supplier</p>
                        </a>
                    </li>
                    <li class="nav-header">Transaction</li>
                    <li class="nav-item">
                        <a href="{{ url('admin/expense') }}"
                            class="nav-link @if (Request::segment(2) == 'expense') active @endif ">
                            <i class="nav-icon fa fa-money-bill"></i>
                            <p>Expenses</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/purchase') }}"
                            class="nav-link @if (Request::segment(2) == 'purchase') active @endif">
                            <i class="nav-icon fa fa-download"></i>
                            <p>Purchase</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/sales') }}"
                            class="nav-link @if (Request::segment(2) == 'sales') active @endif">
                            <i class="nav-icon fa fa-dollar"></i>
                            <p>Sales List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-cart-plus"></i>
                            <p>New Transaction</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-cart-plus"></i>
                            <p>Active Transaction</p>
                        </a>
                    </li>
                    <li class="nav-header">Report</li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-asterisk"></i>
                            <p>InCome</p>
                        </a>
                    </li>
                    <li class="nav-header">System</li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-users"></i>
                            <p>User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-cogs"></i>
                            <p>Setting</p>
                        </a>
                    </li>
                @elseif ($user->is_role === 2)
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}" class="nav-link active">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-header">User</li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon bi bi-star-half"></i>
                            <p>Setting</p>
                        </a>
                    </li>
                    <li class="nav-header">Transaction</li>

                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-cart-plus"></i>
                            <p>New Transaction</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./docs/color-mode.html" class="nav-link">
                            <i class="nav-icon fa fa-cart-plus"></i>
                            <p>Active Transaction</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
