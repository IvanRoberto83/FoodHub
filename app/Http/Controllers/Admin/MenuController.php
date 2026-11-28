<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'restaurant'])->latest()->paginate(10);
        foreach ($menus as $menu) {
            $menu->image = $this->normalizeImageUrl($menu->image, 'menus');
        }

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all();
        $restaurants = Restaurant::all();
        return view('admin.menus.create', compact('categories', 'restaurants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'category_id'   => ['required', 'exists:categories,id'],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'integer', 'min:0'],
            'stock'         => ['required', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available'  => ['nullable', 'boolean'],
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

        Menu::create([
            'restaurant_id' => $validated['restaurant_id'],
            'category_id'   => $validated['category_id'],
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'stock'         => $validated['stock'],
            'image'         => $imageUrl,
            'is_available'  => $request->boolean('is_available', true),
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu makanan baru berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        $restaurants = Restaurant::all();
        $menu->image = $this->normalizeImageUrl($menu->image, 'menus');

        return view('admin.menus.edit', compact('menu', 'categories', 'restaurants'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'category_id'   => ['required', 'exists:categories,id'],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'integer', 'min:0'],
            'stock'         => ['required', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image'  => ['nullable', 'boolean'],
            'is_available'  => ['nullable', 'boolean'],
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

        $menu->update([
            'restaurant_id' => $validated['restaurant_id'],
            'category_id'   => $validated['category_id'],
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'stock'          => $validated['stock'],
            'image'         => $imageUrl,
            'is_available'  => $request->boolean('is_available', true),
        ]);

        $oldImageRemoved = $imageUrl !== $oldImage
            ? $this->deleteStoredImage($oldImage, 'menus')
            : true;

        if (!$oldImageRemoved) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Menu berhasil diperbarui, tetapi foto lama gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu ' . $menu->name . ' berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        $image = $menu->image;
        $menu->delete();

        if (!$this->deleteStoredImage($image, 'menus')) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Menu berhasil dihapus, tetapi foto menu gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu makanan berhasil dihapus!');
    }

    private function deleteStoredImage(?string $image, string $directory): bool
    {
        if (!$image) {
            return true;
        }

        $imagePath = parse_url($image, PHP_URL_PATH);
        $urlPrefix = '/storage/' . $directory . '/';

        if (!is_string($imagePath) || !str_starts_with($imagePath, $urlPrefix)) {
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
        if (!$image) {
            return $image;
        }

        $imagePath = parse_url($image, PHP_URL_PATH);
        $urlPrefix = '/storage/' . $directory . '/';

        if (!is_string($imagePath) || !str_starts_with($imagePath, $urlPrefix)) {
            return $image;
        }

        return $imagePath;
    }
}
