<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManagerUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Gerente',
            'email' => 'manager@example.com',
            'password' => Hash::make('manager123'),
            'role' => 'gerente',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
