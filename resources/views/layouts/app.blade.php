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

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white sticky-top py-3 border-bottom border-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-dark text-decoration-none" href="{{ route('home') }}">
                <span class="badge bg-primary text-white p-2 rounded-3 me-2 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-egg-fried fs-5"></i>
                </span>
                <span class="fw-bolder fs-4 tracking-tight">FOOD<span class="text-primary">HUB</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3 fw-medium">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('menus.*') ? 'text-primary fw-bold' : 'text-muted' }}" href="{{ route('menus.index') }}">Katalog Menu</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.my') ? 'text-primary fw-bold' : 'text-muted' }}" href="{{ route('orders.my') }}">Pesanan Saya</a></li>
                    @endauth
                    <li class="nav-item"><a class="nav-link text-muted" href="{{ route('home') }}#delivery">Pengiriman</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="{{ route('home') }}#testimonials">Testimoni</a></li>
                </ul>

                @guest
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-pill shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-pill d-none d-sm-inline-block">
                            Daftar
                        </a>
                    </div>
                @endguest

                @auth
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('cart.index') }}" class="btn btn-light rounded-pill px-3 py-2 border text-dark position-relative" title="Keranjang Belanja">
                            <i class="bi bi-cart3 text-primary fs-5"></i>
                            <span class="d-none d-sm-inline ms-1 fw-semibold">Keranjang</span>
                            @if(Auth::user()->cart && Auth::user()->cart->items()->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ Auth::user()->cart->items()->sum('quantity') }}
                                </span>
                            @endif
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill px-3 py-2 dropdown-toggle border" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle text-primary me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 mt-2">
                                <li><a class="dropdown-item py-2" href="{{ route('cart.index') }}"><i class="bi bi-cart3 me-2 text-primary"></i> Keranjang Belanja</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('orders.my') }}"><i class="bi bi-bag-check me-2 text-primary"></i> Pesanan Saya</a></li>

                                @if (Auth::user()->isAdmin())
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Admin</a></li>
                                @elseif (Auth::user()->isOwner())
                                    <li><a class="dropdown-item py-2" href="{{ route('owner.dashboard') }}"><i class="bi bi-shop me-2 text-primary"></i> Dashboard Restoran</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
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
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary text-white p-2 rounded-3 me-2 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-egg-fried"></i>
                        </span>
                        <span class="fw-bolder fs-4">FOOD<span class="text-primary">HUB</span></span>
                    </div>
                    <p class="text-muted small">
                        Pesan makanan segar, sehat, dan nikmat dari restoran favoritmu langsung sampai ke pintu rumahmu dalam hitungan menit.
                    </p>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold mb-3">Menu</h6>
                    <ul class="list-unstyled text-muted small d-flex flex-column gap-2">
                        <li><a href="#categories" class="text-decoration-none text-muted">Kategori Pilihan</a></li>
                        <li><a href="#categories" class="text-decoration-none text-muted">Menu Favorit</a></li>
                        <li><a href="#delivery" class="text-decoration-none text-muted">Layanan Antar</a></li>
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
