<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

/**
 * Tests de Cita::validarReserva() — el "único punto de verdad" de las
 * reglas de negocio de reservas (horario, límite diario, límite por
 * carril, etc.), tal como dice su propio comentario en el modelo.
 */
class CitaValidarReservaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Los roles tienen que existir en la tabla de Spatie antes de poder
        // asignárselos a un usuario con assignRole(); si no, lanza una
        // excepción RoleDoesNotExist.
        SpatieRole::create(['name' => Role::ADMIN]);
        SpatieRole::create(['name' => Role::USUARIO]);
    }

    /** @test */
    public function no_se_puede_reservar_en_el_pasado()
    {
        $user = User::factory()->create();

        $inicio = Carbon::now()->subDay();
        $fin = $inicio->copy()->addHour();

        $error = Cita::validarReserva($user, 'carril1', $inicio, $fin, $inicio->toDateString());

        $this->assertNotNull($error);
    }

    /** @test */
    public function no_hay_servicio_los_domingos()
    {
        $user = User::factory()->create();

        $domingo = Carbon::now()->next(Carbon::SUNDAY)->setTime(10, 0);
        $fin = $domingo->copy()->addHour();

        $error = Cita::validarReserva($user, 'carril1', $domingo, $fin, $domingo->toDateString());

        $this->assertNotNull($error);
        $this->assertStringContainsString('domingos', $error);
    }

    /** @test */
    public function los_sabados_no_se_puede_reservar_despues_de_las_14()
    {
        $user = User::factory()->create();

        $sabado = Carbon::now()->next(Carbon::SATURDAY)->setTime(15, 0);
        $fin = $sabado->copy()->addHour();

        $error = Cita::validarReserva($user, 'carril1', $sabado, $fin, $sabado->toDateString());

        $this->assertNotNull($error);
        $this->assertStringContainsString('14:00', $error);
    }

    /** @test */
    public function fuera_del_horario_9_a_22_no_se_puede_reservar()
    {
        $user = User::factory()->create();

        $lunes = Carbon::now()->next(Carbon::MONDAY)->setTime(23, 0);
        $fin = $lunes->copy()->addHour();

        $error = Cita::validarReserva($user, 'carril1', $lunes, $fin, $lunes->toDateString());

        $this->assertNotNull($error);
        $this->assertStringContainsString('9:00 a 22:00', $error);
    }

    /** @test */
    public function un_usuario_normal_no_puede_tener_dos_reservas_el_mismo_dia()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::USUARIO);

        $lunes = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0);

        Cita::create([
            'user_id' => $user->id,
            'title' => $user->name,
            'start' => $lunes->toDateTimeString(),
            'end' => $lunes->copy()->addHour()->toDateTimeString(),
            'resource_id' => 'carril1',
            'day_of_week' => $lunes->dayOfWeek,
            'date' => $lunes->toDateString(),
        ]);

        $otraHora = $lunes->copy()->setTime(16, 0);
        $error = Cita::validarReserva($user, 'carril2', $otraHora, $otraHora->copy()->addHour(), $otraHora->toDateString());

        $this->assertNotNull($error);
    }

    /** @test */
    public function un_admin_si_puede_tener_varias_reservas_el_mismo_dia()
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMIN);

        $lunes = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0);

        Cita::create([
            'user_id' => $admin->id,
            'title' => $admin->name,
            'start' => $lunes->toDateTimeString(),
            'end' => $lunes->copy()->addHour()->toDateTimeString(),
            'resource_id' => 'carril1',
            'day_of_week' => $lunes->dayOfWeek,
            'date' => $lunes->toDateString(),
        ]);

        $otraHora = $lunes->copy()->setTime(16, 0);
        $error = Cita::validarReserva($admin, 'carril2', $otraHora, $otraHora->copy()->addHour(), $otraHora->toDateString());

        $this->assertNull($error);
    }

    /** @test */
    public function un_carril_admite_como_maximo_dos_reservas_en_la_misma_franja()
    {
        // Uso admins para las tres, así puedo colarles varias reservas el
        // mismo día sin chocar con la regla del test anterior (esta prueba
        // es sobre el LÍMITE POR CARRIL, no sobre el límite diario).
        $userA = User::factory()->create();
        $userA->assignRole(Role::ADMIN);
        $userB = User::factory()->create();
        $userB->assignRole(Role::ADMIN);
        $userC = User::factory()->create();
        $userC->assignRole(Role::ADMIN);

        $hora = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0);

        foreach ([$userA, $userB] as $u) {
            Cita::create([
                'user_id' => $u->id,
                'title' => $u->name,
                'start' => $hora->toDateTimeString(),
                'end' => $hora->copy()->addHour()->toDateTimeString(),
                'resource_id' => 'carril1',
                'day_of_week' => $hora->dayOfWeek,
                'date' => $hora->toDateString(),
            ]);
        }

        $error = Cita::validarReserva($userC, 'carril1', $hora, $hora->copy()->addHour(), $hora->toDateString());

        $this->assertNotNull($error);
        $this->assertStringContainsString('dos reservas', $error);
    }

    /** @test */
    public function al_editar_una_cita_no_choca_consigo_misma()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::USUARIO);

        $hora = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0);

        $cita = Cita::create([
            'user_id' => $user->id,
            'title' => $user->name,
            'start' => $hora->toDateTimeString(),
            'end' => $hora->copy()->addHour()->toDateTimeString(),
            'resource_id' => 'carril1',
            'day_of_week' => $hora->dayOfWeek,
            'date' => $hora->toDateString(),
        ]);

        // Sin pasar el id a excluir, esto fallaría con "ya tienes una
        // reserva ese día" porque la cita ya existe en la BD.
        $error = Cita::validarReserva($user, 'carril1', $hora, $hora->copy()->addHour(), $hora->toDateString(), $cita->id);

        $this->assertNull($error);
    }
}