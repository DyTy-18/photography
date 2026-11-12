<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Lead del formulario de rutas: alguien que quiere reservar o cotizar un viaje con Marco. */
class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $guarded = ['id'];

    protected $casts = ['fecha' => 'date'];
}
