<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'restaurant'])->latest()->paginate(10);
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
            $imageUrl = Storage::url($path);
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
            'is_available'  => ['nullable', 'boolean'],
        ]);

        $imageUrl = $menu->image;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $imageUrl = Storage::url($path);
        }

        $menu->update([
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
            ->with('success', 'Menu ' . $menu->name . ' berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu makanan berhasil dihapus!');
    }
}
