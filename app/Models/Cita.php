<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GrupoReserva;
use App\Models\Bono;
use App\Models\User;
use App\Enums\Role;
use Carbon\Carbon;

/**
 * Class Cita
 *
 * @property $id
 * @property $title
 * @property $start
 * @property $end
 * @property $resource_id
 * @property $user_id
 * @property $day_of_week
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cita extends Model
{
    protected $perPage = 20;

    protected $fillable = ['user_id', 'bono_id', 'grupo_reserva_id', 'title', 'day_of_week', 'date', 'start', 'end', 'resource_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grupoReserva(): BelongsTo
    {
        return $this->belongsTo(GrupoReserva::class);
    }

    public function bono(): BelongsTo
    {
        return $this->belongsTo(Bono::class);
    }

    /**
     * Comprueba todas las reglas de negocio para una reserva.
     * Devuelve null si es válida, o un string con el motivo si no lo es.
     * Único punto de verdad: lo usan CitaController, GeminiAssistantService
     * y ReservaRecurrenteService, para no repetir las reglas en 3 sitios.
     */
    public static function validarReserva(User $user, string $resourceId, Carbon $start, Carbon $end, string $fecha): ?string
    {
        if ($start->isBefore(Carbon::now())) {
            return 'No se pueden crear reservas para fechas u horas pasadas.';
        }

        if ($start->dayOfWeek === Carbon::SUNDAY) {
            return 'No hay servicio los domingos.';
        }

        if ($start->dayOfWeek === Carbon::SATURDAY && $start->hour >= 14) {
            return 'Los sábados el servicio termina a las 14:00.';
        }

        if ($start->hour < 9 || $start->hour >= 22) {
            return 'El horario es de 9:00 a 22:00 (sábados hasta las 14:00).';
        }

        $citasEseDia = self::where('user_id', $user->id)
            ->whereDate('date', $fecha)
            ->count();

        if ($citasEseDia > 0 && ! $user->hasRole(Role::ADMIN)) {
            return 'Ya tienes una reserva programada para este día.';
        }

        $existentesEnFranja = self::where('resource_id', $resourceId)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start', '<=', $start)->where('end', '>=', $end);
                    });
            })
            ->count();

        if ($existentesEnFranja >= 2) {
            return "Ya existen dos reservas en {$resourceId} a esa hora.";
        }

        return null;
    }
}
