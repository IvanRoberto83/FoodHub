@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodHub')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('restaurants.index') }}" class="text-decoration-none text-muted">Restoran</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $restaurant->name }}</li>
        </ol>
    </nav>

    {{-- Banner Profil Restoran --}}
    <div class="resto-hero-banner mb-4">
        <div class="row g-0 align-items-center">
            <div class="col-12 col-md-5 col-lg-4">
                <img src="{{ $restaurant->image ?? 'https://picsum.photos/seed/resto-' . $restaurant->slug . '/800/500' }}"
                     alt="{{ $restaurant->name }}"
                     class="w-100"
                     style="height: 240px; object-fit: cover;">
            </div>
            <div class="col-12 col-md-7 col-lg-8 p-4 p-lg-5">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    @if($restaurant->is_open)
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> Sedang Buka
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-semibold">
                            <i class="bi bi-moon-stars-fill me-1"></i> Sedang Tutup Sementara
                        </span>
                    @endif
                    <span class="badge bg-soft-green text-primary rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-bicycle me-1"></i> Pengantaran ±25 Menit
                    </span>
                </div>

                <h1 class="h2 fw-bolder text-dark mb-2">{{ $restaurant->name }}</h1>
                <p class="text-secondary mb-3">
                    {{ $restaurant->description ?: 'Nikmati aneka sajian terbaik yang dimasak segar dengan bahan pilihan.' }}
                </p>

                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><i class="bi bi-geo-alt-fill text-primary me-1"></i> {{ $restaurant->address }}</span>
                    @if($restaurant->phone)
                        <span><i class="bi bi-telephone-fill text-primary me-1"></i> {{ $restaurant->phone }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$restaurant->is_open)
        <div class="alert alert-warning rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <span>Restoran ini sedang <strong>tutup sementara</strong>. Kamu tetap dapat melihat katalog menu di bawah ini.</span>
        </div>
    @endif

    {{-- Filter Kategori & Cari Menu di Restoran Ini --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2 overflow-auto pb-1">
            <a href="{{ route('restaurants.show', ['restaurant' => $restaurant->slug, 'search' => $search]) }}"
               class="btn rounded-pill px-3 py-2 fw-semibold btn-sm text-nowrap {{ !$categorySlug ? 'btn-primary' : 'btn-light border text-dark' }}">
                Semua Menu
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('restaurants.show', array_filter(['restaurant' => $restaurant->slug, 'category' => $cat->slug, 'search' => $search])) }}"
                   class="btn rounded-pill px-3 py-2 fw-semibold btn-sm text-nowrap {{ $categorySlug === $cat->slug ? 'btn-primary' : 'btn-light border text-dark' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('restaurants.show', $restaurant->slug) }}" method="GET" style="min-width: 250px;">
            @if($categorySlug)
                <input type="hidden" name="category" value="{{ $categorySlug }}">
            @endif
            <div class="input-group input-group-sm bg-white rounded-pill p-1 border shadow-sm">
                <input type="text" name="search" class="form-control border-0 shadow-none ps-3"
                       placeholder="Cari menu di resto ini..." value="{{ $search }}">
                <button type="submit" class="btn btn-primary rounded-pill px-3">Cari</button>
            </div>
        </form>
    </div>

    {{-- Daftar Menu Restoran --}}
    <div class="row g-4">
        @forelse($menus as $menu)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card food-card h-100 shadow-sm border-0 d-flex flex-column">
                    <div class="card-img-wrapper position-relative">
                        <a href="{{ route('menus.show', $menu) }}">
                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="card-img-top">
                        </a>
                        @if($menu->category)
                            <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-2 fw-semibold small">
                                {{ $menu->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="fw-bold text-dark text-truncate mb-1">
                            <a href="{{ route('menus.show', $menu) }}" class="text-dark text-decoration-none">
                                {{ $menu->name }}
                            </a>
                        </h5>
                        <p class="text-muted small flex-grow-1 mb-3">
                            {{ Str::limit($menu->description ?: 'Hidangan spesial favorit pelanggan.', 80) }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                <span class="text-muted small d-block">Harga</span>
                                <span class="fw-bold text-primary fs-5">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                            @if($restaurant->is_open && $menu->is_available && $menu->stock > 0)
                                <form action="{{ route('cart.add', $menu) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-cart-plus me-1"></i> +Keranjang
                                    </button>
                                </form>
                            @elseif(!$restaurant->is_open)
                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" disabled>Resto Tutup</button>
                            @else
                                <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>Habis</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="text-muted display-5 mb-2"><i class="bi bi-cup-hot"></i></div>
                <h5 class="fw-bold text-dark">Belum Ada Menu</h5>
                <p class="text-muted small">Restoran ini belum memiliki menu yang sesuai dengan filter pilihanmu.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $menus->links() }}
    </div>
</div>
@endsection
