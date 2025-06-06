<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe un admin
        if (!User::where('is_admin', true)->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@onubabus.es',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
