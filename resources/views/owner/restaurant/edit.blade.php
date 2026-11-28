@extends('layouts.owner')

@section('title', 'Informasi Restoran')

@section('content')
<div class="admin-page-heading">
    <h1 class="fw-bold mb-1">
        {{ $restaurant ? 'Pengaturan Informasi Restoran' : 'Buat Profil Restoran Anda' }}
    </h1>
    <p class="text-muted small">
        Kelola identitas restoran, alamat, foto banner, dan status operasional penerimaan pesanan.
    </p>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('owner.restaurant.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Nama Restoran --}}
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Nama Restoran</label>
                        <input type="text" name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $restaurant?->name) }}"
                               placeholder="Contoh: Warung Nusantara Spesial" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor Telepon Restoran --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone"
                               class="form-control rounded-3 @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $restaurant?->phone) }}"
                               placeholder="0812xxxxxxxx">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Lengkap Restoran</label>
                        <textarea name="address" rows="2"
                                  class="form-control rounded-3 @error('address') is-invalid @enderror"
                                  placeholder="Jl. Raya Darmo No. 12, Surabaya" required>{{ old('address', $restaurant?->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi Restoran --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi Singkat Restoran</label>
                        <textarea name="description" rows="3"
                                  class="form-control rounded-3 @error('description') is-invalid @enderror"
                                  placeholder="Ceritakan keunggulan masakan atau ciri khas restoran Anda...">{{ old('description', $restaurant?->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Foto Banner Restoran --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Foto / Banner Restoran</label>
                        @if($restaurant?->image)
                            <div class="mb-2">
                                <img src="{{ $restaurant->image }}" id="restoImagePreview" alt="{{ $restaurant->name }}"
                                     class="rounded-4 shadow-sm border" style="max-height: 180px; object-fit: cover;">
                            </div>
                        @else
                            <div class="mb-2 d-none" id="previewWrapper">
                                <img src="" id="restoImagePreview" alt="Preview"
                                     class="rounded-4 shadow-sm border" style="max-height: 180px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" id="restoImageInput"
                               class="form-control rounded-3 @error('image') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Format JPG, PNG, atau WEBP (Maks. 2MB). Kosongkan jika tidak ingin mengubah foto.</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($restaurant)
                            <div class="form-check mt-2">
                                <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="removeRestoImage" {{ old('remove_image') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="removeRestoImage">Hapus foto saat ini dan gunakan foto default.</label>
                            </div>
                        @endif
                    </div>

                    {{-- Status Buka / Tutup --}}
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold text-dark">Status Operasional Restoran</div>
                                <small class="text-muted">Jika dimatikan, pelanggan tidak dapat memesan menu dari restoran Anda sementara waktu.</small>
                            </div>
                            <div class="form-check form-switch fs-4 mb-0">
                                <input type="hidden" name="is_open" value="0">
                                <input class="form-check-input" type="checkbox" name="is_open" value="1" id="isOpenSwitch"
                                       {{ old('is_open', $restaurant ? $restaurant->is_open : true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Informasi Restoran
                        </button>
                        @if($restaurant)
                            <a href="{{ route('owner.dashboard') }}" class="btn btn-light rounded-pill px-4">Kembali</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Kartu Info Tambahan --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-soft-green">
            <h6 class="fw-bold text-dark mb-2">
                <i class="bi bi-lightbulb-fill text-warning me-1"></i> Tips Mitra FoodHub
            </h6>
            <ul class="small text-secondary ps-3 mb-0 d-flex flex-column gap-2">
                <li>Gunakan foto restoran atau hidangan andalan yang terang dan menggugah selera.</li>
                <li>Pastikan alamat dan nomor telepon aktif untuk memudahkan koordinasi pengantaran.</li>
                <li>Matikan saklar <strong>Status Operasional</strong> saat jam tutup agar pesanan pelanggan tidak menumpuk.</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.getElementById('restoImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            const img = document.getElementById('restoImagePreview');
            img.src = evt.target.result;
            const wrapper = document.getElementById('previewWrapper');
            if (wrapper) wrapper.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
