@extends('layouts.app')

@section('title', 'FoodHub - Nikmati Makanan Lezat & Sehat di Sekitarmu')

@section('content')
<div class="overflow-hidden">

    {{-- ================= HERO SECTION ================= --}}
    <section class="py-5 hero-wrapper position-relative">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                {{-- Kiri: Teks & Call To Action --}}
                <div class="col-12 col-lg-6">
                    <h1 class="display-4 fw-bolder text-dark lh-sm mb-3">
                        Enjoy Delicious<br>
                        <span class="hero-highlight">Healthy Food</span><br>
                        Near You
                    </h1>

                    <p class="text-secondary lead fs-6 mb-4 col-lg-10">
                        Pesan makanan sehat dan lezat dari restoran terbaik di sekitarmu. Cepat, higienis, dan diantar langsung ke rumahmu.
                    </p>

                    {{-- Bukti Sosial (Join with 4600+ Customer) --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="hero-avatar-stack d-flex">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Customer">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" alt="Customer">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80" alt="Customer">
                        </div>
                        <div class="small fw-semibold text-dark">
                            Bergabung dengan <span class="text-primary fw-bold">4600+</span> Pelanggan<br>
                            <span class="text-muted fw-normal">dan mulai pesan sekarang</span>
                        </div>
                    </div>

                    {{-- Tombol CTA --}}
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('menus.index') }}" class="btn btn-primary btn-pill shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-bag-check-fill"></i> Pesan Sekarang
                        </a>
                        <a href="{{ route('restaurants.index') }}" class="btn btn-outline-primary btn-pill d-inline-flex align-items-center gap-2">
                            <i class="bi bi-shop"></i> Lihat Restoran
                        </a>
                    </div>
                </div>

                {{-- Kanan: Hero Visual Food Dish --}}
                <div class="col-12 col-lg-6 d-flex justify-content-center position-relative">
                    <div class="position-relative">
                        {{-- Background Lingkaran Mint / Daun --}}
                        <div class="hero-food-circle">
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80"
                                 alt="Healthy Delicious Food"
                                 class="hero-food-img">
                        </div>

                        {{-- Floating Card Review Kathryn Murphy --}}
                        <div class="floating-review-card">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80"
                                 alt="Kathryn Murphy"
                                 class="rounded-3"
                                 style="width: 44px; height: 44px; object-fit: cover;">
                            <div>
                                <div class="fw-bold small text-dark mb-0">Kathryn Murphy</div>
                                <div class="text-muted" style="font-size: 11px;">Healthy Food</div>
                                <div class="text-warning small" style="font-size: 11px;">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Emoticon Reaksi --}}
                        <div class="floating-emoji floating-emoji-heart">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="floating-emoji floating-emoji-smile">
                            <span>😋</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CATEGORIES & MENU CARDS ================= --}}
    <section id="categories" class="py-5 bg-white">
        <div class="container py-3">
            {{-- Header Kategori --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <span class="text-primary text-uppercase fw-bold small tracking-wider">CATEGORIES</span>
                    <h2 class="fw-bold text-dark mt-1 mb-0">We care your Choice</h2>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
                    <a href="{{ route('menus.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        Lihat Semua Menu <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="row g-4 align-items-start">
                {{-- List Kategori Vertikal (Kiri) --}}
                <div class="col-12 col-lg-3">
                    <div class="d-flex flex-column gap-2">
                        @php
                            $defaultCategories = [
                                ['name' => 'Vegetarian', 'icon' => 'bi-flower1', 'active' => true],
                                ['name' => 'Fast Food',   'icon' => 'bi-cup-hot', 'active' => false],
                                ['name' => 'Italian',     'icon' => 'bi-pie-chart', 'active' => false],
                                ['name' => 'American',    'icon' => 'bi-fire', 'active' => false],
                                ['name' => 'Dessert',     'icon' => 'bi-cake2', 'active' => false],
                            ];
                        @endphp

                        @if(isset($categories) && $categories->isNotEmpty())
                            @foreach($categories->take(5) as $index => $cat)
                                <a href="{{ route('menus.index', ['category' => $cat->slug]) }}" class="category-menu-item {{ $index === 0 ? 'active' : '' }}">
                                    <div class="cat-icon">
                                        <i class="bi {{ $index === 0 ? 'bi-check2' : 'bi-tag' }}"></i>
                                    </div>
                                    <span>{{ $cat->name }}</span>
                                </a>
                            @endforeach
                        @else
                            @foreach($defaultCategories as $cat)
                                <a href="{{ route('menus.index') }}" class="category-menu-item {{ $cat['active'] ? 'active' : '' }}">
                                    <div class="cat-icon">
                                        <i class="bi {{ $cat['icon'] }}"></i>
                                    </div>
                                    <span>{{ $cat['name'] }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Grid Menu Makanan (Kanan) --}}
                <div class="col-12 col-lg-9">
                    <div class="row g-4">
                        @php
                            $sampleMenus = [
                                [
                                    'name' => 'Sausage salad with tomatoe',
                                    'price' => 'Rp 35.000',
                                    'rating' => '4.8',
                                    'reviews' => '114',
                                    'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80'
                                ],
                                [
                                    'name' => 'Vegetarian Plate Fresh',
                                    'price' => 'Rp 28.000',
                                    'rating' => '4.9',
                                    'reviews' => '78',
                                    'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80'
                                ],
                                [
                                    'name' => 'Veg Biryani & Veg Pulau',
                                    'price' => 'Rp 45.000',
                                    'rating' => '5.0',
                                    'reviews' => '162',
                                    'image' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80'
                                ],
                            ];
                        @endphp

                        @forelse($menus ?? [] as $index => $item)
                            <div class="col-12 col-md-4">
                                <div class="food-card h-100">
                                    <div class="card-img-wrapper">
                                        <a href="{{ route('menus.show', $item) }}">
                                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="card-img-top">
                                        </a>
                                        <button class="btn-fav" type="button" title="Tambah ke favorit">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <h6 class="fw-bold text-dark text-truncate mb-1">
                                            <a href="{{ route('menus.show', $item) }}" class="text-dark text-decoration-none">
                                                {{ $item->name }}
                                            </a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-1 mb-2 text-warning small" style="font-size: 12px;">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                            <span class="text-muted ms-1">(4.8)</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                            <span class="fw-bold text-dark fs-6">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            <form action="{{ route('cart.add', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" title="Tambah ke Keranjang">
                                                    <i class="bi bi-cart-plus"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            @foreach($sampleMenus as $item)
                                <div class="col-12 col-md-4">
                                    <div class="food-card h-100">
                                        <div class="card-img-wrapper">
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="card-img-top">
                                            <button class="btn-fav" type="button" title="Tambah ke favorit">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                        </div>
                                        <div class="p-3">
                                            <h6 class="fw-bold text-dark text-truncate mb-1">{{ $item['name'] }}</h6>
                                            <div class="d-flex align-items-center gap-1 mb-2 text-warning small" style="font-size: 12px;">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <span class="text-muted ms-1">({{ $item['reviews'] }})</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                                <span class="fw-bold text-dark fs-6">{{ $item['price'] }}</span>
                                                <button class="btn btn-sm btn-primary rounded-pill px-3">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FEATURED RESTAURANTS SECTION ================= --}}
    @if(isset($restaurants) && $restaurants->isNotEmpty())
    <section class="py-5 bg-light border-top border-bottom border-light">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <span class="text-primary text-uppercase fw-bold small tracking-wider">MITRA RESTORAN</span>
                    <h2 class="fw-bold text-dark mt-1 mb-0">Restoran Populer di Sekitarmu</h2>
                </div>
                <a href="{{ route('restaurants.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-3 mt-md-0">
                    Semua Restoran <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                @foreach($restaurants as $resto)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="resto-card h-100 d-flex flex-column">
                            <div class="resto-card-img-wrap" style="height: 155px;">
                                <a href="{{ route('restaurants.show', $resto->slug) }}">
                                    <img src="{{ $resto->image ?? 'https://picsum.photos/seed/resto-' . $resto->slug . '/600/400' }}" alt="{{ $resto->name }}">
                                </a>
                                @if($resto->is_open)
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1 small">Buka</span>
                                @else
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1 small">Tutup</span>
                                @endif
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1 text-truncate">
                                    <a href="{{ route('restaurants.show', $resto->slug) }}" class="text-dark text-decoration-none">
                                        {{ $resto->name }}
                                    </a>
                                </h6>
                                <small class="text-muted text-truncate mb-2">
                                    <i class="bi bi-geo-alt text-primary"></i> {{ $resto->address }}
                                </small>
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center small">
                                    <span class="text-muted">{{ $resto->menus_count }} Menu</span>
                                    <a href="{{ route('restaurants.show', $resto->slug) }}" class="text-primary fw-semibold text-decoration-none">
                                        Kunjungi <i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ================= DELIVERY PROMO SECTION ================= --}}
    <section id="delivery" class="py-5 bg-soft-green">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                {{-- Kiri: Ilustrasi Tilted Floating Food Plates --}}
                <div class="col-12 col-lg-5">
                    <div class="delivery-visual-wrapper">
                        {{-- Card 1 Tilted Salad --}}
                        <div class="card-tilt-1">
                            <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=260&q=80"
                                 alt="Salad Bowl"
                                 class="rounded-circle shadow-sm"
                                 style="width: 140px; height: 140px; object-fit: cover;">
                        </div>

                        {{-- Card 2 Tilted Fries --}}
                        <div class="card-tilt-2">
                            <img src="https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=260&q=80"
                                 alt="Fries Skillet"
                                 class="rounded-circle shadow-sm"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                {{-- Kanan: Detail Pengantaran 30 Menit --}}
                <div class="col-12 col-lg-7">
                    <h2 class="display-6 fw-bold text-dark mb-3">
                        We deliver food within<br>
                        <span class="text-primary">30 min</span>
                    </h2>
                    <p class="text-secondary lead fs-6 mb-4 col-lg-10">
                        Kami menjamin setiap pesanan dipersiapkan dari bahan berkualitas dan diantarkan oleh kurir kami dalam waktu maksimal 30 menit. Makanan tiba tetap hangat, segar, dan siap dinikmati.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#categories" class="btn btn-primary btn-pill shadow-sm">
                            <i class="bi bi-bag-check-fill me-1"></i> Order Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONIALS SECTION ================= --}}
    <section id="testimonials" class="py-5 bg-white">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                {{-- Kiri: Testimonial Teks --}}
                <div class="col-12 col-lg-6">
                    <span class="text-primary text-uppercase fw-bold small tracking-wider">TESTIMONIALS</span>
                    <h2 class="fw-bold text-dark mt-1 mb-4">
                        Check what our<br>Customer are saying
                    </h2>

                    {{-- Quote Symbol --}}
                    <div class="display-4 text-warning opacity-75 mb-2" style="line-height: 1;">
                        <i class="bi bi-quote"></i>
                    </div>

                    {{-- 5 Stars --}}
                    <div class="text-warning mb-3">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p class="text-secondary fs-6 fst-italic mb-4 lh-base">
                        "Pelayanan FoodHub sangat luar biasa! Makanan tiba tepat waktu dalam kondisi masih hangat dan rasanya sangat lezat. Pilihan menunya variatif dan sangat memudahkan untuk pola makan sehat sehari-hari."
                    </p>

                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Riana Safitri</h6>
                            <span class="text-muted small">Pelanggan Setia FoodHub</span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Foto Chef Berlatar Hijau --}}
                <div class="col-12 col-lg-6 d-flex justify-content-center">
                    <div class="testimonial-chef-wrapper w-100 text-center position-relative">
                        <div class="position-absolute top-0 end-0 p-3 text-warning fs-3">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&w=600&q=80"
                             alt="Chef FoodHub"
                             class="chef-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
