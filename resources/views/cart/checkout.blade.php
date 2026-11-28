@extends('layouts.app')

@section('title', 'Checkout Pembayaran - FoodHub')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">Keranjang</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <div class="row g-4 justify-content-between">
        {{-- Sisi Kiri: Form Checkout Pengiriman & Pembayaran --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold text-dark mb-1">Pengantaran & Pembayaran</h4>
                <p class="text-muted small mb-4">Pastikan alamat dan instruksi pengiriman sudah sesuai.</p>

                <form action="{{ route('cart.checkout.process') }}" method="POST">
                    @csrf

                    {{-- Alamat Pengiriman --}}
                    <div class="mb-4">
                        <label for="delivery_address" class="form-label fw-semibold text-dark">
                            Alamat Pengantaran Lengkap
                        </label>
                        <textarea name="delivery_address"
                                  id="delivery_address"
                                  rows="3"
                                  class="form-control rounded-3 @error('delivery_address') is-invalid @enderror"
                                  placeholder="Contoh: Jl. Ringroad Utara, Condongcatur, No. 45, Sleman (Rumah pagar hitam)"
                                  required>{{ old('delivery_address', Auth::user()->address ?? '') }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Catatan Pesanan --}}
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold text-dark">
                            Catatan untuk Restoran / Kurir <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="text"
                               name="notes"
                               id="notes"
                               class="form-control rounded-3 @error('notes') is-invalid @enderror"
                               placeholder="Contoh: Sambal dipisah, tolong titipkan di pos satpam..."
                               value="{{ old('notes') }}">
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block">Pilih Metode Pembayaran</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payCod" value="cod" checked>
                                <label class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer" for="payCod">
                                    <span><i class="bi bi-cash-stack text-success me-2"></i> Bayar di Tempat (COD)</span>
                                    <span class="badge bg-light text-dark border">Tunai</span>
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payQris" value="qris" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                                <label class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer" for="payQris">
                                    <span><i class="bi bi-qr-code text-primary me-2"></i> QRIS / Dompet Digital</span>
                                    <span class="badge bg-light text-dark border">Instan</span>
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payTransfer" value="transfer" {{ old('payment_method') === 'transfer' ? 'checked' : '' }}>
                                <label class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer" for="payTransfer">
                                    <span><i class="bi bi-credit-card text-info me-2"></i> Transfer Bank</span>
                                    <span class="badge bg-light text-dark border">Bank</span>
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-pill btn-lg w-100 shadow-sm py-3 fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i> Bayar & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        {{-- Sisi Kanan: Ringkasan Rincian Menu --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
                <h5 class="fw-bold text-dark mb-3">Pesananmu</h5>

                {{-- Info Restoran --}}
                <div class="d-flex align-items-center gap-2 p-2 bg-soft-green rounded-3 mb-3 text-primary small fw-semibold">
                    <i class="bi bi-shop"></i>
                    <span>{{ $cart->restaurant->name ?? 'Restoran FoodHub' }}</span>
                </div>

                {{-- Daftar Menu --}}
                <div class="d-flex flex-column gap-2 mb-3 max-vh-50 overflow-auto">
                    @foreach($cart->items as $item)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span class="fw-semibold text-dark d-block">{{ $item->menu->name ?? 'Menu' }}</span>
                                <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->menu->price ?? 0, 0, ',', '.') }}</small>
                            </div>
                            <span class="fw-semibold text-dark">
                                Rp {{ number_format(($item->menu->price ?? 0) * $item->quantity, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Biaya & Total --}}
                <div class="d-flex flex-column gap-2 small text-muted mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal Makanan:</span>
                        <span class="text-dark fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Ongkos Kirim:</span>
                        <span class="text-success fw-semibold">GRATIS</span>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-6">Total Pembayaran:</span>
                    <span class="fw-bolder text-primary fs-4">
                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
