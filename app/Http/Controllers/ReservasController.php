<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva; // Asegúrate de importar el modelo Reserva
use App\Enums\Role;
use App\Models\Cita;


class ReservasController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el usuario actual
        $user = $request->user();

        // Verificar el rol del usuario
        if ($user && $user->hasRole(Role::ADMIN)) {
            // Si es un administrador, recuperar las reservas de todos los usuarios
            $citas = cita::all();
            return view('admin.reservas', compact('citas'));
        } else {
            // Si no es un administrador, recuperar las reservas solo del usuario actual
            $citas = cita::where('user_id', $user->id)->get();
            return view('usuarios.reservas', compact('citas'));
        }
    }
}
