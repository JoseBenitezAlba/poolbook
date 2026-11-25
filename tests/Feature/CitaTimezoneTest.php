<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

/**
 * Test de regresión para el bug de timezone que depuramos juntos:
 * el calendario manda la hora LOCAL de Madrid con un sufijo "Z" (que
 * parece UTC pero no lo es — así codifica FullCalendar las fechas
 * cuando usas timeZone: 'Europe/Madrid'). En su momento el backend la
 * reinterpretaba como UTC real con ->setTimezone() y la desplazaba
 * +2h, rechazando reservas de tarde/noche que eran perfectamente
 * válidas.
 *
 * Si alguien vuelve a tocar el Carbon::parse() de CitaController@store
 * y reintroduce ese ->setTimezone(), este test se pone en rojo.
 */
class CitaTimezoneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SpatieRole::create(['name' => Role::USUARIO]);
    }

    /** @test */
    public function una_reserva_a_las_21_en_madrid_no_se_desplaza_a_las_23()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::USUARIO);

        $bono = $user->bonos()->create([
            'sesiones_adquiridas' => 5,
            'sesiones_gastadas' => 0,
            'fecha_caducidad' => null,
        ]);
        $bono->activo = true;
        $bono->save();

        $lunes = Carbon::now('Europe/Madrid')->next(Carbon::MONDAY)->setTime(21, 0, 0);

        // Payload igual al que manda de verdad el calendario: 21:00 de
        // Madrid, con "Z" al final aunque NO sean las 21:00 UTC reales.
        $payload = [
            'start' => $lunes->format('Y-m-d\TH:i:s.000') . 'Z',
            'end' => $lunes->copy()->addMinutes(55)->format('Y-m-d\TH:i:s.000') . 'Z',
            'resourceId' => 'carril1',
            'extendedProps' => [
                'day_of_week' => $lunes->dayOfWeek,
                'date' => $lunes->toDateString(),
            ],
        ];

        $response = $this->actingAs($user)->postJson('/citas', $payload);

        $response->assertStatus(201);

        // Si el bug volviera, esto se habría guardado como las 23:00.
        $this->assertDatabaseHas('citas', [
            'user_id' => $user->id,
            'start' => $lunes->format('Y-m-d H:i:s'),
        ]);
    }
}