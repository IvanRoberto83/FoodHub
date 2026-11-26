@extends('layouts.app')

@section('title', 'Dashboard Restoran')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <aside class="col-md-3 col-lg-2 bg-white border-end p-3" style="min-height: 75vh;">
            <div class="list-group list-group-flush">
                <a href="{{ route('owner.dashboard') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <span class="list-group-item text-muted"><i class="bi bi-shop"></i> Info Restoran</span>
                <span class="list-group-item text-muted"><i class="bi bi-egg-fried"></i> Menu</span>
                <span class="list-group-item text-muted"><i class="bi bi-receipt"></i> Pesanan Masuk</span>
            </div>
        </aside>

        {{-- Konten --}}
        <section class="col-md-9 col-lg-10 p-4">
            <h3 class="fw-bold">Dashboard Restoran</h3>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }}. Menu akan diaktifkan di Stage 5.</p>
        </section>
    </div>
</div>
@endsection