<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan seluruh daftar pesanan yang masuk ke restoran milik Owner.
     */
    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran Anda terlebih dahulu.');
        }

        $status = $request->query('status');

        // Hitung jumlah pesanan per status untuk lencana (badge) pada tab filter
        $rawCounts = $restaurant->orders()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = [
            'all'        => $rawCounts->sum(),
            'pending'    => $rawCounts->get('pending', 0),
            'processing' => $rawCounts->get('processing', 0),
            'completed'  => $rawCounts->get('completed', 0),
            'cancelled'  => $rawCounts->get('cancelled', 0),
        ];

        $orders = $restaurant->orders()
            ->with(['customer', 'items'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.orders.index', compact('restaurant', 'orders', 'status', 'statusCounts'));
    }

    /**
     * Memproses dan memperbarui status pesanan yang masuk ke restoran Owner.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant || $order->restaurant_id !== $restaurant->id) {
            abort(403, 'Anda tidak memiliki hak untuk memproses pesanan dari restoran lain.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->with('error', 'Pesanan yang telah dibatalkan tidak dapat diproses kembali.');
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            $order->update(['status' => $newStatus]);

            // Jika pesanan dibatalkan dari status aktif, kembalikan stok ke masing-masing menu
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->menu_id) {
                        $menu = Menu::find($item->menu_id);
                        if ($menu) {
                            $menu->increment('stock', $item->quantity);
                            if (! $menu->is_available && $menu->stock > 0) {
                                $menu->update(['is_available' => true]);
                            }
                        }
                    }
                }
            }
        });

        Cache::forget('admin_dashboard_stats');

        $labels = [
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ];

        return back()->with(
            'success',
            'Status pesanan ' . $order->order_code . ' berhasil diperbarui menjadi ' . ($labels[$newStatus] ?? ucfirst($newStatus)) . '!'
        );
    }
}
