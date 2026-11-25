<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bono extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sesiones_adquiridas', 'sesiones_gastadas', 'fecha_caducidad'];
}