<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semaforo extends Model
{
    protected $fillable = [
        'nome',
        'tempo_vermelho',
        'tempo_amarelo',
        'tempo_verde',
        'latitude_longitude',
        'ativo',
    ];

    protected $casts = [
        'tempo_vermelho' => 'integer',
        'tempo_amarelo' => 'integer',
        'tempo_verde' => 'integer',
        'ativo' => 'boolean',
    ];

    public function getCicloAttribute(): int
    {
        return $this->tempo_vermelho + $this->tempo_amarelo + $this->tempo_verde;
    }
}
