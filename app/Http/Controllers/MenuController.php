<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categorySlug = $request->query('category');

        $categories = Category::withCount('menus')->get();

        $menus = Menu::with(['category', 'restaurant'])
            ->where('is_available', true)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($query, $categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('menus.index', compact('menus', 'categories', 'search', 'categorySlug'));
    }

    public function show(Menu $menu)
    {
        $menu->load(['category', 'restaurant']);

        $relatedMenus = Menu::with('category')
            ->where('category_id', $menu->category_id)
            ->where('id', '!=', $menu->id)
            ->where('is_available', true)
            ->take(3)
            ->get();

        return view('menus.show', compact('menu', 'relatedMenus'));
    }
}
