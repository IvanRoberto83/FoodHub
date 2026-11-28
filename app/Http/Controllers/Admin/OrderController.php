<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['customer', 'restaurant', 'items'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            $order->update(['status' => $newStatus]);

            // Jika status diubah ke cancelled dari non-cancelled, pulihkan stok
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

        return back()->with('success', 'Status pesanan ' . $order->order_code . ' berhasil diubah menjadi ' . ucfirst($newStatus) . '!');
    }
}
