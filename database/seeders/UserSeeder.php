<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Administrator',  'admin@foodhub.test',     'admin'],
            ['Budi Santoso',   'owner1@foodhub.test',    'owner'],
            ['Siti Aminah',    'owner2@foodhub.test',    'owner'],
            ['Andi Wijaya',    'owner3@foodhub.test',    'owner'],
            ['Dewi Lestari',   'owner4@foodhub.test',    'owner'],
            ['Rina Pelanggan', 'customer1@foodhub.test', 'customer'],
            ['Joko Pelanggan', 'customer2@foodhub.test', 'customer'],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => 'password',
                'role'     => $role,
                'phone'    => '0812' . rand(10000000, 99999999),
                'address'  => 'Jl. Contoh No. ' . rand(1, 99) . ', Surabaya',
            ]);
        }
    }
}