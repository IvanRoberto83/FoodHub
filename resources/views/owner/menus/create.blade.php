@extends('layouts.owner')

@section('title', 'Tambah Menu Baru - ' . $restaurant->name)

@section('content')
<div class="admin-page-heading">
    <a href="{{ route('owner.menus.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Menu
    </a>
    <h1 class="fw-bold mb-1">Tambah Menu Makanan Baru</h1>
    <p class="text-muted small">Menu ini akan otomatis ditambahkan ke restoran <strong>{{ $restaurant->name }}</strong>.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card admin-table-card p-4">
            <form action="{{ route('owner.menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- Nama Menu --}}
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Nama Menu Makanan</label>
                        <input type="text" name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Contoh: Nasi Ayam Bakar Madu" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori Menu --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Kategori Menu</label>
                        <select name="category_id" class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Harga & Stok --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Harga per Porsi (Rp)</label>
                        <input type="number" name="price"
                               class="form-control rounded-3 @error('price') is-invalid @enderror"
                               value="{{ old('price') }}" placeholder="25000" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Stok Porsi Tersedia</label>
                        <input type="number" name="stock"
                               class="form-control rounded-3 @error('stock') is-invalid @enderror"
                               value="{{ old('stock', 30) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi Hidangan</label>
                        <textarea name="description" rows="3"
                                  class="form-control rounded-3 @error('description') is-invalid @enderror"
                                  placeholder="Jelaskan bahan pilihan, level pedas, atau porsi menu...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Foto --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Foto Hidangan</label>
                        <input type="file" name="image" id="ownerMenuImageInput"
                               class="form-control rounded-3 @error('image') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Opsional (Maks. 2MB). Jika dikosongkan, sistem akan memakai gambar ilustrasi otomatis.</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-3 text-center d-none" id="ownerPreviewContainer">
                            <span class="text-muted small d-block mb-1">Pratinjau Foto:</span>
                            <img src="" id="ownerImagePreview" class="rounded-4 shadow-sm border" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </div>

                    {{-- Status Ketersediaan --}}
                    <div class="col-12">
                        <div class="form-check form-switch mt-1">
                            <input type="hidden" name="is_available" value="0">
                            <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvailableCreate"
                                   {{ old('is_available', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isAvailableCreate">
                                Langsung tampilkan & tersedia untuk dipesan pelanggan
                            </label>
                        </div>
                    </div>

                    <div class="col-12 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Menu
                        </button>
                        <a href="{{ route('owner.menus.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('ownerMenuImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('ownerImagePreview').src = evt.target.result;
            document.getElementById('ownerPreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
