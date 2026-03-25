<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Enums\Role as RoleEnum;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
    $roleUser = Role::firstOrCreate(['name' => 'Usuario']);

    $permissions = [
        'view-calendar',
        'create-cita',
        'delete-own-cita',
        'delete-any-cita'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $roleAdmin->syncPermissions([
        'view-calendar',
        'create-cita',
        'delete-own-cita',
        'delete-any-cita'
    ]);

    $roleUser->syncPermissions([
        'view-calendar',
        'create-cita',
        'delete-own-cita'
    ]);
}
}
