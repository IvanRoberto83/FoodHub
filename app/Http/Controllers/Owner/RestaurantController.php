<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Menampilkan form pengaturan informasi & status operasional restoran milik Owner.
     */
    public function edit()
    {
        $restaurant = Auth::user()->restaurant;

        if ($restaurant) {
            $restaurant->image = $this->normalizeImageUrl($restaurant->image, 'restaurants');
        }

        return view('owner.restaurant.edit', compact('restaurant'));
    }

    /**
     * Menyimpan profil restoran baru atau memperbarui profil restoran milik Owner.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'address'      => ['required', 'string', 'max:500'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_open'      => ['nullable', 'boolean'],
        ], [
            'name.required'    => 'Nama restoran wajib diisi.',
            'address.required' => 'Alamat lengkap restoran wajib diisi.',
            'image.image'      => 'File yang diunggah harus berupa gambar.',
            'image.max'        => 'Ukuran foto restoran maksimal 2MB.',
        ]);

        $restaurant = Auth::user()->restaurant;
        $slug = $this->generateUniqueSlug($validated['name'], $restaurant?->id);

        $oldImage = $restaurant?->image;
        $defaultImage = 'https://picsum.photos/seed/resto-' . $slug . '/800/500';
        $imageUrl = $oldImage ?? $defaultImage;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('restaurants', 'public');
            if ($path === false) {
                return back()->withInput()->withErrors([
                    'image' => 'Foto restoran gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $imageUrl = '/storage/' . $path;
        } elseif ($request->boolean('remove_image')) {
            $imageUrl = $defaultImage;
        }

        $isOpen = $request->has('is_open') ? $request->boolean('is_open') : true;

        $payload = [
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'address'     => $validated['address'],
            'phone'       => $validated['phone'] ?? null,
            'image'       => $imageUrl,
            'is_open'     => $isOpen,
        ];

        if ($restaurant) {
            $restaurant->update($payload);
            if ($oldImage && $imageUrl !== $oldImage) {
                $this->deleteStoredImage($oldImage, 'restaurants');
            }
            $message = 'Informasi restoran berhasil diperbarui!';
        } else {
            $payload['user_id'] = Auth::id();
            Restaurant::create($payload);
            $message = 'Profil restoran berhasil dibuat! Sekarang Anda dapat menambahkan menu.';
        }

        return redirect()->route('owner.restaurant.edit')
            ->with('success', $message);
    }

    /**
     * Mengubah status operasional Buka / Tutup restoran secara cepat (Quick Toggle).
     */
    public function toggleStatus()
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant) {
            return redirect()->route('owner.restaurant.edit')
                ->with('error', 'Silakan lengkapi profil restoran terlebih dahulu.');
        }

        $restaurant->update([
            'is_open' => ! $restaurant->is_open,
        ]);

        $label = $restaurant->is_open ? 'BUKA (Siap menerima pesanan)' : 'TUTUP sementara';

        return back()->with('success', "Status operasional restoran diubah menjadi: {$label}.");
    }

    /**
     * Membuat slug unik agar tidak bentrok dengan restoran lain.
     */
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'restoran';
        $slug = $baseSlug;
        $counter = 1;

        while (
            Restaurant::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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
