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

                {{-- Tombol login/daftar diaktifkan di Stage 3 --}}
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-primary">Masuk</a>
                    <a href="#" class="btn btn-primary">Daftar</a>
                </div>
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