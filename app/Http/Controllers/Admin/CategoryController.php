<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menus')->latest()->paginate(10);
        foreach ($categories as $category) {
            $category->image = $this->normalizeImageUrl($category->image, 'categories');
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100', 'unique:categories,name'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
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

        $image = 'https://picsum.photos/seed/' . $slug . '/300/300';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            if ($path === false) {
                return back()->withInput()->withErrors([
                    'image' => 'Foto kategori gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $image = '/storage/' . $path;
        }

        Category::create([
            'name'  => $validated['name'],
            'slug'  => $slug,
            'image' => $image,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Kategori dengan nama ini sudah ada.',
        ]);

        $slug = Str::slug($validated['name']);
        $oldImage = $category->image;
        $image = $oldImage;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            if ($path === false) {
                return back()->withInput()->withErrors([
                    'image' => 'Foto kategori gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $image = '/storage/' . $path;
        } elseif ($request->boolean('remove_image')) {
            $image = 'https://picsum.photos/seed/' . $slug . '/300/300';
        }

        $category->update([
            'name'  => $validated['name'],
            'slug'  => $slug,
            'image' => $image,
        ]);

        $oldImageRemoved = $image !== $oldImage
            ? $this->deleteStoredImage($oldImage, 'categories')
            : true;

        if (!$oldImageRemoved) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori berhasil diperbarui, tetapi foto lama gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori ' . $category->name . ' berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->menus()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu makanan.');
        }

        $image = $category->image;
        $category->delete();

        if (!$this->deleteStoredImage($image, 'categories')) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori berhasil dihapus, tetapi foto kategori gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
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
