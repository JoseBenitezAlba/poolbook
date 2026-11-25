<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('grupos_reserva', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users');
    $table->string('dia_semana');
    $table->time('hora');
    $table->date('fecha_inicio')->nullable();
    $table->date('fecha_fin')->nullable();
    $table->enum('estado', ['activo', 'cancelado'])->default('activo'); // Definido con un valor por defecto
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('grupos_reserva');
    }
};
