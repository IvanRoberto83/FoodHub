<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - FoodHub</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">

    {{-- Top Navbar Admin --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-2 px-3 shadow-sm">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.dashboard') }}">
            <span class="badge bg-primary text-white p-2 rounded-3"><i class="bi bi-egg-fried"></i></span>
            <span>FOODHUB <span class="badge bg-danger ms-1 text-uppercase small">Admin</span></span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Web
            </a>
            <div class="dropdown">
                <button class="btn btn-dark btn-sm rounded-pill dropdown-toggle border" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-primary me-1"></i> {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <aside class="col-md-3 col-lg-2 bg-white border-end p-3 min-vh-100" style="position: sticky; top: 64px; height: calc(100vh - 64px); overflow-y: auto; contain: layout style;">
                <div class="list-group list-group-flush gap-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="list-group-item list-group-item-action rounded-3 border-0 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white fw-bold' : 'text-dark' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="list-group-item list-group-item-action rounded-3 border-0 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('admin.categories.*') ? 'bg-primary text-white fw-bold' : 'text-dark' }}">
                        <i class="bi bi-tags"></i> Kategori Menu
                    </a>
                    <a href="{{ route('admin.menus.index') }}"
                       class="list-group-item list-group-item-action rounded-3 border-0 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('admin.menus.*') ? 'bg-primary text-white fw-bold' : 'text-dark' }}">
                        <i class="bi bi-cup-hot"></i> Menu Makanan
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="list-group-item list-group-item-action rounded-3 border-0 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-white fw-bold' : 'text-dark' }}">
                        <i class="bi bi-receipt"></i> Transaksi Pesanan
                    </a>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="col-md-9 col-lg-10 p-4">
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
