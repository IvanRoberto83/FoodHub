<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pesan makanan favoritmu') - FoodHub</title>

    {{-- Memuat CSS dan JS hasil kompilasi Vite --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Navbar: collapse di layar kecil (mobile) --}}
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-4" href="/">
                <i class="bi bi-egg-fried"></i> FoodHub
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="/">Beranda</a></li>
                    {{-- Link berikut diaktifkan di Stage 4 --}}
                    <li class="nav-item"><a class="nav-link" href="#">Restoran</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Kategori</a></li>
                </ul>

                {{-- @guest = tampil jika belum login, @auth = tampil jika sudah login --}}
                @guest
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                    </div>
                @endguest

                @auth
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-primary"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (Auth::user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                            @elseif (Auth::user()->isOwner())
                                <li><a class="dropdown-item" href="{{ route('owner.dashboard') }}">Dashboard Restoran</a></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                {{-- Logout harus POST + CSRF, bukan link biasa --}}
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Isi halaman akan masuk di sini --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="bg-white border-top py-4 mt-5">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} FoodHub. Proyek Pemrograman Web Lanjut.
        </div>
    </footer>

</body>
</html>