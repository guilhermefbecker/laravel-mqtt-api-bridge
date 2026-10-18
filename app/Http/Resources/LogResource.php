<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'dispositivo' => $this->dispositivo,
            'cor'         => $this->cor,
            'porta'       => $this->porta,
            'estado'      => $this->estado,
            'ciclo'       => $this->ciclo,
            'recebido_em' => $this->created_at?->toIso8601String(),
        ];
    }
}
