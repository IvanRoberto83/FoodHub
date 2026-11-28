@extends('layouts.admin')

@section('title', 'Manajemen Transaksi Pesanan')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Transaksi Pesanan</h3>
        <p class="text-muted small mb-0">Pantau pesanan masuk dan ubah status operasional.</p>
    </div>
</div>

{{-- Filter Status Tabs --}}
<div class="d-flex gap-2 overflow-auto pb-2 mb-4">
    <a href="{{ route('admin.orders.index') }}" class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ !$status ? 'btn-primary' : 'btn-light border text-dark' }}">
        Semua Pesanan
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-light border text-dark' }}">
        <i class="bi bi-clock me-1"></i> Menunggu
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'processing' ? 'btn-info text-white' : 'btn-light border text-dark' }}">
        <i class="bi bi-arrow-repeat me-1"></i> Diproses
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'completed' ? 'btn-success text-white' : 'btn-light border text-dark' }}">
        <i class="bi bi-check-circle me-1"></i> Selesai
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'cancelled' ? 'btn-danger text-white' : 'btn-light border text-dark' }}">
        <i class="bi bi-x-circle me-1"></i> Dibatalkan
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">Kode Order</th>
                    <th>Pelanggan & Alamat</th>
                    <th>Item Menu</th>
                    <th>Total Bayar</th>
                    <th>Status Saat Ini</th>
                    <th class="pe-4 text-end">Ubah Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $order->order_code }}</span>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                            <span class="badge bg-light text-dark border text-uppercase d-block mt-1" style="max-width: 60px;">
                                {{ $order->payment_method }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->customer->name ?? 'User' }}</div>
                            <small class="text-muted d-block">{{ $order->customer->phone ?? '' }}</small>
                            <small class="text-secondary text-truncate d-block" style="max-width: 250px;">
                                <i class="bi bi-geo-alt"></i> {{ $order->delivery_address }}
                            </small>
                            @if($order->notes)
                                <small class="text-primary fst-italic d-block">"{{ $order->notes }}"</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $order->restaurant->name ?? '-' }}</div>
                            <div class="small text-muted">
                                @foreach($order->items as $item)
                                    <div>• {{ $item->menu_name }} ({{ $item->quantity }}x)</div>
                                @endforeach
                            </div>
                        </td>
                        <td class="fw-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
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
                        <td class="pe-4 text-end">
                            {{-- Dropdown status tetap untuk ubah status biasa --}}
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline-flex gap-1 align-items-center">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm rounded-pill" style="width: 140px;" onchange="this.form.submit()">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Tidak ada pesanan {{ $status ? "berstatus {$status}" : '' }}.
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
@endsection
