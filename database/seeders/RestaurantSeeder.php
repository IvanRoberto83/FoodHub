<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            ['owner1@foodhub.test', 'Warung Nusantara',   'warung-nusantara',   'Masakan rumahan khas Nusantara dengan cita rasa autentik.'],
            ['owner2@foodhub.test', 'Ayam Geprek Mantap', 'ayam-geprek-mantap', 'Ayam geprek renyah dengan sambal level pedas pilihan.'],
            ['owner3@foodhub.test', 'Burger Kita',        'burger-kita',        'Burger dan kentang goreng dengan bahan segar setiap hari.'],
            ['owner4@foodhub.test', 'Kopi & Manis',       'kopi-manis',         'Kopi, minuman segar, dan dessert untuk menemani harimu.'],
        ];

        foreach ($restaurants as [$ownerEmail, $name, $slug, $description]) {
            $owner = User::where('email', $ownerEmail)->first();

            Restaurant::create([
                'user_id'     => $owner->id,
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'address'     => 'Jl. Raya ' . $name . ' No. ' . rand(1, 50) . ', Surabaya',
                'phone'       => '0313' . rand(1000000, 9999999),
                'image'       => 'https://picsum.photos/seed/resto-' . $slug . '/800/500',
                'is_open'     => true,
            ]);
        }
    }
}