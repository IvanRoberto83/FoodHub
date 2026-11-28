@extends('layouts.owner')

@section('title', 'Kelola Menu - ' . $restaurant->name)

@section('content')
<div class="admin-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="fw-bold mb-1">Kelola Menu Restoran</h1>
        <p class="text-muted small">Atur hidangan, harga, stok porsi, dan ketersediaan menu untuk <strong>{{ $restaurant->name }}</strong>.</p>
    </div>
    <a href="{{ route('owner.menus.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Menu Baru
    </a>
</div>

{{-- Filter & Pencarian Menu --}}
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('owner.menus.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0"
                       placeholder="Cari nama atau deskripsi menu..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="category_id" class="form-select bg-light">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">Filter</button>
            @if($search || $categoryId)
                <a href="{{ route('owner.menus.index') }}" class="btn btn-light border rounded-pill px-3">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel Daftar Menu Milik Restoran Owner --}}
<div class="card admin-table-card overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Foto</th>
                    <th>Nama Hidangan</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Ketersediaan</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $item)
                    <tr>
                        <td class="ps-4 text-muted">{{ $menus->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ $item->image }}" alt="{{ $item->name }}"
                                 class="rounded-3 shadow-sm border" style="width: 54px; height: 54px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 240px;">
                                {{ $item->description ?: 'Tanpa deskripsi' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="fw-semibold text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>
                            @if($item->stock > 0)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                    {{ $item->stock }} porsi
                                </span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-1">0 (Habis)</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('owner.menus.toggle', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                @if($item->is_available && $item->stock > 0)
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="Klik untuk nonaktifkan">
                                        <i class="bi bi-check-circle me-1"></i> Tersedia
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Klik untuk aktifkan">
                                        <i class="bi bi-slash-circle me-1"></i> Nonaktif
                                    </button>
                                @endif
                            </form>
                        </td>
                        <td class="pe-4 text-end text-nowrap">
                            <a href="{{ route('owner.menus.edit', $item) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete-owner-menu-{{ $item->id }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>

                            {{-- Modal Konfirmasi Hapus --}}
                            <x-confirm-modal
                                id="delete-owner-menu-{{ $item->id }}"
                                title="Hapus Menu Makanan?"
                                message="Apakah Anda yakin ingin menghapus menu <strong>{{ e($item->name) }}</strong> dari restoran Anda? Riwayat transaksi yang sudah ada tetap aman."
                                confirm-text="Ya, Hapus"
                                confirm-class="btn-danger"
                                form-action="{{ route('owner.menus.destroy', $item) }}"
                                method="DELETE"
                                icon="bi-trash-fill"
                                icon-class="text-danger"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-egg-fried display-6 d-block mb-2"></i>
                            Belum ada menu makanan yang terdaftar di restoran Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($menus->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $menus->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
