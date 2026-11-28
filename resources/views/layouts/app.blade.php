<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pesan makanan favoritmu') - FoodHub</title>

    {{-- Memuat CSS dan JS hasil kompilasi Vite --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100 bg-white">

    {{-- Modern Sticky Header Wrapper --}}
    <header class="fh-header-wrapper">
        {{-- Top Info Strip --}}
        <div class="fh-topbar">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 gap-md-3 text-truncate">
                    <span class="fw-semibold">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Pengantaran Cepat ±30 Menit
                    </span>
                    <span class="d-none d-md-inline opacity-50">•</span>
                    <span class="d-none d-md-inline opacity-90">
                        <i class="bi bi-shield-check me-1"></i>Restoran Higienis & Terverifikasi
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="fh-topbar-pill">
                                <i class="bi bi-grid-1x2-fill"></i> Panel Admin
                            </a>
                        @elseif(Auth::user()->isOwner())
                            <a href="{{ route('owner.dashboard') }}" class="fh-topbar-pill">
                                <i class="bi bi-shop"></i> Panel Restoran
                            </a>
                        @else
                            <span class="d-none d-sm-inline opacity-90">
                                <i class="bi bi-geo-alt-fill me-1"></i>Pesan Antar Aktif
                            </span>
                        @endif
                    @else
                        <span class="d-none d-sm-inline opacity-90">
                            <i class="bi bi-tag-fill me-1"></i>Gratis Ongkir Semua Resto
                        </span>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Main Glass Navbar --}}
        <nav class="navbar navbar-expand-lg fh-navbar">
            <div class="container gap-2">
                {{-- Brand Logo --}}
                <a class="fh-brand me-lg-2" href="{{ route('home') }}">
                    <span class="fh-brand-icon">
                        <i class="bi bi-egg-fried"></i>
                    </span>
                    <span class="fh-brand-text">Food<span class="text-primary">Hub</span></span>
                </a>

                {{-- Mobile Quick Cart + Toggler --}}
                <div class="d-flex align-items-center gap-2 d-lg-none">
                    @auth
                        <a href="{{ route('cart.index') }}" class="fh-cart-btn py-1 px-2" title="Keranjang">
                            <i class="bi bi-cart3 text-primary"></i>
                            @php $mobileCartQty = Auth::user()->cart ? Auth::user()->cart->items()->sum('quantity') : 0; @endphp
                            @if($mobileCartQty > 0)
                                <span class="badge rounded-pill bg-danger">{{ $mobileCartQty }}</span>
                            @endif
                        </a>
                    @endauth
                    <button class="navbar-toggler border-0 shadow-none p-1" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                {{-- Collapsible Nav, Search & Actions --}}
                <div class="collapse navbar-collapse" id="mainNav">
                    {{-- Pill Navigation Links --}}
                    <ul class="navbar-nav fh-nav-pills me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link fh-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house-door"></i> Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fh-nav-link {{ request()->routeIs('restaurants.*') ? 'active' : '' }}" href="{{ route('restaurants.index') }}">
                                <i class="bi bi-shop"></i> Restoran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fh-nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}" href="{{ route('menus.index') }}">
                                <i class="bi bi-compass"></i> Katalog Menu
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link fh-nav-link {{ request()->routeIs('orders.my') ? 'active' : '' }}" href="{{ route('orders.my') }}">
                                    <i class="bi bi-receipt-cutoff"></i> Pesanan Saya
                                </a>
                            </li>
                        @endauth
                    </ul>

                    {{-- Quick Search Input di Header --}}
                    <form action="{{ route('menus.index') }}" method="GET" class="fh-header-search me-lg-3">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari makan atau minuman..."
                               value="{{ request()->routeIs('menus.index') ? request('search') : '' }}">
                    </form>

                    {{-- Right Actions (Guest vs Auth) --}}
                    @guest
                        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm btn-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold btn-sm">
                                Daftar
                            </a>
                        </div>
                    @endguest

                    @auth
                        @php
                            $cartCount = Auth::user()->cart ? Auth::user()->cart->items()->sum('quantity') : 0;
                            $userInitial = mb_substr(Auth::user()->name, 0, 1);
                        @endphp
                        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                            {{-- Desktop Cart Pill --}}
                            <a href="{{ route('cart.index') }}" class="fh-cart-btn d-none d-lg-inline-flex" title="Keranjang Belanja">
                                <i class="bi bi-cart3 text-primary fs-6"></i>
                                <span>Keranjang</span>
                                @if($cartCount > 0)
                                    <span class="badge rounded-pill bg-danger ms-1">{{ $cartCount }}</span>
                                @endif
                            </a>

                            {{-- User Profile Pill Dropdown --}}
                            <div class="dropdown">
                                <button class="btn fh-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="fh-user-avatar">{{ $userInitial }}</span>
                                    <span class="text-truncate" style="max-width: 115px;">{{ Auth::user()->name }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                                    <li class="px-3 py-2 border-bottom mb-1">
                                        <div class="fw-bold text-dark small text-truncate">{{ Auth::user()->name }}</div>
                                        <span class="badge bg-soft-green text-primary rounded-pill text-uppercase" style="font-size: 10px;">
                                            {{ Auth::user()->role }}
                                        </span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-medium" href="{{ route('cart.index') }}">
                                            <i class="bi bi-cart3 me-2 text-primary"></i> Keranjang Belanja
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-medium" href="{{ route('orders.my') }}">
                                            <i class="bi bi-bag-check me-2 text-primary"></i> Pesanan Saya
                                        </a>
                                    </li>

                                    @if (Auth::user()->isAdmin())
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 small fw-semibold text-primary" href="{{ route('admin.dashboard') }}">
                                                <i class="bi bi-grid-1x2 me-2"></i> Dashboard Admin
                                            </a>
                                        </li>
                                    @elseif (Auth::user()->isOwner())
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 small fw-semibold text-primary" href="{{ route('owner.dashboard') }}">
                                                <i class="bi bi-shop me-2"></i> Dashboard Restoran
                                            </a>
                                        </li>
                                    @endif

                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded-3 py-2 small fw-semibold text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow-1">
        @if(session('success') && !request()->routeIs('orders.my'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error') && !request()->routeIs('orders.my'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-top py-5 mt-5">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fh-brand-icon" style="width: 34px; height: 34px; font-size: 1rem;">
                            <i class="bi bi-egg-fried"></i>
                        </span>
                        <span class="fw-bolder fs-4">Food<span class="text-primary">Hub</span></span>
                    </div>
                    <p class="text-muted small">
                        Pesan makanan segar, sehat, dan nikmat dari restoran favoritmu langsung sampai ke pintu rumahmu dalam hitungan menit.
                    </p>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold mb-3">Jelajahi</h6>
                    <ul class="list-unstyled text-muted small d-flex flex-column gap-2">
                        <li><a href="{{ route('restaurants.index') }}" class="text-decoration-none text-muted">Daftar Restoran</a></li>
                        <li><a href="{{ route('menus.index') }}" class="text-decoration-none text-muted">Katalog Menu</a></li>
                        <li><a href="{{ route('home') }}#delivery" class="text-decoration-none text-muted">Layanan Antar</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold mb-3">Bantuan</h6>
                    <ul class="list-unstyled text-muted small d-flex flex-column gap-2">
                        <li><a href="#" class="text-decoration-none text-muted">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 text-muted opacity-25">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center small text-muted">
                <div>&copy; {{ date('Y') }} FoodHub. Proyek Pemrograman Web Lanjut.</div>
                <div class="d-flex gap-3 mt-2 mt-sm-0">
                    <a href="#" class="text-muted fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-muted fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted fs-5"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
