@extends('layouts.app')

@section('title', 'Daftar Restoran Pilihan - FoodHub')

@section('content')
{{-- Hero Header Daftar Restoran --}}
<div class="bg-soft-green py-4 border-bottom border-light">
    <div class="container">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-12 col-md-6">
                <span class="text-primary text-uppercase fw-bold small tracking-wider">Mitra Kuliner</span>
                <h1 class="h3 fw-bold text-dark mb-1">Jelajahi Restoran Pilihan</h1>
                <p class="text-muted small mb-0">Pilih restoran favoritmu dan nikmati menu segar yang dimasak langsung.</p>
            </div>
            <div class="col-12 col-md-5">
                <form action="{{ route('restaurants.index') }}" method="GET">
                    @if($onlyOpen)
                        <input type="hidden" name="open" value="1">
                    @endif
                    <div class="input-group bg-white rounded-pill p-1 shadow-sm border">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none"
                               placeholder="Cari nama atau lokasi restoran..."
                               value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('restaurants.index', array_filter(['open' => $onlyOpen ? 1 : null])) }}"
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
    {{-- Filter Buka / Semua --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('restaurants.index', array_filter(['search' => $search])) }}"
           class="btn rounded-pill px-3 py-2 fw-semibold btn-sm {{ !$onlyOpen ? 'btn-primary' : 'btn-light border text-dark' }}">
            <i class="bi bi-grid-fill me-1"></i> Semua Restoran
        </a>
        <a href="{{ route('restaurants.index', array_filter(['open' => 1, 'search' => $search])) }}"
           class="btn rounded-pill px-3 py-2 fw-semibold btn-sm {{ $onlyOpen ? 'btn-primary' : 'btn-light border text-dark' }}">
            <i class="bi bi-check-circle-fill me-1"></i> Sedang Buka Saja
        </a>
    </div>

    {{-- Grid Restoran --}}
    <div class="row g-4">
        @forelse($restaurants as $resto)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="resto-card h-100 d-flex flex-column">
                    <div class="resto-card-img-wrap">
                        <a href="{{ route('restaurants.show', $resto->slug) }}">
                            <img src="{{ $resto->image ?? 'https://picsum.photos/seed/resto-' . $resto->slug . '/800/500' }}"
                                 alt="{{ $resto->name }}">
                        </a>
                        @if($resto->is_open)
                            <span class="badge bg-success position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i> Buka
                            </span>
                        @else
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-moon-stars-fill me-1"></i> Tutup
                            </span>
                        @endif
                        <span class="badge bg-white text-dark position-absolute bottom-0 end-0 m-3 rounded-pill px-3 py-2 shadow-sm fw-semibold">
                            <i class="bi bi-egg-fried text-primary me-1"></i> {{ $resto->menus_count }} Menu Aktif
                        </span>
                    </div>

                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1">
                            <a href="{{ route('restaurants.show', $resto->slug) }}" class="text-dark text-decoration-none">
                                {{ $resto->name }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-2 text-truncate">
                            <i class="bi bi-geo-alt text-primary me-1"></i>{{ $resto->address }}
                        </p>
                        <p class="text-secondary small flex-grow-1 mb-3">
                            {{ Str::limit($resto->description ?: 'Menyajikan aneka hidangan lezat dan higienis setiap hari.', 90) }}
                        </p>

                        <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
                            <span class="small text-muted">
                                <i class="bi bi-clock text-primary me-1"></i> ±20–30 mnt
                            </span>
                            <a href="{{ route('restaurants.show', $resto->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                Lihat Menu <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="text-muted display-4 mb-2"><i class="bi bi-shop"></i></div>
                <h4 class="fw-bold text-dark">Restoran Tidak Ditemukan</h4>
                <p class="text-muted">Coba gunakan kata kunci pencarian lain.</p>
                <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-pill">Tampilkan Semua Restoran</a>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $restaurants->links() }}
    </div>
</div>
@endsection
