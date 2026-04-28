<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'superadmin@example.com';

        if (!User::where('email', $email)->exists()) {
            User::create([
                'name' => 'Super Administrador',
                'email' => $email,
                'password' => Hash::make('Secret123!'),
                'role' => 'superadministrador',
                'activo' => true,
            ]);
        }
    }
}
