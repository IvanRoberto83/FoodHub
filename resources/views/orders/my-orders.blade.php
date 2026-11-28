@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya - FoodHub')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Riwayat Pesanan Saya</h2>
            <p class="text-muted small mb-0">Pantau dan kelola seluruh pesanan makanan yang pernah kamu buat.</p>
        </div>
        <a href="{{ route('menus.index') }}" class="btn btn-primary btn-pill shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Pesan Menu Baru
        </a>
    </div>

    {{-- Alert Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase small">Kode Order & Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small">Restoran & Item Menu</th>
                        <th class="py-3 text-secondary text-uppercase small">Total Bayar</th>
                        <th class="py-3 text-secondary text-uppercase small">Metode</th>
                        <th class="py-3 text-secondary text-uppercase small text-center">Status</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase small text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            {{-- Kode Order & Tanggal --}}
                            <td class="ps-4 py-3">
                                <span class="fw-bold text-dark d-block">{{ $order->order_code }}</span>
                                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            </td>

                            {{-- Restoran & Menu --}}
                            <td class="py-3">
                                <div class="fw-semibold text-dark">{{ $order->restaurant->name ?? 'FoodHub Kitchen' }}</div>
                                <div class="small text-muted">
                                    @foreach($order->items as $item)
                                        <div>• {{ $item->menu_name }} ({{ $item->quantity }}x)</div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Total Bayar --}}
                            <td class="py-3">
                                <span class="fw-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </td>

                            {{-- Metode Pembayaran --}}
                            <td class="py-3">
                                <span class="badge bg-light text-dark border text-uppercase">
                                    {{ $order->payment_method }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3 text-center">
                                @php
                                    $statusClasses = [
                                        'pending'    => 'bg-warning text-dark',
                                        'processing' => 'bg-info text-white',
                                        'completed'  => 'bg-success text-white',
                                        'cancelled'  => 'bg-danger text-white',
                                    ];
                                    $statusLabels = [
                                        'pending'    => 'Menunggu',
                                        'processing' => 'Diproses',
                                        'completed'  => 'Selesai',
                                        'cancelled'  => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }} rounded-pill px-3 py-2 fw-semibold">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="pe-4 py-3 text-end">
                                @if($order->status === 'pending')
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancel-order-{{ $order->id }}">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan
                                    </button>
                                    <x-confirm-modal
                                        id="cancel-order-{{ $order->id }}"
                                        title="Batalkan Pesanan?"
                                        message="Apakah Anda yakin ingin membatalkan pesanan <strong>{{ $order->order_code }}</strong>? Stok makanan akan dikembalikan dan tindakan ini tidak dapat dibatalkan."
                                        confirm-text="Ya, Batalkan Pesanan"
                                        confirm-class="btn-danger"
                                        form-action="{{ route('orders.cancel', $order) }}"
                                        method="PATCH"
                                        icon="bi-x-circle-fill"
                                        icon-class="text-danger"
                                    />
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted display-6 mb-2"><i class="bi bi-inbox"></i></div>
                                <h6 class="fw-bold text-dark">Belum Ada Pesanan</h6>
                                <p class="text-muted small">Kamu belum pernah memesan makanan apapun.</p>
                                <a href="{{ route('menus.index') }}" class="btn btn-primary btn-pill btn-sm">
                                    Mulai Pesan Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
