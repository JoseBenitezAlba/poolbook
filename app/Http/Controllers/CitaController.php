<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Http\Requests\CitaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Enums\Role;
use App\Services\BonoService;



use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

/**
 * Class CitaController
 * @package App\Http\Controllers
 */
class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $citas = Cita::all();
       

        return response()->json($citas);
    }


    /**
     * Store a newly created resource in storage.
     */

     public function store(CitaRequest $request, BonoService $bonoService)
    {
        try {
            $validatedData = $request->validated();

            $user = $request->user();
            // El calendario envía las fechas con sufijo "Z" (formato ISO), pero esos
            // componentes de hora YA representan la hora local de Madrid (así es como
            // FullCalendar codifica las fechas cuando se usa timeZone: 'Europe/Madrid').
            // NO son un instante UTC real, así que no hay que convertir desde UTC:
            // basta con quitar la "Z" e indicarle a Carbon que interprete esos
            // componentes directamente en la zona horaria de Madrid.
            $startDateTime = Carbon::parse(rtrim($validatedData['start'], 'Z'), 'Europe/Madrid');
            $endDateTime = Carbon::parse(rtrim($validatedData['end'], 'Z'), 'Europe/Madrid');
            $date = $validatedData['extendedProps']['date'];

            $error = Cita::validarReserva($user, $validatedData['resourceId'], $startDateTime, $endDateTime, $date);

            if ($error) {
                return response()->json(['error' => $error], 409);
            }

            $cita = DB::transaction(function () use ($user, $bonoService, $validatedData, $startDateTime, $endDateTime, $date) {
                $bono = $bonoService->consumirSesion($user);

                if (! $bono) {
                    return null;
                }

                return Cita::create([
                    'title' => $user->name,
                    'start' => $startDateTime->toDateTimeString(),
                    'end' => $endDateTime->toDateTimeString(),
                    'user_id' => $user->id,
                    'bono_id' => $bono->id,
                    'resource_id' => $validatedData['resourceId'],
                    'day_of_week' => $validatedData['extendedProps']['day_of_week'],
                    'date' => $date,
                ]);
            });

            if (! $cita) {
                return response()->json(['error' => 'No tienes sesiones disponibles en un bono válido.'], 409);
            }

            return response()->json(['id' => $cita->id], 201);

        } catch (Exception $e) {
            Log::error('Error al almacenar la cita: ' . $e->getMessage(), [
                'userId' => $request->user()->id,
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Error al almacenar la cita: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource (mover una reserva a otro día/hora/carril).
     * No toca el bono: sigue siendo la misma sesión ya consumida, solo cambia
     * cuándo y dónde es la cita.
     */
    public function update(CitaRequest $request, Cita $cita)
    {
        $response = Gate::inspect('update', $cita);

        if (! $response->allowed()) {
            return response()->json(['error' => 'No tienes permisos para modificar esta cita'], 403);
        }

        try {
            $validatedData = $request->validated();
            $user = $request->user();

            $startDateTime = Carbon::parse(rtrim($validatedData['start'], 'Z'), 'Europe/Madrid');
            $endDateTime = Carbon::parse(rtrim($validatedData['end'], 'Z'), 'Europe/Madrid');
            $date = $validatedData['extendedProps']['date'];

            // Le pasamos el id de la propia cita para que la validación no
            // choque consigo misma (ver comentario en Cita::validarReserva).
            $error = Cita::validarReserva($user, $validatedData['resourceId'], $startDateTime, $endDateTime, $date, $cita->id);

            if ($error) {
                return response()->json(['error' => $error], 409);
            }

            $cita->update([
                'start' => $startDateTime->toDateTimeString(),
                'end' => $endDateTime->toDateTimeString(),
                'resource_id' => $validatedData['resourceId'],
                'day_of_week' => $validatedData['extendedProps']['day_of_week'],
                'date' => $date,
            ]);

            return response()->json(['id' => $cita->id], 200);

        } catch (Exception $e) {
            Log::error('Error al modificar la cita: ' . $e->getMessage(), [
                'userId' => $request->user()->id,
                'citaId' => $cita->id,
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Error al modificar la cita: ' . $e->getMessage()], 500);
        }
    }
     
     
    /**
     * Show the form for editing the specified resource.
     */
    public function getAllCitas()
    {
        try {
            $currentUser = Auth::user();
            // Un admin puede ver de quién es cada reserva y gestionarlas todas;
            // un usuario normal solo ve "Ocupado" en las que no son suyas y
            // solo puede gestionar (cancelar) las suyas propias.
            $esAdmin = $currentUser !== null && $currentUser->hasRole(Role::ADMIN);

            $citas = Cita::all()->map(function (Cita $cita) use ($currentUser, $esAdmin) {
                $esPropietaria = $currentUser !== null && $cita->user_id === $currentUser->id;
                $puedeGestionar = $esPropietaria || $esAdmin;

                return [
                    'id' => $cita->id,
                    'title' => $esPropietaria
                        ? 'Mi reserva'
                        : ($esAdmin ? $cita->title : 'Ocupado'),
                    'start' => $cita->start,
                    'end' => $cita->end,
                    'resource_id' => $cita->resource_id,
                    'day_of_week' => $cita->day_of_week,
                    'date' => $cita->date,
                    'es_propietaria' => $esPropietaria,
                    'puede_gestionar' => $puedeGestionar,
                ];
            });

            return response()->json($citas);
        } catch (Exception $e) {
            Log::error('Error al obtener las citas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las citas'], 500);
        }
    }
  
    
        // Obtener el usuario autenticado
        public function reservasUsuario(Request $request)
        {
            // Obtener el usuario autenticado
            $user = Auth::user();
        
            // Obtener las reservas del usuario actual
            $reservas = Cita::where('user_id', $user->id)->get();
        
            // Devolver las reservas en formato JSON
            return response()->json($reservas);
        }
        
    /**
     * Remove the specified resource from storage.
     */
public function destroy(Request $request, Cita $cita, BonoService $bonoService)
{
    $response = Gate::inspect('delete', $cita);

    if (!$response->allowed()) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'No tienes permisos para eliminar esta cita'], 403);
        } else {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar esta cita');
        }
    }

    DB::transaction(function () use ($cita, $bonoService) {
        if ($cita->bono) {
            $bonoService->devolverSesion($cita->bono);
        }

        $cita->delete();
    });

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Cita eliminada con éxito'], 200);
    } else {
        return redirect()->back()->with('success', 'Cita eliminada con éxito');
    }
}

    
}