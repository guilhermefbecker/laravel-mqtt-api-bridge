<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComandoRequest;
use App\Models\LogAlteracao;
use App\Services\MqttService;
use Illuminate\Http\JsonResponse;

class ComandoController extends Controller
{
    // Publica no MQTT, registra a auditoria e devolve o JSON.
    public function store(ComandoRequest $request, MqttService $mqtt): JsonResponse
    {
        $comando = $request->validated();

        try {
            $mqtt->publicar(config('mqtt.topico_comando'), $comando);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'mensagem' => 'Falha ao publicar comando no broker MQTT.',
            ], 502);
        }

        LogAlteracao::registrar(
            'enviou comando',
            'comandos',
            null,
            null,
            $comando,
            'Enviou comando: ' . $this->descreverComando($comando),
        );

        return response()->json([
            'ok' => true,
            'mensagem' => 'Comando publicado com sucesso.',
            'comando' => $comando,
            'topico' => config('mqtt.topico_comando'),
        ]);
    }

    private function descreverComando(array $comando): string
    {
        if ($comando['estado'] === 'ciclo') {
            return sprintf(
                'ciclo (vermelho %ds, amarelo %ds, verde %ds)',
                $comando['tempoVermelho'],
                $comando['tempoAmarelo'],
                $comando['tempoVerde'],
            );
        }

        // Cores acendem a luz: "vermelho = ligado" (mesmo formato dos logs do MQTT).
        if (!empty($comando['cor'])) {
            return "{$comando['cor']} = ligado";
        }

        return $comando['estado'];
    }
}
