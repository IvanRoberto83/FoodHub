<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache statistik statis selama 5 menit (300 detik) agar tidak query berulang
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'totalCategories'  => Category::count(),
                'totalMenus'       => Menu::count(),
                'todayOrdersCount' => Order::whereDate('created_at', today())->count(),
                'totalRevenue'     => Order::where('status', 'completed')->sum('total_price'),
            ];
        });

        // Recent orders hanya 5 data, tidak perlu cache panjang
        $recentOrders = Order::with(['customer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', array_merge($stats, compact('recentOrders')));
    }
}
