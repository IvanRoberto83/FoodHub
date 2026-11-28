<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama Dashboard Restoran milik Owner yang sedang login.
     */
    public function index()
    {
        $restaurant = Auth::user()->restaurant;

        // Jika akun owner baru belum memiliki profil restoran, arahkan ke halaman pengaturan restoran
        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran Anda terlebih dahulu sebelum mulai berjualan.');
        }

        // Statistik khusus untuk restoran milik Owner ini saja
        $totalMenus        = $restaurant->menus()->count();
        $availableMenus    = $restaurant->menus()->where('is_available', true)->where('stock', '>', 0)->count();
        $todayOrdersCount  = $restaurant->orders()->whereDate('created_at', today())->count();
        $pendingOrdersCount = $restaurant->orders()->where('status', 'pending')->count();
        $totalRevenue      = $restaurant->orders()->where('status', 'completed')->sum('total_price');

        // 5 pesanan terbaru yang masuk ke restoran ini
        $recentOrders = $restaurant->orders()
            ->with(['customer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('owner.dashboard', compact(
            'restaurant',
            'totalMenus',
            'availableMenus',
            'todayOrdersCount',
            'pendingOrdersCount',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
