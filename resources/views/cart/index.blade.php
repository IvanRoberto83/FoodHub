@extends('layouts.app')

@section('title', 'Keranjang Belanja - FoodHub')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Keranjang Belanja</h2>
            <p class="text-muted small mb-0">Periksa makanan pilihanmu sebelum melakukan pembayaran.</p>
        </div>
        <a href="{{ route('menus.index') }}" class="btn btn-outline-primary btn-pill">
            <i class="bi bi-plus-lg me-1"></i> Tambah Menu Lain
        </a>
    </div>

    {{-- Alert Konflik Restoran (Jika memilih menu dari resto berbeda) --}}
    @if(session('cart_conflict'))
        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">Ganti Restoran?</h5>
                    <p class="mb-3 small">{{ session('cart_conflict')['message'] }}</p>
                    <form action="{{ route('cart.add', session('cart_conflict')['new_menu_id']) }}" method="POST">
                        @csrf
                        <input type="hidden" name="force_replace" value="1">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm">
                            Ya, Kosongkan & Pesan {{ session('cart_conflict')['new_menu_name'] }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($cart && $cart->items->isNotEmpty())
        <div class="row g-4 align-items-start">
            {{-- Daftar Item Keranjang --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    {{-- Banner Restoran --}}
                    <div class="d-flex align-items-center gap-2 pb-3 border-bottom mb-3">
                        <div class="badge bg-soft-green text-primary p-2 rounded-3">
                            <i class="bi bi-shop fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Pesanan dari:</span>
                            <h6 class="fw-bold text-dark mb-0">{{ $cart->restaurant->name ?? 'FoodHub Kitchen' }}</h6>
                        </div>
                    </div>

                    {{-- List Menu dalam Keranjang --}}
                    <div class="d-flex flex-column gap-3">
                        @foreach($cart->items as $item)
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between p-3 rounded-3 bg-light gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->menu->image ?? 'https://picsum.photos/100/100' }}"
                                         alt="{{ $item->menu->name ?? 'Menu' }}"
                                         class="rounded-3 shadow-sm"
                                         style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $item->menu->name ?? 'Menu Makanan' }}</h6>
                                        <span class="text-primary fw-semibold small">
                                            Rp {{ number_format($item->menu->price ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Aksi Kuantitas & Hapus --}}
                                <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3">
                                    {{-- Form Update Quantity --}}
                                    <div class="d-flex align-items-center gap-1 border rounded-pill bg-white px-2 py-1 shadow-sm">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                                            <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" title="Kurangi">
                                                <i class="bi bi-dash-circle fs-5"></i>
                                            </button>
                                        </form>

                                        <span class="fw-bold px-2">{{ $item->quantity }}</span>

                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                            <button type="submit" class="btn btn-sm btn-link text-primary p-0 text-decoration-none"
                                                    {{ ($item->menu && $item->quantity >= $item->menu->stock) ? 'disabled' : '' }} title="Tambah">
                                                <i class="bi bi-plus-circle fs-5"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Subtotal per item --}}
                                    <div class="text-end" style="min-width: 100px;">
                                        <span class="fw-bold text-dark d-block">
                                            Rp {{ number_format(($item->menu->price ?? 0) * $item->quantity, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('cart.remove', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm text-danger p-0 border-0 bg-transparent" title="Hapus dari keranjang">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Ringkasan Pembayaran & Tombol Checkout --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold text-dark mb-3">Ringkasan Pesanan</h5>

                    <div class="d-flex justify-content-between text-muted small mb-2">
                        <span>Total Porsi:</span>
                        <span class="fw-semibold text-dark">{{ $cart->items->sum('quantity') }} porsi</span>
                    </div>

                    <div class="d-flex justify-content-between text-muted small mb-2">
                        <span>Biaya Pengantaran:</span>
                        <span class="fw-semibold text-success">GRATIS</span>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark fs-6">Subtotal:</span>
                        <span class="fw-bolder text-primary fs-4">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-pill btn-lg w-100 shadow-sm fw-bold">
                        Lanjut ke Pembayaran <i class="bi bi-arrow-right ms-1"></i>
                    </a>

                    <div class="mt-3 text-center">
                        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Transaksi Aman & Terverifikasi</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Keranjang Kosong --}}
        <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
            <div class="mb-3 text-muted display-4">
                <i class="bi bi-bag-x"></i>
            </div>
            <h4 class="fw-bold text-dark">Keranjang Belanja Kosong</h4>
            <p class="text-muted small mb-4">Yuk, cari makanan lezat dan tambahkan ke keranjangmu sekarang!</p>
            <div>
                <a href="{{ route('menus.index') }}" class="btn btn-primary btn-pill px-4 shadow-sm">
                    Eksplorasi Menu Makanan
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
