<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fecha',
        'detalle',
        // Otros campos que puedas tener en tu tabla de reservas
    ];

    // Relación con el usuario que realiza la reserva
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
