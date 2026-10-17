<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['dispositivo', 'cor', 'porta', 'estado', 'ciclo'];

    protected $casts = ['estado' => 'boolean', 'ciclo' => 'boolean'];
}
