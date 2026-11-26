<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categories  = Category::pluck('id', 'slug');
        $restaurants = Restaurant::pluck('id', 'slug');

        $menus = [
            ['warung-nusantara', 'nasi',      'Nasi Goreng Spesial', 25000, 'Nasi goreng dengan telur, ayam suwir, dan kerupuk.', true],
            ['warung-nusantara', 'nasi',      'Nasi Rawon',          28000, 'Rawon daging sapi dengan kuah hitam khas Jawa Timur.', true],
            ['warung-nusantara', 'minuman',   'Es Teh Manis',         5000, 'Teh manis dingin yang menyegarkan.', true],

            ['ayam-geprek-mantap', 'ayam',    'Ayam Geprek Original', 22000, 'Ayam crispy digeprek dengan sambal bawang.', true],
            ['ayam-geprek-mantap', 'ayam',    'Ayam Geprek Keju',     27000, 'Ayam geprek dengan topping keju mozzarella.', true],
            ['ayam-geprek-mantap', 'nasi',    'Nasi Putih',            5000, 'Nasi putih hangat.', true],
            ['ayam-geprek-mantap', 'mie',     'Mie Geprek Pedas',     20000, 'Mie goreng dengan ayam geprek (sedang habis).', false],

            ['burger-kita', 'fast-food',      'Cheese Burger',        30000, 'Burger daging sapi dengan keju leleh.', true],
            ['burger-kita', 'fast-food',      'Kentang Goreng',       15000, 'Kentang goreng renyah dengan saus pilihan.', true],
            ['burger-kita', 'minuman',        'Lemon Tea',             8000, 'Teh lemon dingin.', true],

            ['kopi-manis', 'minuman',         'Kopi Susu Gula Aren',  18000, 'Espresso, susu segar, dan gula aren.', true],
            ['kopi-manis', 'dessert',         'Pancake Coklat',       22000, 'Pancake lembut dengan saus coklat.', true],
            ['kopi-manis', 'dessert',         'Es Krim Vanila',       12000, 'Dua scoop es krim vanila.', true],
        ];

        foreach ($menus as [$restoSlug, $catSlug, $name, $price, $desc, $available]) {
            Menu::create([
                'restaurant_id' => $restaurants[$restoSlug],
                'category_id'   => $categories[$catSlug],
                'name'          => $name,
                'description'   => $desc,
                'price'         => $price,
                'image'         => 'https://picsum.photos/seed/' . Str::slug($name) . '/600/400',
                'stock'         => $available ? 50 : 0,
                'is_available'  => $available,
            ]);
        }
    }
}