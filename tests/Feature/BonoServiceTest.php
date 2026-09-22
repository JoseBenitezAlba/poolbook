<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BonoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonoServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function consume_el_bono_que_antes_caduca_primero()
    {
        $user = User::factory()->create();

        $bonoLejano = $user->bonos()->create([
            'sesiones_adquiridas' => 10,
            'sesiones_gastadas' => 0,
            'fecha_caducidad' => Carbon::now()->addMonths(6),
        ]);
        $bonoLejano->activo = true;
        $bonoLejano->save();

        $bonoCercano = $user->bonos()->create([
            'sesiones_adquiridas' => 10,
            'sesiones_gastadas' => 0,
            'fecha_caducidad' => Carbon::now()->addDays(5),
        ]);
        $bonoCercano->activo = true;
        $bonoCercano->save();

        $consumido = (new BonoService())->consumirSesion($user);

        $this->assertNotNull($consumido);
        $this->assertEquals($bonoCercano->id, $consumido->id, 'Debería haber elegido el bono que antes caduca, no cualquiera.');
        $this->assertEquals(1, $bonoCercano->fresh()->sesiones_gastadas);
        $this->assertEquals(0, $bonoLejano->fresh()->sesiones_gastadas, 'El bono que no tocaba no debería haberse movido.');
    }

    /** @test */
    public function ignora_bonos_caducados_agotados_o_desactivados()
    {
        $user = User::factory()->create();

        $caducado = $user->bonos()->create([
            'sesiones_adquiridas' => 10, 'sesiones_gastadas' => 0,
            'fecha_caducidad' => Carbon::now()->subDay(),
        ]);
        $caducado->activo = true;
        $caducado->save();

        $agotado = $user->bonos()->create([
            'sesiones_adquiridas' => 5, 'sesiones_gastadas' => 5,
            'fecha_caducidad' => Carbon::now()->addMonth(),
        ]);
        $agotado->activo = true;
        $agotado->save();

        $desactivado = $user->bonos()->create([
            'sesiones_adquiridas' => 10, 'sesiones_gastadas' => 0,
            'fecha_caducidad' => Carbon::now()->addMonth(),
        ]);
        $desactivado->activo = false;
        $desactivado->save();

        $resultado = (new BonoService())->consumirSesion($user);

        $this->assertNull($resultado, 'No debería haber encontrado ningún bono válido entre esos tres.');
    }

    /** @test */
    public function un_bono_sin_fecha_de_caducidad_tambien_es_valido()
    {
        $user = User::factory()->create();

        $bono = $user->bonos()->create([
            'sesiones_adquiridas' => 3, 'sesiones_gastadas' => 0,
            'fecha_caducidad' => null,
        ]);
        $bono->activo = true;
        $bono->save();

        $consumido = (new BonoService())->consumirSesion($user);

        $this->assertNotNull($consumido);
        $this->assertEquals($bono->id, $consumido->id);
    }

    /** @test */
    public function devolver_sesion_no_baja_de_cero()
    {
        $user = User::factory()->create();

        $bono = $user->bonos()->create([
            'sesiones_adquiridas' => 5, 'sesiones_gastadas' => 0,
            'fecha_caducidad' => null,
        ]);
        $bono->activo = true;
        $bono->save();

        (new BonoService())->devolverSesion($bono);

        $this->assertEquals(0, $bono->fresh()->sesiones_gastadas);
    }
}