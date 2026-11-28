<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Owner Panel') - FoodHub</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-shell">
    <nav class="navbar admin-navbar sticky-top px-3 px-lg-4">
        <a class="navbar-brand admin-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('owner.dashboard') }}">
            <span class="admin-brand-mark"><i class="bi bi-egg-fried"></i></span>
            <span>FoodHub <span class="admin-brand-label">Owner</span></span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
            <a href="{{ route('home') }}" class="btn admin-public-link btn-sm" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-box-arrow-up-right me-1"></i><span class="d-none d-sm-inline">Lihat Web</span>
            </a>
            <div class="dropdown">
                <button class="btn admin-user-button btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="admin-user-avatar"><i class="bi bi-person-fill"></i></span>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row admin-layout">
            <aside class="col-lg-2 admin-sidebar">
                <div class="admin-sidebar-inner">
                    <p class="admin-sidebar-heading">Menu utama</p>
                    <nav class="nav flex-column gap-1" aria-label="Navigasi owner">
                        <a href="{{ route('owner.dashboard') }}"
                           class="admin-nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"
                           @if(request()->routeIs('owner.dashboard')) aria-current="page" @endif>
                            <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                        </a>
                        <a href="{{ route('owner.restaurant.edit') }}"
                           class="admin-nav-link {{ request()->routeIs('owner.restaurant.*') ? 'active' : '' }}"
                           @if(request()->routeIs('owner.restaurant.*')) aria-current="page" @endif>
                            <i class="bi bi-shop"></i><span>Info Restoran</span>
                        </a>
                        <a href="{{ route('owner.menus.index') }}"
                           class="admin-nav-link {{ request()->routeIs('owner.menus.*') ? 'active' : '' }}"
                           @if(request()->routeIs('owner.menus.*')) aria-current="page" @endif>
                            <i class="bi bi-cup-hot"></i><span>Menu Makanan</span>
                        </a>
                        <a href="{{ route('owner.orders.index') }}"
                           class="admin-nav-link {{ request()->routeIs('owner.orders.*') ? 'active' : '' }}"
                           @if(request()->routeIs('owner.orders.*')) aria-current="page" @endif>
                            <i class="bi bi-receipt"></i><span>Transaksi Pesanan</span>
                        </a>
                    </nav>
                    <div class="admin-sidebar-note">
                        <i class="bi bi-shield-check"></i>
                        <span>Panel pengelolaan {{ Auth::user()->restaurant?->name ?? 'Restoran' }}</span>
                    </div>
                </div>
            </aside>

            <main class="col-lg-10 admin-main">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
