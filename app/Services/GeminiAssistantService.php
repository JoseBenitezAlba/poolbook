<?php
namespace App\Services;

use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAssistantService
{
    private const MAX_MENSAJES_HISTORIAL = 12;
    private const MAX_TOKENS_RESPUESTA = 300;
    private const MAX_PASOS_HERRAMIENTAS = 4;

    protected array $carriles = ['carril1', 'carril2', 'carril3', 'carril4', 'carril5'];

    protected ReservaRecurrenteService $reservaRecurrenteService;
    protected BonoService $bonoService;
    protected array $tools;

    public function __construct(ReservaRecurrenteService $reservaRecurrenteService, BonoService $bonoService)
    {
        $this->reservaRecurrenteService = $reservaRecurrenteService;
        $this->bonoService = $bonoService;

        // Mismas 4 herramientas que antes, pero en formato OpenAI/Groq:
        // cada una envuelta en {"type": "function", "function": {...}}
        $this->tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'consultarDisponibilidad',
                    'description' => 'Consulta qué carriles de la piscina están libres u ocupados en una fecha y, opcionalmente, una hora concreta.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'fecha' => [
                                'type' => 'string',
                                'description' => 'Fecha en formato YYYY-MM-DD.',
                            ],
                            'hora' => [
                                'type' => 'string',
                                'description' => 'Hora en formato HH:00 (24h). Opcional: si no se indica, se devuelve la disponibilidad de todo el día.',
                            ],
                        ],
                        'required' => ['fecha'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'crearReserva',
                    'description' => 'Crea una reserva (cita) para el usuario autenticado en un carril, fecha y hora concretos. Las reservas duran 1 hora.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'fecha' => [
                                'type' => 'string',
                                'description' => 'Fecha en formato YYYY-MM-DD.',
                            ],
                            'hora' => [
                                'type' => 'string',
                                'description' => 'Hora de inicio en formato HH:00 (24h).',
                            ],
                            'carril' => [
                                'type' => 'string',
                                'description' => "Id del carril, ej. 'carril1'. Si el usuario no especifica uno, usa el primero libre que hayas visto con consultarDisponibilidad.",
                            ],
                        ],
                        'required' => ['fecha', 'hora', 'carril'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'consultarSaldoUsuario',
                    'description' => 'Consulta cuántas sesiones/usos de bono le quedan disponibles al usuario autenticado, sumando todos sus bonos no caducados.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'resolverLimiteMensual',
                    'description' => 'Convierte un mes escrito en español en el límite de una recurrencia. Úsala siempre que el usuario diga "hasta diciembre", "hasta enero", etc., incluso si el nombre del mes tiene una errata. No pidas al usuario una fecha YYYY-MM-DD.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'mes' => [
                                'type' => 'string',
                                'description' => "Mes en español, ej. 'diciembre'.",
                            ],
                            'anio' => [
                                'type' => 'integer',
                                'description' => 'Año opcional. Si no se indica, el sistema usa el próximo mes con ese nombre.',
                            ],
                        ],
                        'required' => ['mes'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'calcularProximasFechas',
                    'description' => 'Calcula las próximas fechas de un día de la semana desde hoy. Úsala cuando el usuario diga una cantidad como "dos lunes" o "tres martes" sin indicar fechas concretas.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'dia_semana' => [
                                'type' => 'string',
                                'description' => "Día de la semana en español, ej. 'lunes'.",
                            ],
                            'cantidad' => [
                                'type' => 'integer',
                                'description' => 'Número de próximas fechas que quiere reservar.',
                            ],
                        ],
                        'required' => ['dia_semana', 'cantidad'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'calcularFechasRecurrentes',
                    'description' => 'Calcula de forma exacta las fechas de una reserva recurrente. Úsala siempre antes de decir qué fechas corresponden a un día de la semana; nunca las calcules mentalmente.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'dia_semana' => [
                                'type' => 'string',
                                'description' => "Día de la semana en español, ej. 'lunes'.",
                            ],
                            'fecha_inicio' => [
                                'type' => 'string',
                                'description' => 'Fecha inicial en formato YYYY-MM-DD.',
                            ],
                            'fecha_final' => [
                                'type' => 'string',
                                'description' => 'Fecha final en formato YYYY-MM-DD.',
                            ],
                            'incluir_hoy' => [
                                'type' => 'boolean',
                                'description' => 'Indica si debe incluirse hoy si coincide con el día solicitado.',
                            ],
                        ],
                        'required' => ['dia_semana', 'fecha_inicio', 'fecha_final', 'incluir_hoy'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'crearReservaRecurrente',
                    'description' => 'Crea una serie de reservas recurrentes para el usuario autenticado (ej. "todos los lunes"), descontando de su saldo de bonos. Si el saldo no llega a cubrir todas las fechas del rango pedido, solo se crean las que el saldo permite, y se informa de cuáles se quedaron fuera. Úsala solo después de que el usuario haya confirmado que quiere seguir, especialmente si antes le avisaste de que el saldo no llega para todas las fechas.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'dia_semana' => [
                                'type' => 'string',
                                'description' => "Día de la semana en español, ej. 'lunes', 'miercoles'.",
                            ],
                            'fecha_inicio' => [
                                'type' => 'string',
                                'description' => 'Fecha desde la que empezar a buscar el día pedido, en formato YYYY-MM-DD. Normalmente la fecha de hoy.',
                            ],
                            'fecha_final' => [
                                'type' => 'string',
                                'description' => 'Fecha límite de la serie recurrente, en formato YYYY-MM-DD.',
                            ],
                            'hora' => [
                                'type' => 'string',
                                'description' => 'Hora de inicio en formato HH:00 (24h).',
                            ],
                            'carril' => [
                                'type' => 'string',
                                'description' => "Carril preferido, ej. 'carril1'. Si no está libre en alguna fecha concreta, el sistema prueba otros automáticamente.",
                            ],
                            'incluir_hoy' => [
                                'type' => 'boolean',
                                'description' => 'Si hoy mismo coincide con el día de la semana pedido, indica si hay que incluir hoy o empezar la semana que viene. Pregunta al usuario si no está claro por el contexto.',
                            ],
                        ],
                        'required' => [
                            'dia_semana',
                            'fecha_inicio',
                            'fecha_final',
                            'hora',
                            'carril',
                            'incluir_hoy',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function chat(string $userMessage, array $history = []): array
    {
        $apiKey = config('services.groq.key');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (empty($apiKey)) {
            return [
                'reply' => 'El asistente no está configurado (falta GROQ_API_KEY).',
                'history' => $history,
            ];
        }

        $ahora = Carbon::now()->locale('es')->isoFormat('LLLL');

        $systemInstruction = <<<TXT
Eres el asistente de reservas de PoolBook, una piscina con 5 carriles (carril1 a carril5).
Fecha y hora actual del sistema: {$ahora}.

Reglas del negocio que debes respetar SIEMPRE, sin excepción, aunque el usuario insista:
- No hay servicio los domingos.
- Horario: lunes a viernes de 9:00 a 22:00. Sábados de 9:00 a 14:00.
- Cada reserva dura 1 hora exacta (las recurrentes duran 55 minutos, gestionado por el sistema).
- Un mismo carril admite como máximo 2 reservas en la misma franja horaria.
- No se pueden crear reservas en fechas u horas ya pasadas.
- Las reservas se pagan con bonos: cada sesión gasta 1 uso del bono del usuario.

Cuando el usuario pida disponibilidad, usa consultarDisponibilidad antes de responder; nunca inventes qué carriles están libres.
Cuando el usuario pida reservar UN día suelto, primero comprueba disponibilidad si no la tienes, y luego usa crearReserva. La reserva siempre queda a nombre del usuario autenticado: no pidas su nombre.
Cuando el usuario pida algo recurrente (ej. "todos los lunes", "los martes hasta final de mes"):
1. Si pide una cantidad sin fechas (ej. "dos lunes"), usa calcularProximasFechas. Si dice "hasta" un mes, usa primero resolverLimiteMensual y después calcularFechasRecurrentes con el límite devuelto. Si da un rango de fechas, usa calcularFechasRecurrentes. Nunca calcules en texto qué día de la semana corresponde a una fecha: usa exactamente las fechas que devuelva la herramienta.
2. Después usa consultarSaldoUsuario para saber cuántas sesiones le quedan.
3. Si el saldo no llega para todas, avísale ANTES de reservar nada: dile cuántas sí puede cubrir y pregúntale si quiere seguir con esas o prefiere ampliar su bono primero. No uses crearReservaRecurrente todavía en ese caso.
4. Solo llama a crearReservaRecurrente cuando el usuario haya confirmado explícitamente que quiere proceder.
5. Después de crear las reservas, si el resultado indica fechas que no se pudieron cubrir por falta de hueco en ningún carril, infórmaselo también.
Si no especifica carril, elige tú el primero libre.
Responde siempre en español, de forma breve y natural.
TXT;

        // Enviamos siempre las instrucciones actuales y solo el contexto reciente.
        // Así una conversación larga no agota el límite de tokens de Groq.
        $messages = array_merge([
            [
                'role' => 'system',
                'content' => $systemInstruction,
            ],
        ], $this->recortarHistorial($history));

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage . $this->contextoFechaRecurrente($userMessage),
        ];

        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        for ($i = 0; $i < self::MAX_PASOS_HERRAMIENTAS; $i++) {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'tools' => $this->tools,
                    'tool_choice' => 'auto',
                    'max_completion_tokens' => self::MAX_TOKENS_RESPUESTA,
                    'reasoning_effort' => 'low',
                ]);

            if (! $response->successful()) {
                Log::error('Error llamando a Groq', [
                    'body' => $response->body(),
                ]);

                if ($response->status() === 429) {
                    $espera = $response->header('retry-after');
                    $detalleEspera = $espera
                        ? " Espera aproximadamente {$espera} segundos antes de volver a intentarlo."
                        : ' Prueba de nuevo dentro de unos minutos.';

                    return [
                        'reply' => 'Se ha alcanzado temporalmente el límite gratuito del asistente.' . $detalleEspera,
                        'history' => $messages,
                    ];
                }

                return [
                    'reply' => 'El servicio de IA no está disponible en este momento. Inténtalo de nuevo más tarde.',
                    'history' => $messages,
                ];
            }

            $message = $response->json('choices.0.message');
            $toolCalls = $message['tool_calls'] ?? [];

            // Guardamos el mensaje del asistente tal cual vino (incluyendo
            // tool_calls si los hay) para que el historial quede coherente.
            $messages[] = $message;

            if (! empty($toolCalls)) {
                foreach ($toolCalls as $toolCall) {
                    $nombreHerramienta = $toolCall['function']['name'];
                    $argumentos = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

                    $resultado = $this->ejecutarHerramienta($nombreHerramienta, $argumentos);

                    // En formato OpenAI/Groq, cada resultado de tool_call
                    // va como un mensaje role:"tool" con su tool_call_id.
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content' => json_encode($resultado),
                    ];
                }

                continue;
            }

            return [
                'reply' => trim($message['content'] ?? '') ?: 'No he podido generar una respuesta.',
                'history' => $messages,
            ];
        }

        return [
            'reply' => 'El asistente necesita una confirmación adicional antes de terminar. ¿Puedes repetir la última indicación?',
            'history' => $messages,
        ];
    }

    /**
     * Mantiene un contexto corto sin separar una llamada de herramienta de su respuesta.
     */
    protected function recortarHistorial(array $history): array
    {
        $history = array_values(array_filter($history, function ($message) {
            return is_array($message)
                && in_array($message['role'] ?? null, ['user', 'assistant', 'tool'], true);
        }));

        $history = array_slice($history, -self::MAX_MENSAJES_HISTORIAL);

        // El historial no puede empezar por una respuesta ni por el resultado de
        // una herramienta: Groq necesita antes el mensaje del usuario que la originó.
        while (! empty($history) && ($history[0]['role'] ?? null) !== 'user') {
            array_shift($history);
        }

        return $history;
    }

    /**
     * Aporta al modelo una fecha comprobada por el servidor cuando el usuario
     * menciona un día de la semana y una fecha de referencia.
     */
    protected function contextoFechaRecurrente(string $mensaje): string
    {
        $dias = 'lunes|martes|mi(?:é|e)rcoles|jueves|viernes|s(?:á|a)bado|domingo';

        if (! preg_match("/\\b({$dias})\\b/ui", $mensaje, $coincidenciaDia)
            || ! preg_match('/\\b(\\d{4}-\\d{2}-\\d{2})\\b/', $mensaje, $coincidenciaFecha)) {
            return '';
        }

        try {
            $dia = strtr(mb_strtolower($coincidenciaDia[1]), ['é' => 'e', 'á' => 'a']);
            $fechaReferencia = Carbon::createFromFormat('Y-m-d', $coincidenciaFecha[1]);
            $siguienteFecha = $this->reservaRecurrenteService->calcularPrimeraFecha(
                $fechaReferencia->toDateString(),
                $dia
            );
        } catch (\Throwable $e) {
            return '';
        }

        return "\n\n[Dato calculado por el sistema: {$coincidenciaFecha[1]} es {$fechaReferencia->locale('es')->isoFormat('dddd')}. Para reservas de {$dia}, la primera fecha válida desde esa referencia es {$siguienteFecha->toDateString()}. No afirmes que la fecha de referencia es {$dia} ni pidas incluirla si no coincide.]";
    }

    protected function ejecutarHerramienta(string $nombre, array $args): array
    {
        return match ($nombre) {
            'consultarDisponibilidad' => $this->consultarDisponibilidad($args),
            'crearReserva' => $this->crearReserva($args),
            'consultarSaldoUsuario' => $this->consultarSaldoUsuario($args),
            'resolverLimiteMensual' => $this->resolverLimiteMensual($args),
            'calcularProximasFechas' => $this->calcularProximasFechas($args),
            'calcularFechasRecurrentes' => $this->calcularFechasRecurrentes($args),
            'crearReservaRecurrente' => $this->crearReservaRecurrente($args),
            default => ['error' => "Herramienta desconocida: {$nombre}"],
        };
    }

    protected function resolverLimiteMensual(array $args): array
    {
        $mesTexto = $args['mes'] ?? null;
        $anio = filter_var($args['anio'] ?? null, FILTER_VALIDATE_INT);
        $meses = [
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12,
        ];
        $mesNormalizado = strtr(mb_strtolower(trim((string) $mesTexto)), ['é' => 'e', 'á' => 'a']);

        if (! isset($meses[$mesNormalizado])) {
            $mejorCoincidencia = null;
            $menorDistancia = 4;

            foreach (array_keys($meses) as $mesValido) {
                $distancia = levenshtein($mesNormalizado, $mesValido);
                if ($distancia < $menorDistancia) {
                    $menorDistancia = $distancia;
                    $mejorCoincidencia = $mesValido;
                }
            }

            $mesNormalizado = $mejorCoincidencia ?? $mesNormalizado;
        }

        if (! isset($meses[$mesNormalizado])) {
            return ['error' => 'No reconozco ese mes.'];
        }

        $hoy = Carbon::today();
        $numeroMes = $meses[$mesNormalizado];
        $anioDestino = $anio ?: $hoy->year;

        if (! $anio && $numeroMes < $hoy->month) {
            $anioDestino++;
        }

        $inicioMes = Carbon::create($anioDestino, $numeroMes, 1)->startOfDay();

        return [
            'inicio_mes' => $inicioMes->toDateString(),
            'fecha_final' => $inicioMes->copy()->subDay()->toDateString(),
            'explicacion' => "La recurrencia termina antes del 1 de {$mesNormalizado}.",
        ];
    }

    protected function calcularProximasFechas(array $args): array
    {
        $diaSemana = $args['dia_semana'] ?? null;
        $cantidad = filter_var($args['cantidad'] ?? null, FILTER_VALIDATE_INT);

        if (! $diaSemana || $cantidad === false || $cantidad < 1 || $cantidad > 52) {
            return ['error' => 'Indica un día de la semana y una cantidad entre 1 y 52.'];
        }

        try {
            $primeraFecha = $this->reservaRecurrenteService->calcularPrimeraFecha(
                Carbon::today()->toDateString(),
                $diaSemana
            );
        } catch (\InvalidArgumentException $e) {
            return ['error' => $e->getMessage()];
        }

        $fechas = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $fechas[] = $primeraFecha->copy()->addWeeks($i)->toDateString();
        }

        return [
            'dia_semana' => $diaSemana,
            'fechas' => $fechas,
            'total' => $cantidad,
        ];
    }

    protected function calcularFechasRecurrentes(array $args): array
    {
        $diaSemana = $args['dia_semana'] ?? null;
        $fechaInicio = $args['fecha_inicio'] ?? null;
        $fechaFin = $args['fecha_final'] ?? $args['fecha_fin'] ?? null;
        $incluirHoy = $args['incluir_hoy'] ?? true;

        if (! $diaSemana || ! $this->esFechaValida($fechaInicio) || ! $this->esFechaValida($fechaFin)) {
            return ['error' => 'Indica un día de la semana y fechas válidas en formato YYYY-MM-DD.'];
        }

        try {
            $fechas = $this->reservaRecurrenteService->generarFechasRecurrentes(
                $fechaInicio,
                $diaSemana,
                $fechaFin,
                (bool) $incluirHoy
            );
        } catch (\InvalidArgumentException $e) {
            return ['error' => $e->getMessage()];
        }

        return [
            'dia_semana' => $diaSemana,
            'fechas' => $fechas,
            'total' => count($fechas),
        ];
    }

    protected function consultarDisponibilidad(array $args): array
    {
        $fecha = $args['fecha'] ?? null;
        $hora = $args['hora'] ?? null;

        if (! $fecha || ! $this->esFechaValida($fecha)) {
            return [
                'error' => 'Fecha no válida o formato incorrecto (usa YYYY-MM-DD).',
            ];
        }

        $citasDelDia = Cita::whereDate('date', $fecha)->get();

        $resultado = [];

        foreach ($this->carriles as $carril) {
            $citasCarril = $citasDelDia->where('resource_id', $carril);

            if ($hora) {
                $franjaInicio = Carbon::parse("{$fecha} {$hora}");

                $ocupadas = $citasCarril->filter(function ($cita) use ($franjaInicio) {
                    return Carbon::parse($cita->start)->lt($franjaInicio->copy()->addHour())
                        && Carbon::parse($cita->end)->gt($franjaInicio);
                })->count();

                $resultado[$carril] = $ocupadas >= 2
                    ? 'ocupado'
                    : 'libre';
            } else {
                $horasOcupadas = $citasCarril
                    ->map(fn ($c) => Carbon::parse($c->start)->format('H:i'))
                    ->unique()
                    ->values();

                $resultado[$carril] = $horasOcupadas->isEmpty()
                    ? 'libre todo el día'
                    : ['horas_ocupadas' => $horasOcupadas];
            }
        }

        return [
            'fecha' => $fecha,
            'disponibilidad' => $resultado,
        ];
    }

    protected function crearReserva(array $args): array
    {
        $fecha = $args['fecha'] ?? null;
        $hora = $args['hora'] ?? null;
        $carril = $args['carril'] ?? null;

        if (! $fecha || ! $hora || ! $carril) {
            return [
                'error' => 'Faltan datos para crear la reserva (fecha, hora y carril son obligatorios).',
            ];
        }

        if (! in_array($carril, $this->carriles, true)) {
            return [
                'error' => "Carril no válido. Los carriles disponibles son: " . implode(', ', $this->carriles),
            ];
        }

        if (! $this->esFechaValida($fecha)) {
            return [
                'error' => 'Fecha no válida o formato incorrecto (usa YYYY-MM-DD).',
            ];
        }

        $user = Auth::user();

        if (! $user) {
            return [
                'error' => 'No hay ningún usuario autenticado; no se puede crear la reserva.',
            ];
        }

        try {
            $start = Carbon::parse("{$fecha} {$hora}");
        } catch (\Throwable $e) {
            return [
                'error' => 'Hora no válida (usa formato HH:00).',
            ];
        }

        $end = $start->copy()->addHour();

        $error = Cita::validarReserva(
            $user,
            $carril,
            $start,
            $end,
            $fecha
        );

        if ($error) {
            return [
                'error' => $error,
            ];
        }

        $cita = DB::transaction(function () use ($user, $start, $end, $carril, $fecha) {
            $bono = $this->bonoService->consumirSesion($user);

            if (! $bono) {
                return null;
            }

            return Cita::create([
                'title' => $user->name,
                'start' => $start,
                'end' => $end,
                'user_id' => $user->id,
                'bono_id' => $bono->id,
                'resource_id' => $carril,
                'day_of_week' => $start->dayOfWeek,
                'date' => $fecha,
            ]);
        });

        if (! $cita) {
            return [
                'error' => 'No tienes sesiones disponibles en un bono válido.',
            ];
        }

        return [
            'ok' => true,
            'cita_id' => $cita->id,
            'mensaje' => "Reserva creada: {$carril} el {$fecha} a las {$hora}.",
        ];
    }

    protected function consultarSaldoUsuario(array $args): array
    {
        $user = Auth::user();

        if (! $user) {
            return [
                'error' => 'No hay ningún usuario autenticado.',
            ];
        }

        $saldo = $user->bonos()
            ->where('activo', true)
            ->where(function ($query) {
                $query->whereNull('fecha_caducidad')
                    ->orWhereDate('fecha_caducidad', '>=', Carbon::today());
            })
            ->get()
            ->sum(
                fn ($bono) =>
                    $bono->sesiones_adquiridas - $bono->sesiones_gastadas
            );

        return [
            'sesiones_disponibles' => $saldo,
        ];
    }

    protected function crearReservaRecurrente(array $args): array
    {
        $diaSemana = $args['dia_semana'] ?? null;
        $fechaInicio = $args['fecha_inicio'] ?? null;
        $fechaFin = $args['fecha_final'] ?? $args['fecha_fin'] ?? null;
        $hora = $args['hora'] ?? null;
        $carril = $args['carril'] ?? null;
        $incluirHoy = $args['incluir_hoy'] ?? true;

        if (! $diaSemana || ! $fechaInicio || ! $fechaFin || ! $hora || ! $carril) {
            return [
                'error' => 'Faltan datos para crear la reserva recurrente.',
            ];
        }

        $user = Auth::user();

        if (! $user) {
            return [
                'error' => 'No hay ningún usuario autenticado.',
            ];
        }

        try {
            $resultado = $this->reservaRecurrenteService->crearReservasRecurrentes(
                $user,
                $diaSemana,
                $fechaInicio,
                $fechaFin,
                $hora,
                $carril,
                $user->name,
                $incluirHoy
            );
        } catch (\InvalidArgumentException $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }

        return [
            'ok' => true,
            'grupo_reserva_id' => $resultado['grupo']->id,
            'fechas_sin_saldo' => $resultado['fechas_no_cubiertas_por_saldo'],
            'fechas_sin_hueco' => $resultado['fechas_sin_hueco'],
        ];
    }

    protected function esFechaValida(string $fecha): bool
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $fecha);

            return $date->format('Y-m-d') === $fecha;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
