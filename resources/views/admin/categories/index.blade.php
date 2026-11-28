@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Kategori Makanan</h3>
        <p class="text-muted small mb-0">Kelola master kategori menu makanan di FoodHub.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Gambar</th>
                    <th>Nama Kategori</th>
                    <th>Slug URL</th>
                    <th>Jumlah Menu</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $cat)
                    <tr>
                        <td class="ps-4 text-muted">{{ $categories->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ $cat->image ?: 'https://picsum.photos/seed/' . $cat->slug . '/100/100' }}"
                                 alt="{{ $cat->name }}"
                                 class="rounded-3 shadow-sm"
                                 style="width: 44px; height: 44px; object-fit: cover;">
                        </td>
                        <td class="fw-bold text-dark">{{ $cat->name }}</td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                {{ $cat->menus_count }} Menu
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditCategory{{ $cat->id }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete-category-{{ $cat->id }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>

                            {{-- Modal Hapus Konfirmasi --}}
                            <x-confirm-modal
                                id="delete-category-{{ $cat->id }}"
                                title="Hapus Kategori?"
                                message="Apakah Anda yakin ingin menghapus kategori <strong>{{ $cat->name }}</strong>? Kategori yang memiliki menu tidak dapat dihapus."
                                confirm-text="Ya, Hapus"
                                confirm-class="btn-danger"
                                form-action="{{ route('admin.categories.destroy', $cat) }}"
                                method="DELETE"
                                icon="bi-trash-fill"
                                icon-class="text-danger"
                            />
                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    <div class="modal fade" id="modalEditCategory{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form action="{{ route('admin.categories.update', $cat) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Kategori</label>
                                            <input type="text" name="name" class="form-control rounded-3" value="{{ $cat->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">URL Gambar (Opsional)</label>
                                            <input type="url" name="image" class="form-control rounded-3" value="{{ $cat->image }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $categories->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modal Create Category --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Aneka Jus, Seafood..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL Gambar (Opsional)</label>
                        <input type="url" name="image" class="form-control rounded-3" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
