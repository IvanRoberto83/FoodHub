<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\MenuController as OwnerMenuController;
use App\Http\Controllers\Owner\OrderController as OwnerOrderController;
use App\Http\Controllers\Owner\RestaurantController as OwnerRestaurantController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. Halaman Publik (Guest & User)
// ==========================================
Route::get('/', function () {
    try {
        $categories = \App\Models\Category::withCount('menus')->get();
        $menus = \App\Models\Menu::with(['category', 'restaurant'])
            ->where('is_available', true)
            ->take(6)
            ->get();
        $restaurants = \App\Models\Restaurant::withCount([
            'menus' => fn ($q) => $q->where('is_available', true)->where('stock', '>', 0),
        ])->orderByDesc('is_open')->take(4)->get();
    } catch (\Throwable $e) {
        $categories = collect();
        $menus = collect();
        $restaurants = collect();
    }

    return view('home', compact('categories', 'menus', 'restaurants'));
})->name('home');

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');

// ==========================================
// 2. Autentikasi (Guest Only)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==========================================
// 3. Halaman Customer (Wajib Login)
// ==========================================
Route::middleware('auth')->group(function () {
    // Keranjang Belanja (Cart)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{menu}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout Multi-Menu dari Keranjang
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('cart.checkout.process');

    // Pemesanan Langsung 1 Menu (Quick Order)
    Route::get('/orders/create/{menu}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/create/{menu}', [OrderController::class, 'store'])->name('orders.store');

    // Riwayat & Pembatalan Pesanan
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// ==========================================
// 4. Area Admin (Wajib Login & Role Admin)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Kategori
        Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);

        // Master Menu
        Route::resource('menus', AdminMenuController::class)->except(['show']);

        // Transaksi Pesanan
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    });

// ==========================================
// 5. Area Restaurant Owner (Role Owner)
// ==========================================
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // Informasi & Status Operasional Restoran
        Route::get('/restaurant', [OwnerRestaurantController::class, 'edit'])->name('restaurant.edit');
        Route::put('/restaurant', [OwnerRestaurantController::class, 'update'])->name('restaurant.update');
        Route::patch('/restaurant/toggle-status', [OwnerRestaurantController::class, 'toggleStatus'])->name('restaurant.toggle');

        // Manajemen Menu Milik Restoran Sendiri
        Route::patch('/menus/{menu}/toggle', [OwnerMenuController::class, 'toggleAvailability'])->name('menus.toggle');
        Route::resource('menus', OwnerMenuController::class)->except(['show']);

        // Proses Pesanan Masuk Restoran
        Route::get('/orders', [OwnerOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [OwnerOrderController::class, 'updateStatus'])->name('orders.status');
    });
