@extends('layouts.owner')

@section('title', 'Edit Menu - ' . $menu->name)

@section('content')
<div class="admin-page-heading">
    <a href="{{ route('owner.menus.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Menu
    </a>
    <h1 class="fw-bold mb-1">Edit Menu: {{ $menu->name }}</h1>
    <p class="text-muted small">Perbarui detail hidangan milik restoran <strong>{{ $restaurant->name }}</strong>.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card admin-table-card p-4">
            <form action="{{ route('owner.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Nama Menu --}}
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Nama Menu Makanan</label>
                        <input type="text" name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $menu->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori Menu --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Kategori Menu</label>
                        <select name="category_id" class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>
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
                               value="{{ old('price', $menu->price) }}" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Stok Porsi Tersedia</label>
                        <input type="number" name="stock"
                               class="form-control rounded-3 @error('stock') is-invalid @enderror"
                               value="{{ old('stock', $menu->stock) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi Hidangan</label>
                        <textarea name="description" rows="3"
                                  class="form-control rounded-3 @error('description') is-invalid @enderror">{{ old('description', $menu->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Foto Saat Ini & Ganti Foto --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Foto Saat Ini</label>
                        <div class="mb-2">
                            <img src="{{ $menu->image }}" id="ownerCurrentImage" alt="{{ $menu->name }}"
                                 class="rounded-4 shadow-sm border" style="height: 150px; object-fit: cover;">
                        </div>
                        <label class="form-label fw-semibold small text-muted">Ganti Foto Baru (Opsional)</label>
                        <input type="file" name="image" id="ownerMenuImageEditInput"
                               class="form-control rounded-3 @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-check mt-2">
                            <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="removeOwnerMenuImage" {{ old('remove_image') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="removeOwnerMenuImage">Hapus foto saat ini dan gunakan foto default.</label>
                        </div>
                    </div>

                    {{-- Status Ketersediaan --}}
                    <div class="col-12">
                        <div class="form-check form-switch mt-1">
                            <input type="hidden" name="is_available" value="0">
                            <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvailableEdit"
                                   {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isAvailableEdit">
                                Tersedia untuk dipesan pelanggan
                            </label>
                        </div>
                    </div>

                    <div class="col-12 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('owner.menus.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('ownerMenuImageEditInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('ownerCurrentImage').src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
