<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja pengguna.
     */
    public function index()
    {
        $cart = Cart::with(['restaurant', 'items.menu'])
            ->where('user_id', Auth::id())
            ->first();

        $subtotal = 0;
        if ($cart) {
            foreach ($cart->items as $item) {
                if ($item->menu) {
                    $subtotal += $item->menu->price * $item->quantity;
                }
            }
        }

        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * Tambahkan menu ke keranjang.
     */
    public function add(Request $request, Menu $menu)
    {
        if ($menu->restaurant && ! $menu->restaurant->is_open) {
            return back()->with('error', 'Maaf, restoran "' . $menu->restaurant->name . '" sedang tutup sementara.');
        }

        if (! $menu->is_available || $menu->stock < 1) {
            return back()->with('error', 'Maaf, menu sedang habis atau tidak tersedia.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        $force = $request->boolean('force_replace', false);

        $cart = Cart::where('user_id', Auth::id())->first();

        // Cek jika sudah ada keranjang dan dari restoran berbeda
        if ($cart && $cart->restaurant_id !== $menu->restaurant_id) {
            if (! $force) {
                return back()->with('cart_conflict', [
                    'message'       => 'Keranjang Anda saat ini berisi menu dari restoran lain (' . ($cart->restaurant->name ?? 'Restoran Sebelumnya') . '). Ingin mengosongkan keranjang lama dan memesan dari restoran ini?',
                    'new_menu_id'   => $menu->id,
                    'new_menu_name' => $menu->name,
                ]);
            }

            // Hapus keranjang lama jika disetujui
            $cart->items()->delete();
            $cart->delete();
            $cart = null;
        }

        // Buat keranjang jika belum ada
        if (! $cart) {
            $cart = Cart::create([
                'user_id'       => Auth::id(),
                'restaurant_id' => $menu->restaurant_id,
            ]);
        }

        // Cari atau buat item dalam keranjang
        $item = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($item) {
            $newQuantity = min($item->quantity + $quantity, $menu->stock);
            $item->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id'  => $cart->id,
                'menu_id'  => $menu->id,
                'quantity' => min($quantity, $menu->stock),
            ]);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Berhasil menambahkan ' . $menu->name . ' ke keranjang.');
    }

    /**
     * Update jumlah (quantity) item di keranjang.
     */
    public function update(Request $request, CartItem $item)
    {
        // Pastikan item milik user yang sedang login
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $quantity = (int) $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->remove($item);
        }

        $maxStock = $item->menu ? $item->menu->stock : 99;
        $finalQuantity = min($quantity, $maxStock);

        $item->update(['quantity' => $finalQuantity]);

        return back()->with('success', 'Jumlah porsi diperbarui.');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function remove(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart = $item->cart;
        $item->delete();

        // Jika keranjang sudah kosong, hapus keranjang
        if ($cart->items()->count() === 0) {
            $cart->delete();
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Tampilkan formulir checkout untuk semua menu di keranjang.
     */
    public function checkout()
    {
        $cart = Cart::with(['restaurant', 'items.menu'])
            ->where('user_id', Auth::id())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang Anda masih kosong. Silakan pilih menu terlebih dahulu.');
        }

        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->menu && $item->menu->is_available && $item->menu->stock > 0) {
                $subtotal += $item->menu->price * $item->quantity;
            }
        }

        return view('cart.checkout', compact('cart', 'subtotal'));
    }

    /**
     * Proses transaksi checkout dari keranjang belanja.
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:255'],
            'payment_method'   => ['required', 'string', 'in:cod,transfer,qris'],
        ], [
            'delivery_address.required' => 'Alamat pengantaran wajib diisi.',
            'payment_method.required'   => 'Pilih metode pembayaran.',
        ]);

        $cart = Cart::with(['restaurant', 'items.menu'])
            ->where('user_id', Auth::id())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang Anda kosong.');
        }

        if ($cart->restaurant && ! $cart->restaurant->is_open) {
            return redirect()->route('cart.index')
                ->with('error', 'Maaf, restoran "' . $cart->restaurant->name . '" sedang tutup sementara sehingga pesanan belum dapat diproses.');
        }

        return DB::transaction(function () use ($validated, $cart) {
            $totalPrice = 0;
            $itemsToProcess = [];

            // Kunci dan validasi stok tiap menu di server
            foreach ($cart->items as $cartItem) {
                $menu = Menu::where('id', $cartItem->menu_id)->lockForUpdate()->first();

                if (! $menu || ! $menu->is_available || $menu->stock < $cartItem->quantity) {
                    return redirect()->route('cart.index')
                        ->with('error', 'Stok menu "' . ($menu->name ?? 'Item') . '" tidak mencukupi saat checkout.');
                }

                $subtotal = $menu->price * $cartItem->quantity;
                $totalPrice += $subtotal;

                $itemsToProcess[] = [
                    'menu'      => $menu,
                    'quantity'  => $cartItem->quantity,
                    'price'     => $menu->price,
                    'subtotal'  => $subtotal,
                ];
            }

            // Buat order utama
            $orderCode = 'ORD-' . strtoupper(Str::random(4)) . '-' . date('YmdHis');
            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => Auth::id(),
                'restaurant_id'    => $cart->restaurant_id,
                'status'           => 'pending',
                'total_price'      => $totalPrice,
                'delivery_address' => $validated['delivery_address'],
                'notes'            => $validated['notes'] ?? null,
                'payment_method'   => $validated['payment_method'],
            ]);

            // Buat order_items dan kurangi stok menu
            foreach ($itemsToProcess as $itemData) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'menu_id'    => $itemData['menu']->id,
                    'menu_name'  => $itemData['menu']->name,
                    'price'      => $itemData['price'],
                    'quantity'   => $itemData['quantity'],
                    'subtotal'   => $itemData['subtotal'],
                ]);

                $itemData['menu']->decrement('stock', $itemData['quantity']);
                if ($itemData['menu']->fresh()->stock <= 0) {
                    $itemData['menu']->update(['is_available' => false]);
                }
            }

            // Hapus keranjang belanja setelah checkout berhasil
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('orders.my')
                ->with('success', 'Pesanan ' . $orderCode . ' berhasil dibuat!');
        });
    }
}
