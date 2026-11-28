@extends('layouts.owner')

@section('title', 'Dashboard Restoran')

@section('content')
<div class="admin-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="fw-bold mb-1">Dashboard Restoran</h1>
        <p class="text-muted small">
            Ringkasan statistik operasional <strong>{{ $restaurant->name }}</strong> saat ini
            ({{ $restaurant->is_open ? 'Sedang Buka' : 'Sedang Tutup' }}).
        </p>
    </div>
    <form action="{{ route('owner.restaurant.toggle') }}" method="POST">
        @csrf
        @method('PATCH')
        @if($restaurant->is_open)
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                <i class="bi bi-pause-circle me-1"></i> Tutup Restoran
            </button>
        @else
            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 text-white">
                <i class="bi bi-play-circle me-1"></i> Buka Restoran
            </button>
        @endif
    </form>
</div>

{{-- 4 Kartu Statistik Cepat --}}
<div class="row g-3 mb-4">
    {{-- Perlu Diproses --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-stat-icon bg-primary bg-opacity-10 text-primary fs-3">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Perlu Diproses</span>
                    <h3 class="fw-bolder text-dark mb-0">{{ $pendingOrdersCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Menu --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-stat-icon bg-info bg-opacity-10 text-info fs-3">
                    <i class="bi bi-cup-hot"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Total Menu</span>
                    <h3 class="fw-bolder text-dark mb-0">{{ $totalMenus }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Pesanan Hari Ini --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-stat-icon bg-warning bg-opacity-10 text-warning fs-3">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Pesanan Hari Ini</span>
                    <h3 class="fw-bolder text-dark mb-0">{{ $todayOrdersCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Pendapatan --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="admin-stat-icon bg-success bg-opacity-10 text-success fs-3">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Total Pendapatan</span>
                    <h4 class="fw-bolder text-success mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Transaksi Terkini --}}
<div class="card admin-table-card overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Pesanan Masuk Terkini</h5>
        <a href="{{ route('owner.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Lihat Semua Pesanan
        </a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">Kode Order</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $order->order_code }}</td>
                        <td>{{ $order->customer->name ?? 'Guest' }}</td>
                        <td class="fw-semibold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending'    => 'bg-warning text-dark',
                                    'processing' => 'bg-info text-white',
                                    'completed'  => 'bg-success text-white',
                                    'cancelled'  => 'bg-danger text-white',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }} rounded-pill px-2 py-1">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="pe-4 text-end text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
