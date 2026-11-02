<?php

namespace App\Http\Controllers;

use App\Models\LogAlteracao;
use App\Services\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComandoController extends Controller
{
    // Valida, publica no MQTT, registra a auditoria e devolve o JSON.
    public function store(Request $request, MqttService $mqtt): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'estado' => ['required', 'string', 'in:vermelho,amarelo,verde,intermitente,ciclo,desligado'],
            'cor' => ['nullable', 'string', 'required_if:estado,vermelho,amarelo,verde', 'same:estado'],
            'tempoVermelho' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
            'tempoAmarelo' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
            'tempoVerde' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
        ], [
            'estado.required' => 'Informe o estado.',
            'estado.in' => 'Estado inválido. Use vermelho, amarelo, verde, intermitente, ciclo ou desligado.',
            'cor.required_if' => 'Informe a cor.',
            'cor.same' => 'A cor deve ser igual ao estado.',
            'tempoVermelho.required_if' => 'Informe o tempo vermelho.',
            'tempoVermelho.integer' => 'Informe o tempo vermelho em segundos.',
            'tempoVermelho.min' => 'O tempo vermelho deve ser de no mínimo 1 segundo.',
            'tempoVermelho.max' => 'O tempo vermelho deve ser de no máximo 3600 segundos.',
            'tempoAmarelo.required_if' => 'Informe o tempo amarelo.',
            'tempoAmarelo.integer' => 'Informe o tempo amarelo em segundos.',
            'tempoAmarelo.min' => 'O tempo amarelo deve ser de no mínimo 1 segundo.',
            'tempoAmarelo.max' => 'O tempo amarelo deve ser de no máximo 3600 segundos.',
            'tempoVerde.required_if' => 'Informe o tempo verde.',
            'tempoVerde.integer' => 'Informe o tempo verde em segundos.',
            'tempoVerde.min' => 'O tempo verde deve ser de no mínimo 1 segundo.',
            'tempoVerde.max' => 'O tempo verde deve ser de no máximo 3600 segundos.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'erros' => $validator->errors(),
            ], 422);
        }

        $comando = $validator->validated();

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
            'enviou',
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
        $rotulos = [
            'vermelho' => 'Vermelho',
            'amarelo' => 'Amarelo',
            'verde' => 'Verde',
            'intermitente' => 'Intermitente',
            'desligado' => 'Desligado',
        ];

        if ($comando['estado'] === 'ciclo') {
            return sprintf(
                'Ciclo (vermelho %ds, amarelo %ds, verde %ds)',
                $comando['tempoVermelho'],
                $comando['tempoAmarelo'],
                $comando['tempoVerde'],
            );
        }

        return $rotulos[$comando['estado']] ?? $comando['estado'];
    }
}
