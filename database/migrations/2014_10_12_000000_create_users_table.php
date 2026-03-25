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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Agregar la columna para la clave foránea en la tabla citas
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('citas');

        // Eliminar la columna y la clave foránea en la tabla citas si es necesario
        // Schema::table('citas', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        //     $table->dropColumn('user_id');
        // });

        Schema::dropIfExists('users');
    }
};
