@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <aside class="col-md-3 col-lg-2 bg-white border-end p-3" style="min-height: 75vh;">
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <span class="list-group-item text-muted"><i class="bi bi-people"></i> Pengguna</span>
                <span class="list-group-item text-muted"><i class="bi bi-shop"></i> Restoran</span>
                <span class="list-group-item text-muted"><i class="bi bi-tags"></i> Kategori</span>
                <span class="list-group-item text-muted"><i class="bi bi-receipt"></i> Transaksi</span>
            </div>
        </aside>

        {{-- Konten --}}
        <section class="col-md-9 col-lg-10 p-4">
            <h3 class="fw-bold">Dashboard Admin</h3>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }}. Menu akan diaktifkan di Stage 9.</p>
        </section>
    </div>
</div>
@endsection