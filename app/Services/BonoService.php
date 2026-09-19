<?php

namespace App\Services;

use App\Models\Bono;
use App\Models\User;
use Carbon\Carbon;

class BonoService
{
    /**
     * Consume una sesión del primer bono válido con saldo.
     * Debe ejecutarse dentro de la transacción de creación de la reserva.
     */
    public function consumirSesion(User $user): ?Bono
    {
        $bono = $user->bonos()
            ->where('activo', true)
            ->whereColumn('sesiones_gastadas', '<', 'sesiones_adquiridas')
            ->where(function ($query) {
                $query->whereNull('fecha_caducidad')
                    ->orWhereDate('fecha_caducidad', '>=', Carbon::today());
            })
            ->orderByRaw('fecha_caducidad IS NULL, fecha_caducidad ASC')
            ->lockForUpdate()
            ->first();

        if (! $bono) {
            return null;
        }

        $bono->increment('sesiones_gastadas');

        return $bono;
    }

    /** Devuelve una sesión al bono que la cita consumió. */
    public function devolverSesion(Bono $bono): void
    {
        Bono::whereKey($bono->id)
            ->where('sesiones_gastadas', '>', 0)
            ->lockForUpdate()
            ->decrement('sesiones_gastadas');
    }
}
