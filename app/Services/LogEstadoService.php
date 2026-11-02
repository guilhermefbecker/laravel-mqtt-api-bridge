<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Validator;

class LogEstadoService
{
    /**
     * Decodifica a mensagem do MQTT, valida e salva em logs.
     * Retorna ['status' => salvo|ignorado|invalido, 'log' => ?Log].
     */
    public function salvarMensagem(string $mensagem): array
    {
        $dados = json_decode($mensagem, true);

        // Ignora mensagens que não são JSON (ex.: comandos de texto como "iniciar").
        if (!is_array($dados)) {
            return ['status' => 'ignorado', 'log' => null];
        }

        $validator = Validator::make($dados, [
            'dispositivo' => 'required|string',
            'cor' => 'required|string',
            'porta' => 'required|integer',
            'estado' => 'required|boolean',
            'ciclo' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ['status' => 'invalido', 'log' => null];
        }

        return ['status' => 'salvo', 'log' => Log::create($validator->validated())];
    }
}
