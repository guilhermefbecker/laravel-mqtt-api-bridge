<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\MqttClient;

class ComandoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dispositivo' => ['required', 'string', 'max:60'],
            'cor' => ['required', 'string', 'in:vermelho,amarelo,verde'],
            'estado' => ['required', 'boolean'],
            'ciclo' => ['nullable', 'boolean'],
            'porta' => ['nullable', 'integer', 'min:0'],
        ], [
            'dispositivo.required' => 'Informe o dispositivo.',
            'dispositivo.string' => 'Informe o dispositivo.',
            'cor.required' => 'Informe a cor.',
            'cor.in' => 'Cor inválida. Use vermelho, amarelo ou verde.',
            'estado.required' => 'Informe o estado.',
            'estado.boolean' => 'Informe um estado válido.',
            'ciclo.boolean' => 'Informe um ciclo válido.',
            'porta.integer' => 'Informe uma porta válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'erros' => $validator->errors(),
            ], 422);
        }

        $comando = [
            'dispositivo' => $validator->validated()['dispositivo'],
            'cor' => $validator->validated()['cor'],
            'estado' => $request->boolean('estado'),
        ];

        if ($request->has('ciclo')) {
            $comando['ciclo'] = $request->boolean('ciclo');
        }

        if ($request->filled('porta')) {
            $comando['porta'] = (int) $request->input('porta');
        }

        try {
            $this->publicar($comando);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'mensagem' => 'Falha ao publicar comando no broker MQTT.',
            ], 502);
        }

        return response()->json([
            'ok' => true,
            'mensagem' => 'Comando publicado com sucesso.',
            'comando' => $comando,
            'topico' => config('mqtt.topico_comando'),
        ]);
    }

    private function publicar(array $comando): void
    {
        $clientId = 'laravel-semaforo-comando-' . bin2hex(random_bytes(4));
        $mqtt = new MqttClient(config('mqtt.host'), config('mqtt.port'), $clientId);

        $mqtt->connect(null, false);

        $mqtt->publish(
            config('mqtt.topico_comando'),
            json_encode($comando, JSON_UNESCAPED_UNICODE),
            MqttClient::QOS_AT_MOST_ONCE,
            false,
        );

        $mqtt->disconnect();
    }
}
