<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Nasi',      'nasi'],
            ['Ayam',      'ayam'],
            ['Mie',       'mie'],
            ['Fast Food', 'fast-food'],
            ['Minuman',   'minuman'],
            ['Dessert',   'dessert'],
        ];

        foreach ($categories as [$name, $slug]) {
            Category::create([
                'name'  => $name,
                'slug'  => $slug,
                'image' => 'https://picsum.photos/seed/cat-' . $slug . '/300/300',
            ]);
        }
    }
}