<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bono;
use App\Enums\Role;
use App\Models\Cita;

class AdminUserController extends Controller
{
    // Método para mostrar el formulario de creación de usuarios
    public function create()
    {
        return view('admin.create');
    }

    // Método para almacenar un nuevo usuario, con el rol que se elija en el formulario
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . Role::ADMIN . ',' . Role::USUARIO,
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Asignar el rol elegido en el formulario (antes siempre era Admin,
        // ignorando por completo lo que se seleccionara)
        $user->assignRole($validatedData['role']);

        // Redireccionar a alguna vista o acción
        return redirect()->route('dashboard')->with('success', 'Usuario creado correctamente');
    }



    public function index()
    {
        // Cargamos también los bonos activos (y no caducados) de cada usuario,
        // para poder mostrar en el listado cuántas sesiones le quedan sin
        // tener que entrar al detalle uno a uno.
        $users = User::with(['bonos' => function ($query) {
            $query->where('activo', true)
                ->where(function ($q) {
                    $q->whereNull('fecha_caducidad')
                        ->orWhereDate('fecha_caducidad', '>=', now()->toDateString());
                });
        }])->get();

        return view('admin.index', compact('users'));
    }

    /**
     * Detalle de un usuario: sus bonos (todos, no solo los activos) y su
     * historial de reservas, más el formulario para añadirle un bono nuevo.
     */
    public function show(User $user)
    {
        $bonos = $user->bonos()->orderByDesc('created_at')->get();

        $citas = $user->citas()
            ->orderByDesc('start')
            ->limit(20)
            ->get();

        return view('admin.users.show', compact('user', 'bonos', 'citas'));
    }

    /**
     * Crear un bono nuevo para un usuario.
     */
    public function storeBono(Request $request, User $user)
    {
        $validated = $request->validate([
            'sesiones_adquiridas' => ['required', 'integer', 'min:1'],
            'fecha_caducidad' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $bono = $user->bonos()->create([
            'sesiones_adquiridas' => $validated['sesiones_adquiridas'],
            'sesiones_gastadas' => 0,
            'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
        ]);

        // 'activo' no está en $fillable de Bono, así que create() lo ignora.
        // Lo fijamos explícitamente para no depender de un valor por defecto
        // en la base de datos.
        $bono->activo = true;
        $bono->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Bono añadido correctamente.');
    }

    /**
     * Desactivar (no borrar físicamente) un bono. Igual que en storeBono,
     * usamos forceFill porque 'activo' no está en $fillable.
     */
    public function desactivarBono(Bono $bono)
    {
        $bono->forceFill(['activo' => false])->save();

        return redirect()
            ->route('admin.users.show', $bono->user_id)
            ->with('success', 'Bono desactivado.');
    }

    /**
     * Eliminar un bono por completo. Solo si no tiene citas asociadas,
     * para no romper la relación Cita::bono() / BonoService::devolverSesion.
     */
    public function destroyBono(Bono $bono)
    {
        $userId = $bono->user_id;

        $tieneCitas = Cita::where('bono_id', $bono->id)->exists();

        if ($tieneCitas) {
            return redirect()
                ->route('admin.users.show', $userId)
                ->with('error', 'Este bono tiene citas asociadas: desactívalo en vez de eliminarlo.');
        }

        $bono->delete();

        return redirect()
            ->route('admin.users.show', $userId)
            ->with('success', 'Bono eliminado.');
    }
}