@extends('layouts.app')

@section('title', 'Katalog Menu Makanan - FoodHub')

@section('content')
<div class="bg-soft-green py-4 border-bottom border-light">
    <div class="container">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-12 col-md-6">
                <span class="text-primary text-uppercase fw-bold small tracking-wider">Katalog Lezat</span>
                <h1 class="h3 fw-bold text-dark mb-1">Eksplorasi Menu Makanan</h1>
                <p class="text-muted small mb-0">Temukan hidangan lezat dan segar favoritmu dari berbagai restoran.</p>
            </div>
            <div class="col-12 col-md-5">
                <form action="{{ route('menus.index') }}" method="GET">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="input-group bg-white rounded-pill p-1 shadow-sm border">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none"
                               placeholder="Cari nasi goreng, burger..."
                               value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('menus.index', array_filter(['category' => request('category')])) }}"
                               class="btn btn-link text-muted text-decoration-none">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    {{-- Filter Kategori --}}
    <div class="d-flex align-items-center gap-2 overflow-auto pb-3 mb-4">
        <a href="{{ route('menus.index', array_filter(['search' => $search])) }}"
           class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ !$categorySlug ? 'btn-primary' : 'btn-light text-dark border' }}">
            <i class="bi bi-grid-fill me-1"></i> Semua Kategori
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('menus.index', array_filter(['category' => $cat->slug, 'search' => $search])) }}"
               class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $categorySlug === $cat->slug ? 'btn-primary' : 'btn-light text-dark border' }}">
                <span>{{ $cat->name }}</span>
                <span class="badge {{ $categorySlug === $cat->slug ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25 text-dark' }} ms-1">
                    {{ $cat->menus_count }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Grid Menu --}}
    <div class="row g-4">
        @forelse($menus as $menu)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card food-card h-100 shadow-sm border-0 d-flex flex-column">
                    <div class="card-img-wrapper position-relative">
                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="card-img-top">
                        <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-2 fw-semibold small">
                            {{ $menu->category->name ?? 'Makanan' }}
                        </span>
                        @if($menu->stock > 0)
                            <span class="badge bg-success position-absolute bottom-0 start-0 m-3 rounded-pill px-2 py-1 small">
                                Stok: {{ $menu->stock }}
                            </span>
                        @else
                            <span class="badge bg-danger position-absolute bottom-0 start-0 m-3 rounded-pill px-2 py-1 small">
                                Habis
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-shop text-primary"></i>
                            <span class="text-truncate">{{ $menu->restaurant->name ?? 'FoodHub Kitchen' }}</span>
                        </div>
                        <h5 class="fw-bold text-dark text-truncate mb-2">
                            <a href="{{ route('menus.show', $menu) }}" class="text-dark text-decoration-none hover-primary">
                                {{ $menu->name }}
                            </a>
                        </h5>
                        <p class="text-muted small flex-grow-1 line-clamp-2 mb-3">
                            {{ Str::limit($menu->description, 80) }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                <span class="text-muted small d-block">Harga</span>
                                <span class="fw-bold text-primary fs-5">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('menus.show', $menu) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3" title="Lihat Detail">
                                    Detail
                                </a>
                                @if($menu->stock > 0 && $menu->is_available)
                                    <form action="{{ route('cart.add', $menu) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3" title="Tambah ke Keranjang">
                                            <i class="bi bi-cart-plus"></i> +Keranjang
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="mb-3 text-muted display-4">
                    <i class="bi bi-search"></i>
                </div>
                <h4 class="fw-bold text-dark">Menu Tidak Ditemukan</h4>
                <p class="text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
                <a href="{{ route('menus.index') }}" class="btn btn-primary btn-pill">
                    Reset Filter
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $menus->links() }}
    </div>
</div>
@endsection
