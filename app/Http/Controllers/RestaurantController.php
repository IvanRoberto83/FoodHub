<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Menampilkan daftar seluruh restoran mitra FoodHub di halaman publik.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $onlyOpen = $request->boolean('open');

        $restaurants = Restaurant::withCount([
            'menus' => fn ($query) => $query->where('is_available', true)->where('stock', '>', 0),
        ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($onlyOpen, fn ($query) => $query->where('is_open', true))
            ->orderByDesc('is_open')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('restaurants.index', compact('restaurants', 'search', 'onlyOpen'));
    }

    /**
     * Menampilkan profil detail restoran beserta daftar menu makanan miliknya.
     */
    public function show(Request $request, Restaurant $restaurant)
    {
        $categorySlug = $request->query('category');
        $search = $request->query('search');

        // Ambil kategori yang dimiliki oleh menu di restoran ini
        $categories = Category::whereHas('menus', function ($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->get();

        $menus = $restaurant->menus()
            ->with('category')
            ->when($categorySlug, function ($query, $categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('is_available')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('restaurants.show', compact('restaurant', 'categories', 'menus', 'categorySlug', 'search'));
    }
}
