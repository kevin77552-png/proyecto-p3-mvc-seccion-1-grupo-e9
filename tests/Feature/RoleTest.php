<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_gerente_role()
    {
        $now = now();
        DB::table('users')->insert([
            'name' => 'Test Gerente',
            'email' => 'testgerente@example.com',
            'password' => Hash::make('password'),
            'role' => 'gerente',
            'activo' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testgerente@example.com',
            'role' => 'gerente',
        ]);
    }
}
