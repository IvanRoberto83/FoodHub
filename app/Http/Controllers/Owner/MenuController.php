<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu makanan khusus milik restoran Owner.
     */
    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran Anda terlebih dahulu.');
        }

        $search     = $request->query('search');
        $categoryId = $request->query('category_id');
        $categories = Category::orderBy('name')->get();

        $menus = $restaurant->menus()
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        foreach ($menus as $item) {
            $item->image = $this->normalizeImageUrl($item->image, 'menus');
        }

        return view('owner.menus.index', compact('restaurant', 'menus', 'categories', 'search', 'categoryId'));
    }

    /**
     * Menampilkan form tambah menu baru untuk restoran milik Owner.
     */
    public function create()
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran Anda terlebih dahulu.');
        }

        $categories = Category::orderBy('name')->get();

        return view('owner.menus.create', compact('restaurant', 'categories'));
    }

    /**
     * Menyimpan menu baru dan mengikatnya langsung ke restoran milik Owner.
     */
    public function store(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran Anda terlebih dahulu.');
        }

        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'integer', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'name.required'        => 'Nama menu wajib diisi.',
            'price.required'       => 'Harga menu wajib diisi.',
            'stock.required'       => 'Stok porsi wajib diisi.',
        ]);

        $imageUrl = 'https://picsum.photos/seed/' . rand(100, 999) . '/600/400';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            if ($path === false) {
                return back()->withInput()->withErrors([
                    'image' => 'Foto menu gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $imageUrl = '/storage/' . $path;
        }

        $isAvailable = $request->has('is_available')
            ? $request->boolean('is_available')
            : true;

        if ((int) $validated['stock'] <= 0) {
            $isAvailable = false;
        }

        Menu::create([
            'restaurant_id' => $restaurant->id,
            'category_id'   => $validated['category_id'],
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'stock'         => $validated['stock'],
            'image'         => $imageUrl,
            'is_available'  => $isAvailable,
        ]);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('owner.menus.index')
            ->with('success', 'Menu "' . $validated['name'] . '" berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit menu (hanya jika menu milik restoran Owner).
     */
    public function edit(Menu $menu)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit');
        }

        $this->authorizeOwnership($menu, $restaurant);

        $menu->image = $this->normalizeImageUrl($menu->image, 'menus');
        $categories = Category::orderBy('name')->get();

        return view('owner.menus.edit', compact('restaurant', 'menu', 'categories'));
    }

    /**
     * Memperbarui informasi, harga, stok, foto, dan ketersediaan menu milik Owner.
     */
    public function update(Request $request, Menu $menu)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit');
        }

        $this->authorizeOwnership($menu, $restaurant);

        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'integer', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'name.required'        => 'Nama menu wajib diisi.',
            'price.required'       => 'Harga menu wajib diisi.',
            'stock.required'       => 'Stok porsi wajib diisi.',
        ]);

        $oldImage = $menu->image;
        $imageUrl = $menu->image;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            if ($path === false) {
                return back()->withInput()->withErrors([
                    'image' => 'Foto menu gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $imageUrl = '/storage/' . $path;
        } elseif ($request->boolean('remove_image')) {
            $imageUrl = 'https://picsum.photos/seed/' . Str::slug($validated['name']) . '/600/400';
        }

        $isAvailable = $request->has('is_available')
            ? $request->boolean('is_available')
            : $menu->is_available;

        if ((int) $validated['stock'] <= 0) {
            $isAvailable = false;
        }

        $menu->update([
            'category_id'  => $validated['category_id'],
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'image'        => $imageUrl,
            'is_available' => $isAvailable,
        ]);

        $oldImageRemoved = $imageUrl !== $oldImage
            ? $this->deleteStoredImage($oldImage, 'menus')
            : true;

        if (! $oldImageRemoved) {
            return redirect()->route('owner.menus.index')
                ->with('error', 'Menu berhasil diperbarui, tetapi foto lama gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('owner.menus.index')
            ->with('success', 'Menu "' . $menu->name . '" berhasil diperbarui!');
    }

    /**
     * Mengubah ketersediaan menu secara cepat langsung dari tabel daftar menu.
     */
    public function toggleAvailability(Menu $menu)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit');
        }

        $this->authorizeOwnership($menu, $restaurant);

        if (! $menu->is_available && $menu->stock <= 0) {
            return back()->with('error', 'Menu "' . $menu->name . '" tidak bisa diaktifkan karena stok masih 0.');
        }

        $menu->update([
            'is_available' => ! $menu->is_available,
        ]);

        $statusLabel = $menu->is_available ? 'Tersedia' : 'Tidak Tersedia';

        return back()->with('success', 'Status menu "' . $menu->name . '" diubah menjadi ' . $statusLabel . '.');
    }

    /**
     * Menghapus menu milik restoran Owner beserta file foto lokalnya.
     */
    public function destroy(Menu $menu)
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit');
        }

        $this->authorizeOwnership($menu, $restaurant);

        $menuName = $menu->name;
        $image    = $menu->image;
        $menu->delete();

        Cache::forget('admin_dashboard_stats');

        if (! $this->deleteStoredImage($image, 'menus')) {
            return redirect()->route('owner.menus.index')
                ->with('error', 'Menu berhasil dihapus, tetapi foto menu gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('owner.menus.index')
            ->with('success', 'Menu "' . $menuName . '" berhasil dihapus!');
    }

    /**
     * Validasi keamanan: pastikan menu benar-benar milik restoran Owner yang sedang login.
     */
    private function authorizeOwnership(Menu $menu, Restaurant $restaurant): void
    {
        if ($menu->restaurant_id !== $restaurant->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses atau mengubah menu restoran lain.');
        }
    }

    private function deleteStoredImage(?string $image, string $directory): bool
    {
        if (! $image) {
            return true;
        }

        $imagePath = parse_url($image, PHP_URL_PATH);
        $urlPrefix = '/storage/' . $directory . '/';

        if (! is_string($imagePath) || ! str_starts_with($imagePath, $urlPrefix)) {
            return true;
        }

        $storagePath = substr($imagePath, strlen('/storage/'));
        if (str_contains($storagePath, '..')) {
            return true;
        }

        return Storage::disk('public')->delete($storagePath);
    }

    private function normalizeImageUrl(?string $image, string $directory): ?string
    {
        if (! $image) {
            return $image;
        }

        $imagePath = parse_url($image, PHP_URL_PATH);
        $urlPrefix = '/storage/' . $directory . '/';

        if (! is_string($imagePath) || ! str_starts_with($imagePath, $urlPrefix)) {
            return $image;
        }

        return $imagePath;
    }
}
