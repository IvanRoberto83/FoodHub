@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="hero-foodhub py-5">
        <div class="container py-4">
            <h1 class="display-5 fw-bold">Lapar? Pesan di FoodHub</h1>
            <p class="lead mb-4">Makanan dari restoran favoritmu, diantar cepat ke rumah.</p>
            <a href="#" class="btn btn-light btn-lg text-primary fw-semibold">
                <i class="bi bi-search"></i> Cari Restoran
            </a>
        </div>
    </section>

    <div class="container mt-5">
        <h4 class="fw-bold mb-3">Tes tampilan</h4>
        <div class="row g-4">
            @foreach (['Restoran', 'Menu', 'Pesanan'] as $item)
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm card-hover h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-shop display-4 text-primary"></i>
                            <h5 class="mt-3">{{ $item }}</h5>
                            <button class="btn btn-primary btn-sm">Tombol Orange</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection