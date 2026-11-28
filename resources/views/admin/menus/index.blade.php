@extends('layouts.admin')

@section('title', 'Manajemen Menu')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Menu Makanan</h3>
        <p class="text-muted small mb-0">Kelola daftar hidangan, harga, stok, dan ketersediaan menu.</p>
    </div>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Menu Baru
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Foto</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Restoran</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $item)
                    <tr>
                        <td class="ps-4 text-muted">{{ $menus->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                {{ $item->description }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1">
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $item->restaurant->name ?? '-' }}</small></td>
                        <td class="fw-semibold text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>
                            @if($item->stock > 0)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                                    {{ $item->stock }}
                                </span>
                            @else
                                <span class="badge bg-danger rounded-pill px-2 py-1">0 (Habis)</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_available && $item->stock > 0)
                                <span class="badge bg-success rounded-pill px-2 py-1">Tersedia</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-2 py-1">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.menus.edit', $item) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete-menu-{{ $item->id }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>

                            {{-- Modal Hapus Konfirmasi --}}
                            <x-confirm-modal
                                id="delete-menu-{{ $item->id }}"
                                title="Hapus Menu?"
                                message="Apakah Anda yakin ingin menghapus menu <strong>{{ $item->name }}</strong>? Data pesanan yang terkait tidak akan terhapus."
                                confirm-text="Ya, Hapus"
                                confirm-class="btn-danger"
                                form-action="{{ route('admin.menus.destroy', $item) }}"
                                method="DELETE"
                                icon="bi-trash-fill"
                                icon-class="text-danger"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada menu makanan.</td>
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
