@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Menu
    </a>
    <h3 class="fw-bold text-dark mb-1">Tambah Menu Makanan Baru</h3>
    <p class="text-muted small">Lengkapi formulir untuk menambahkan hidangan ke katalog.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- Nama Menu --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Menu Makanan</label>
                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Contoh: Ayam Geprek Sambal Matah" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Restoran & Kategori --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Restoran</label>
                        <select name="restaurant_id" class="form-select rounded-3 @error('restaurant_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Restoran --</option>
                            @foreach($restaurants as $resto)
                                <option value="{{ $resto->id }}" {{ old('restaurant_id') == $resto->id ? 'selected' : '' }}>
                                    {{ $resto->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('restaurant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
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
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control rounded-3 @error('price') is-invalid @enderror"
                               value="{{ old('price') }}" placeholder="25000" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Stok Porsi</label>
                        <input type="number" name="stock" class="form-control rounded-3 @error('stock') is-invalid @enderror"
                               value="{{ old('stock', 50) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi Makanan</label>
                        <textarea name="description" rows="3" class="form-control rounded-3 @error('description') is-invalid @enderror"
                                  placeholder="Jelaskan cita rasa, bahan utama, dan porsi hidangan...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Foto dengan Preview --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Foto Menu</label>
                        <input type="file" name="image" id="menuImageInput" class="form-control rounded-3 @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-3 text-center d-none" id="previewContainer">
                            <span class="text-muted small d-block mb-1">Pratinjau Foto:</span>
                            <img src="" id="imagePreview" class="rounded-4 shadow-sm border" style="max-height: 220px; object-fit: cover;">
                        </div>
                    </div>

                    {{-- Status Ketersediaan --}}
                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvailable" checked>
                            <label class="form-check-label fw-semibold" for="isAvailable">Tersedia untuk dipesan pelanggan</label>
                        </div>
                    </div>

                    <div class="col-12 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Menu
                        </button>
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('menuImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('imagePreview').src = evt.target.result;
            document.getElementById('previewContainer').classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
