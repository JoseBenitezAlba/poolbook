<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::updateOrCreate(
            ['email' => 'prueba@prueba.com'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('prueba'),
            ]
        );
        if (! $usuario->hasRole(Role::USUARIO)) {
            $usuario->assignRole(Role::USUARIO);
        }

        // Le damos un bono con 999.999 sesiones y sin fecha de caducidad,
        // para que a efectos prácticos nunca se quede sin sesiones al
        // probar el calendario o el asistente. 
       
        $bonoDemo = $usuario->bonos()->firstOrCreate(
            ['sesiones_adquiridas' => 999999],
            [
                'sesiones_gastadas' => 0,
                'fecha_caducidad' => null,
            ]
        );
        $bonoDemo->activo = true;
        $bonoDemo->save();
    }
}