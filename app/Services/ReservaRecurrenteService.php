<?php

namespace App\Services; 

use Carbon\Carbon;
use App\Models\User;
use App\Models\GrupoReserva;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;

class ReservaRecurrenteService
{
    /**
     * Calcula la primera fecha correspondiente a un día de la semana específico.
     *
     * @param string $fechaInicio Fecha base en formato texto o fecha.
     * @param string $diaSemanaTexto Día deseado en español (ej. 'lunes').
     * @param bool $incluirHoy Si es true y la fecha coincide con el día deseado, devuelve hoy.
     * @return Carbon
     */
    public function calcularPrimeraFecha(string $fechaInicio, string $diaSemanaTexto, bool $incluirHoy = true): Carbon
    {
        $diasMapeo = [
            'domingo'   => 0,
            'lunes'     => 1,
            'martes'    => 2,
            'miercoles' => 3,
            'jueves'    => 4,
            'viernes'   => 5,
            'sabado'    => 6,
        ];

        // 2. Normalizar el día en texto
        $diaNormalizado = strtolower(trim($diaSemanaTexto));

        if (!isset($diasMapeo[$diaNormalizado])) {
            throw new \InvalidArgumentException("Día de la semana no válido: {$diaSemanaTexto}");
        }

        $diaObjetivo = $diasMapeo[$diaNormalizado];

        // 3. Convertir la fecha de inicio a un objeto Carbon
        $fecha = Carbon::parse($fechaInicio);

        // 4. Sacar el día de la semana actual 
        $diaActual = $fecha->dayOfWeek;

        // 5. Aplicar la fórmula del módulo para obtener los días de diferencia
        $diasDiferencia = ($diaObjetivo - $diaActual + 7) % 7;

        // 6. Si $incluirHoy es false y el resultado es 0 (coincide hoy), forzar al siguiente ciclo (7 días)
        if (!$incluirHoy && $diasDiferencia === 0) {
            $diasDiferencia = 7;
        }

        // 7. Sumar los días calculados y devolver el resultado
        return $fecha->addDays($diasDiferencia);
    }

    public function comprobarSaldoParaFechas(User $user, array $fechas): array
    {
        // 1. Sumar el saldo total disponible (sesiones restantes de bonos no caducados)
        $saldoTotal = $user->bonos()
            ->where('activo', true)
            ->where(function ($query) {
                $query->whereNull('fecha_caducidad')
                    ->orWhereDate('fecha_caducidad', '>=', Carbon::today());
            })
            ->get()
            ->sum(function ($bono) {
                return $bono->sesiones_adquiridas - $bono->sesiones_gastadas;
            });

        // Inicializamos las listas de clasificación
        $cubiertas = [];
        $noCubiertas = [];

        // 2. Recorrer las fechas y clasificarlas restando del saldo
        foreach ($fechas as $fecha) {
            if ($saldoTotal > 0) {
                $cubiertas[] = $fecha;
                $saldoTotal--;
            } else {
                $noCubiertas[] = $fecha;
            }
        }

        // 3. Devolver el resultado con las dos listas
        return [
            'cubiertas' => $cubiertas,
            'no_cubiertas' => $noCubiertas,
        ];
    }

    public function generarFechasRecurrentes(string $fechaInicio, string $diaSemanaTexto, string $fechaFin, bool $incluirHoy = true): array
    {
        $primeraFecha = $this->calcularPrimeraFecha($fechaInicio, $diaSemanaTexto, $incluirHoy);
        $fechaLimite = Carbon::parse($fechaFin);

        $fechas = [];
        $fechaActual = $primeraFecha->copy();

        while ($fechaActual->lte($fechaLimite)) {
            $fechas[] = $fechaActual->format('Y-m-d');
            $fechaActual = $fechaActual->copy()->addWeek();
        }

        return $fechas;
    }

    /**
     * Crea una serie de reservas recurrentes, aplicando FIFO sobre los bonos
     * y las reglas de negocio de Cita::validarReserva() (horario, disponibilidad, etc.).
     * Si el carril pedido está ocupado en alguna fecha, prueba los demás carriles
     * antes de descartarla. Nunca gasta saldo de bono por una fecha que no llegó
     * a crearse.
     *
     * @return array{grupo: GrupoReserva, fechas_no_cubiertas_por_saldo: array, fechas_sin_hueco: array}
     */
    public function crearReservasRecurrentes(
        User $user,
        string $diaSemanaTexto,
        string $fechaInicio,
        string $fechaFin,
        string $hora,
        string $resourceId,
        string $titulo,
        bool $incluirHoy = true
    ): array {
        // Todos los carriles posibles, para poder probar alternativas si el pedido está lleno
        $carrilesDisponibles = ['carril1', 'carril2', 'carril3', 'carril4', 'carril5'];

        $fechas = $this->generarFechasRecurrentes($fechaInicio, $diaSemanaTexto, $fechaFin, $incluirHoy);
        $resultado = $this->comprobarSaldoParaFechas($user, $fechas);
        $fechasACrear = $resultado['cubiertas'];
        $fechasNoCubiertasPorSaldo = $resultado['no_cubiertas'];

        return DB::transaction(function () use ($user, $diaSemanaTexto, $fechaInicio, $fechaFin, $fechasACrear, $fechasNoCubiertasPorSaldo, $hora, $resourceId, $titulo, $carrilesDisponibles) {
            // Esta tabla solo guarda los datos de la serie en sí (día, hora, rango, estado).
            // Las fechas no cubiertas por saldo NO son una columna de esta tabla: son solo
            // información para devolver al chat, así que van en el "return" de más abajo,
            // no aquí dentro del create().
            $grupo = GrupoReserva::create([
                'user_id' => $user->id,
                'dia_semana' => $diaSemanaTexto,
                'hora' => $hora,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => 'activo',
            ]);

            $bonos = $user->bonos()
                ->where('activo', true)
                ->where(function ($query) {
                    $query->whereNull('fecha_caducidad')
                        ->orWhereDate('fecha_caducidad', '>=', Carbon::today());
                })
                ->orderByRaw('fecha_caducidad IS NULL, fecha_caducidad ASC')
                ->get();

            $indiceBono = 0;
            $fechasSinHueco = []; // fechas con saldo suficiente pero sin carril libre en ninguno

            foreach ($fechasACrear as $fecha) {
                $inicio = Carbon::parse($fecha . ' ' . $hora);
                $fin = $inicio->copy()->addMinutes(55);

                // 1. Probar primero el carril pedido, y si no, los demás en orden
                $carrilElegido = null;
                $errorUltimoIntento = null;

                $ordenCarriles = array_unique(array_merge([$resourceId], $carrilesDisponibles));

                foreach ($ordenCarriles as $carril) {
                    $error = Cita::validarReserva($user, $carril, $inicio, $fin, $fecha);
                    if ($error === null) {
                        $carrilElegido = $carril;
                        break;
                    }
                    $errorUltimoIntento = $error;
                }

                // 2. Si ningún carril sirvió para esta fecha, se salta SIN gastar bono
                if ($carrilElegido === null) {
                    $fechasSinHueco[] = ['fecha' => $fecha, 'motivo' => $errorUltimoIntento];
                    continue;
                }

                // 3. Solo aquí, con carril confirmado, gastamos el bono correspondiente
                while ($indiceBono < count($bonos) &&
                       $bonos[$indiceBono]->sesiones_adquiridas - $bonos[$indiceBono]->sesiones_gastadas <= 0) {
                    $indiceBono++;
                }

                if ($indiceBono >= count($bonos)) {
                    // No debería pasar, ya filtramos con comprobarSaldoParaFechas, pero por seguridad
                    $fechasSinHueco[] = ['fecha' => $fecha, 'motivo' => 'Sin saldo de bono disponible.'];
                    continue;
                }

                $bonos[$indiceBono]->sesiones_gastadas++;
                $bonos[$indiceBono]->save();

                Cita::create([
                    'user_id' => $user->id,
                    'bono_id' => $bonos[$indiceBono]->id,
                    'grupo_reserva_id' => $grupo->id,
                    'title' => $titulo,
                    'day_of_week' => $inicio->dayOfWeek,
                    'date' => $fecha,
                    'start' => $inicio,
                    'end' => $fin,
                    'resource_id' => $carrilElegido,
                ]);
            }

            return [
                'grupo' => $grupo,
                'fechas_no_cubiertas_por_saldo' => $fechasNoCubiertasPorSaldo,
                'fechas_sin_hueco' => $fechasSinHueco,
            ];
        });
    }
}
