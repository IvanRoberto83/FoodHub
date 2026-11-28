@extends('layouts.owner')

@section('title', 'Pesanan Masuk - ' . $restaurant->name)

@section('content')
<div class="admin-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h1 class="fw-bold mb-1">Transaksi Pesanan Masuk</h1>
        <p class="text-muted small">Kelola dan perbarui status pesanan pelanggan untuk <strong>{{ $restaurant->name }}</strong>.</p>
    </div>
</div>

{{-- Tab Filter Status Pesanan beserta Jumlahnya --}}
<div class="d-flex gap-2 overflow-auto pb-2 mb-4">
    <a href="{{ route('owner.orders.index') }}"
       class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ !$status ? 'btn-primary' : 'btn-white bg-white border text-dark' }}">
        Semua <span class="badge {{ !$status ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $statusCounts['all'] }}</span>
    </a>
    <a href="{{ route('owner.orders.index', ['status' => 'pending']) }}"
       class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-white bg-white border text-dark' }}">
        <i class="bi bi-clock me-1"></i> Menunggu <span class="badge bg-dark bg-opacity-10 text-dark ms-1">{{ $statusCounts['pending'] }}</span>
    </a>
    <a href="{{ route('owner.orders.index', ['status' => 'processing']) }}"
       class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'processing' ? 'btn-info text-white' : 'btn-white bg-white border text-dark' }}">
        <i class="bi bi-fire me-1"></i> Diproses <span class="badge {{ $status === 'processing' ? 'bg-white text-info' : 'bg-light text-dark border' }} ms-1">{{ $statusCounts['processing'] }}</span>
    </a>
    <a href="{{ route('owner.orders.index', ['status' => 'completed']) }}"
       class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'completed' ? 'btn-success text-white' : 'btn-white bg-white border text-dark' }}">
        <i class="bi bi-check-circle me-1"></i> Selesai <span class="badge {{ $status === 'completed' ? 'bg-white text-success' : 'bg-light text-dark border' }} ms-1">{{ $statusCounts['completed'] }}</span>
    </a>
    <a href="{{ route('owner.orders.index', ['status' => 'cancelled']) }}"
       class="btn rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $status === 'cancelled' ? 'btn-danger text-white' : 'btn-white bg-white border text-dark' }}">
        <i class="bi bi-x-circle me-1"></i> Dibatalkan <span class="badge {{ $status === 'cancelled' ? 'bg-white text-danger' : 'bg-light text-dark border' }} ms-1">{{ $statusCounts['cancelled'] }}</span>
    </a>
</div>

{{-- Tabel Pesanan Masuk --}}
<div class="card admin-table-card overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">Kode & Waktu</th>
                    <th>Pelanggan & Pengantaran</th>
                    <th>Rincian Pesanan</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Aksi Proses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $order->order_code }}</span>
                            <small class="text-muted d-block">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            <span class="badge bg-light text-dark border text-uppercase mt-1">
                                {{ $order->payment_method }}
                            </span>
                        </td>
                        <td style="max-width: 260px;">
                            <div class="fw-bold text-dark">{{ $order->customer->name ?? 'Pelanggan' }}</div>
                            @if($order->customer?->phone)
                                <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $order->customer->phone }}</small>
                            @endif
                            <small class="text-secondary d-block mt-1">
                                <i class="bi bi-geo-alt text-primary"></i> {{ $order->delivery_address }}
                            </small>
                            @if($order->notes)
                                <small class="text-primary fst-italic d-block mt-1">
                                    <i class="bi bi-chat-left-text me-1"></i>"{{ $order->notes }}"
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                @foreach($order->items as $item)
                                    <div class="d-flex justify-content-between gap-3 py-1 border-bottom border-light">
                                        <span class="text-dark fw-medium">{{ $item->menu_name }} <span class="text-muted">x{{ $item->quantity }}</span></span>
                                        <span class="text-muted">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="fw-bold text-primary text-nowrap">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
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
                            @if($order->status === 'pending')
                                <div class="d-flex justify-content-end gap-1 mb-1">
                                    <form action="{{ route('owner.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="bi bi-check2-circle me-1"></i> Proses
                                        </button>
                                    </form>
                                    <form action="{{ route('owner.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Batalkan Pesanan">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @elseif($order->status === 'processing')
                                <form action="{{ route('owner.orders.status', $order) }}" method="POST" class="mb-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 text-white">
                                        <i class="bi bi-bag-check-fill me-1"></i> Tandai Selesai
                                    </button>
                                </form>
                            @elseif($order->status === 'cancelled')
                                <span class="text-muted small fst-italic">Pesanan dibatalkan</span>
                            @else
                                <span class="text-success small fw-semibold"><i class="bi bi-check-all"></i> Transaksi Tuntas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Belum ada pesanan {{ $status ? 'dengan filter status ini' : 'yang masuk' }}.
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
