@extends('layouts.app')

@section('title', $menu->name . ' - FoodHub')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menus.index') }}" class="text-decoration-none text-muted">Katalog Menu</a></li>
            @if($menu->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('menus.index', ['category' => $menu->category->slug]) }}" class="text-decoration-none text-muted">
                        {{ $menu->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $menu->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5 align-items-start">
        {{-- Foto Menu --}}
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                @if($menu->category)
                    <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-2 fw-semibold">
                        {{ $menu->category->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Detail Informasi Menu & Aksi Pemesanan --}}
        <div class="col-12 col-md-6 col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                <i class="bi bi-shop text-primary"></i>
                <span class="fw-semibold text-dark">{{ $menu->restaurant->name ?? 'FoodHub Kitchen' }}</span>
                @if($menu->restaurant?->address)
                    <span>• {{ $menu->restaurant->address }}</span>
                @endif
            </div>

            <h1 class="h2 fw-bolder text-dark mb-2">{{ $menu->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="text-warning small">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                    <span class="text-dark fw-bold ms-1">4.8</span>
                    <span class="text-muted">(120+ ulasan)</span>
                </div>
                <span>•</span>
                @if($menu->stock > 0 && $menu->is_available)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                        <i class="bi bi-check-circle me-1"></i> Tersedia (Sisa {{ $menu->stock }})
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                        <i class="bi bi-x-circle me-1"></i> Stok Habis
                    </span>
                @endif
            </div>

            <div class="bg-soft-green p-3 rounded-4 mb-4 d-inline-block">
                <span class="text-muted small d-block">Harga per porsi</span>
                <span class="display-6 fw-bolder text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-2">Deskripsi Makanan</h6>
                <p class="text-secondary lh-base mb-0">
                    {{ $menu->description ?: 'Hidangan lezat dipersiapkan dengan bahan-bahan pilihan berkualitas tinggi dan higienis langsung oleh koki terbaik.' }}
                </p>
            </div>

            <hr class="my-4 text-muted opacity-25">

            {{-- Tombol Aksi Keranjang & Pesan Langsung --}}
            @if($menu->stock > 0 && $menu->is_available)
                <form action="{{ route('cart.add', $menu) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="let input=this.nextElementSibling; if(input.value>1) input.value--;">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" name="quantity" class="form-control text-center fw-bold" value="1" min="1" max="{{ $menu->stock }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="let input=this.previousElementSibling; if(input.value<{{ $menu->stock }}) input.value++;">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary btn-pill px-4 shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-cart-plus-fill fs-5"></i> + Keranjang
                        </button>

                        <a href="{{ route('orders.create', $menu) }}" class="btn btn-outline-primary btn-pill px-4">
                            Pesan Langsung
                        </a>
                    </div>
                </form>
            @else
                <div class="mb-3">
                    <button class="btn btn-secondary btn-pill px-4" disabled>
                        <i class="bi bi-slash-circle me-1"></i> Menu Sedang Habis
                    </button>
                </div>
            @endif

            <a href="{{ route('menus.index') }}" class="btn btn-link text-muted text-decoration-none px-0">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Menu
            </a>
        </div>
    </div>

    {{-- Menu Terkait --}}
    @if(isset($relatedMenus) && $relatedMenus->isNotEmpty())
        <div class="mt-5 pt-4 border-top">
            <h4 class="fw-bold text-dark mb-4">Menu Terkait yang Mungkin Kamu Suka</h4>
            <div class="row g-4">
                @foreach($relatedMenus as $rel)
                    <div class="col-12 col-md-4">
                        <div class="card food-card h-100 shadow-sm border-0">
                            <div class="card-img-wrapper">
                                <img src="{{ $rel->image }}" alt="{{ $rel->name }}" class="card-img-top">
                            </div>
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-truncate mb-1">
                                    <a href="{{ route('menus.show', $rel) }}" class="text-dark text-decoration-none">
                                        {{ $rel->name }}
                                    </a>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-primary fw-bold">Rp {{ number_format($rel->price, 0, ',', '.') }}</span>
                                    <a href="{{ route('menus.show', $rel) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
