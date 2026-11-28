<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Menu $menu)
    {
        if (! $menu->is_available || $menu->stock < 1) {
            return redirect()->route('menus.show', $menu)
                ->with('error', 'Maaf, menu ini sedang habis atau tidak tersedia.');
        }

        $menu->load(['category', 'restaurant']);

        return view('orders.create', compact('menu'));
    }

    public function store(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'quantity'         => ['required', 'integer', 'min:1', 'max:' . max(1, $menu->stock)],
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:255'],
            'payment_method'   => ['required', 'string', 'in:cod,transfer,qris'],
        ], [
            'quantity.required'         => 'Jumlah pesanan wajib diisi.',
            'quantity.max'              => 'Jumlah melebihi stok yang tersedia (:max).',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
            'payment_method.required'   => 'Pilih metode pembayaran.',
        ]);

        return DB::transaction(function () use ($validated, $menu) {
            // Lock row menu for update
            $freshMenu = Menu::where('id', $menu->id)->lockForUpdate()->first();

            if (! $freshMenu->is_available || $freshMenu->stock < $validated['quantity']) {
                return redirect()->route('menus.show', $menu)
                    ->with('error', 'Maaf, stok menu tidak mencukupi saat checkout diproses.');
            }

            $quantity = (int) $validated['quantity'];
            $price = (int) $freshMenu->price;
            $totalPrice = $price * $quantity;
            $orderCode = 'ORD-' . strtoupper(Str::random(4)) . '-' . date('YmdHis');

            // 1. Buat pesanan utama
            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => Auth::id(),
                'restaurant_id'    => $freshMenu->restaurant_id,
                'status'           => 'pending',
                'total_price'      => $totalPrice,
                'delivery_address' => $validated['delivery_address'],
                'notes'            => $validated['notes'] ?? null,
                'payment_method'   => $validated['payment_method'],
            ]);

            // 2. Buat detail item
            OrderItem::create([
                'order_id'   => $order->id,
                'menu_id'    => $freshMenu->id,
                'menu_name'  => $freshMenu->name,
                'price'      => $price,
                'quantity'   => $quantity,
                'subtotal'   => $totalPrice,
            ]);

            // 3. Potong stok
            $freshMenu->decrement('stock', $quantity);
            if ($freshMenu->fresh()->stock <= 0) {
                $freshMenu->update(['is_available' => false]);
            }

            return redirect()->route('orders.my')
                ->with('success', 'Pesanan ' . $orderCode . ' berhasil dibuat!');
        });
    }

    public function myOrders()
    {
        $orders = Order::with(['items', 'restaurant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.my-orders', compact('orders'));
    }

    public function cancel(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa membatalkan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan pesanan ini.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses atau selesai.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Kembalikan stok menu
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
        });

        return back()->with('success', 'Pesanan ' . $order->order_code . ' berhasil dibatalkan.');
    }
}
