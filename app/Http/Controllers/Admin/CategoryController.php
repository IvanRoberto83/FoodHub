<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menus')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100', 'unique:categories,name'],
            'image' => ['nullable', 'url', 'max:255'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Kategori dengan nama ini sudah ada.',
        ]);

        $slug = Str::slug($validated['name']);
        // Ensure slug is unique
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        Category::create([
            'name'  => $validated['name'],
            'slug'  => $slug,
            'image' => $validated['image'] ?? 'https://picsum.photos/seed/' . $slug . '/300/300',
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'image' => ['nullable', 'url', 'max:255'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Kategori dengan nama ini sudah ada.',
        ]);

        $slug = Str::slug($validated['name']);

        $category->update([
            'name'  => $validated['name'],
            'slug'  => $slug,
            'image' => $validated['image'] ?? $category->image,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori ' . $category->name . ' berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->menus()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu makanan.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
