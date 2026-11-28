@extends('layouts.app')

@section('title', 'Pesan ' . $menu->name . ' - FoodHub')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menus.index') }}" class="text-decoration-none text-muted">Katalog Menu</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menus.show', $menu) }}" class="text-decoration-none text-muted">{{ $menu->name }}</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Form Pemesanan</li>
        </ol>
    </nav>

    <div class="row g-4 justify-content-between">
        {{-- Sisi Kiri: Form Input Pemesanan --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold text-dark mb-1">Konfirmasi & Detail Pesanan</h4>
                <p class="text-muted small mb-4">Lengkapi informasi pengantaran untuk memproses pesananmu.</p>

                <form action="{{ route('orders.store', $menu) }}" method="POST" id="orderForm">
                    @csrf

                    {{-- Jumlah Porsi / Quantity --}}
                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-semibold text-dark">
                            Jumlah Porsi (Maksimal: {{ $menu->stock }})
                        </label>
                        <div class="input-group" style="max-width: 220px;">
                            <button type="button" class="btn btn-outline-secondary" id="btnMinus">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   class="form-control text-center fw-bold @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 1) }}"
                                   min="1"
                                   max="{{ $menu->stock }}"
                                   required>
                            <button type="button" class="btn btn-outline-secondary" id="btnPlus">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Pengantaran --}}
                    <div class="mb-4">
                        <label for="delivery_address" class="form-label fw-semibold text-dark">Alamat Pengiriman</label>
                        <textarea name="delivery_address"
                                  id="delivery_address"
                                  rows="3"
                                  class="form-control rounded-3 @error('delivery_address') is-invalid @enderror"
                                  placeholder="Contoh: Jl. Kaliurang KM 5, Gang Pandega Marta No. 12, Sleman"
                                  required>{{ old('delivery_address') }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold text-dark">Catatan Khusus <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text"
                               name="notes"
                               id="notes"
                               class="form-control rounded-3 @error('notes') is-invalid @enderror"
                               placeholder="Contoh: Jangan terlalu pedas, kuah dipisah ya..."
                               value="{{ old('notes') }}">
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block">Metode Pembayaran</label>
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
                                    <span><i class="bi bi-qr-code text-primary me-2"></i> QRIS / E-Wallet</span>
                                    <span class="badge bg-light text-dark border">Instan</span>
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" class="btn btn-primary btn-pill btn-lg w-100 shadow-sm py-3 fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi & Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>

        {{-- Sisi Kanan: Live Calculation & Ringkasan Menu --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
                <h5 class="fw-bold text-dark mb-3">Ringkasan Pesanan</h5>

                <div class="d-flex gap-3 align-items-center pb-3 border-bottom mb-3">
                    <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="rounded-3" style="width: 75px; height: 75px; object-fit: cover;">
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="badge bg-soft-green text-primary rounded-pill px-2 py-1 small mb-1">
                            {{ $menu->category->name ?? 'Menu' }}
                        </span>
                        <h6 class="fw-bold text-dark text-truncate mb-0">{{ $menu->name }}</h6>
                        <small class="text-muted d-block text-truncate">
                            <i class="bi bi-shop me-1"></i> {{ $menu->restaurant->name ?? 'Restoran FoodHub' }}
                        </small>
                    </div>
                </div>

                {{-- Live Price Breakdown --}}
                <div class="d-flex flex-column gap-2 small text-muted mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Harga Satuan:</span>
                        <span class="text-dark fw-semibold">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Jumlah Porsi:</span>
                        <span class="text-dark fw-semibold" id="displayQty">1x</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Biaya Pengantaran:</span>
                        <span class="text-success fw-semibold">GRATIS (Promo)</span>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-6">Total Pembayaran:</span>
                    <span class="fw-bolder text-primary fs-4" id="displayTotal">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </span>
                </div>

                <div class="alert alert-light border mt-4 mb-0 small text-muted rounded-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <div>Garansi tiba dalam 30 menit atau gratis voucher makan.</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script Live Calculation Sederhana --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitPrice = {{ (int) $menu->price }};
    const maxStock = {{ (int) $menu->stock }};
    const qtyInput = document.getElementById('quantity');
    const displayQty = document.getElementById('displayQty');
    const displayTotal = document.getElementById('displayTotal');
    const btnMinus = document.getElementById('btnMinus');
    const btnPlus = document.getElementById('btnPlus');

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function updateTotal() {
        let val = parseInt(qtyInput.value) || 1;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        qtyInput.value = val;

        displayQty.textContent = val + 'x';
        displayTotal.textContent = formatRupiah(unitPrice * val);
    }

    btnMinus.addEventListener('click', function () {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) {
            qtyInput.value = val - 1;
            updateTotal();
        }
    });

    btnPlus.addEventListener('click', function () {
        let val = parseInt(qtyInput.value) || 1;
        if (val < maxStock) {
            qtyInput.value = val + 1;
            updateTotal();
        }
    });

    qtyInput.addEventListener('input', updateTotal);
    qtyInput.addEventListener('change', updateTotal);
});
</script>
@endsection
