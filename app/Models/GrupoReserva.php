<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoReserva extends Model
{
    use HasFactory;
     protected $table = 'grupos_reserva';
    protected $fillable = ['user_id', 'dia_semana', 'hora', 'fecha_inicio', 'fecha_fin', 'estado'];
}
