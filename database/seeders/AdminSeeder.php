<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol admin si no existe
       $adminRole = SpatieRole::firstOrCreate(['name' => Role::ADMIN]);

        // Crear usuario admin solo si no existe
        $user = User::firstOrCreate(
            ['email' => 'admin@poolbook.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin1234'),
            ]
        );

        // Asignar rol
        if (!$user->hasRole($adminRole)) {
            $user->assignRole($adminRole);
        }
    }
}