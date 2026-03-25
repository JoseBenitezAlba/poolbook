<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Enums\Role;
use App\Models\User;



use Exception;
use Illuminate\Support\Facades\Gate;

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

     public function store(Request $request)
     {
         try {
             // Validación de datos
             $validatedData = $request->validate([
                 'title' => 'required|string|max:255',
                 'start' => 'required|date_format:Y-m-d\TH:i:s.v\Z',
                 'end' => 'required|date|after:start',
                 'resourceId' => 'required|string',
                 'extendedProps.day_of_week' => 'required|integer',
                 'extendedProps.date' => 'required|date',
             ]);

     
             // Obtener el usuario autenticado
             $user = $request->user();
             $startDateTime = Carbon::parse($validatedData['start'])->toDateTimeString();
             $endDateTime = Carbon::parse($validatedData['end'])->toDateTimeString();
             $date = $validatedData['extendedProps']['date'];
     
             // Verificar si la cita es para una fecha pasada
             if (Carbon::parse($date)->isBefore(Carbon::today())) {
                 return response()->json(['error' => 'No se pueden crear citas para días anteriores al de hoy.'], 400);
             }
     
             // Verificar si el usuario ya tiene una cita para ese día
             $userCitasForDate = Cita::where('user_id', $user->id)
                 ->whereDate('start', '=', $date)
                 ->count();
     
             // Verificar si el usuario ya tiene una cita para ese día
             if ($userCitasForDate > 0 && !$user->hasRole(Role::ADMIN)) {
                 // Si el usuario ya tiene una cita y no es administrador, retornar un error
                 return response()->json(['error' => 'Ya tienes una cita programada para este día.'], 409);
             }
     
             // Consulta para contar las citas existentes en el mismo carril y rango de tiempo
             $existingCitasCount = Cita::where('resource_id', $validatedData['resourceId'])
                 ->where(function ($query) use ($startDateTime, $endDateTime) {
                     $query->whereBetween('start', [$startDateTime, $endDateTime])
                         ->orWhereBetween('end', [$startDateTime, $endDateTime])
                         ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                             $query->where('start', '<=', $startDateTime)
                                 ->where('end', '>=', $endDateTime);
                         });
                 })
                 ->count();
     
             // Verificar si ya hay dos citas en el mismo horario y carril
             if ($existingCitasCount >= 2) {
                 // Aquí puedes manejar la respuesta en caso de que haya más de dos citas
                 return response()->json(['error' => 'Ya existen dos citas en ese horario y carril.'], 403);
             }
     
             // Crear y almacenar la cita
             $cita = new Cita([
                 'title' => $validatedData['title'],
                 'start' => $startDateTime,
                 'end' => $endDateTime,
                 'user_id' => $user->id,
                 'resource_id' => $validatedData['resourceId'],
                 'day_of_week' => $validatedData['extendedProps']['day_of_week'],
                 'date' => $date,
             ]);
     
             $cita->save();
     
             return response()->json(['id' => $cita->id], 201);
     
         } catch (Exception $e) {
             // Manejo de errores
             Log::error('Error al almacenar la cita: ' . $e->getMessage(), [
                 'userId' => $request->user()->id,
                 'request' => $request->all()
             ]);
             return response()->json(['error' => 'Error al almacenar la cita: ' . $e->getMessage()], 500);
         }
     }
     
     
    /**
     * Show the form for editing the specified resource.
     */
    public function getAllCitas()
    {
        try {
            $citas = Cita::all();
            
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
    public function destroy(Request $request, Cita $cita)
{
    $response = Gate::inspect('delete', $cita);

    if (!$response->allowed()) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'No tienes permisos para eliminar esta cita'], 403);
        } else {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar esta cita');
        }
    }

    $cita->delete();

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Cita eliminada con éxito'], 200);
    } else {
        return redirect()->back()->with('success', 'Cita eliminada con éxito');
    }
}

    
}    