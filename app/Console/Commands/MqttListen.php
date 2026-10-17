<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Escuta o tópico semaforo/estado e salva no banco';

    public function handle()
    {
        $mqtt = new MqttClient('broker.hivemq.com', 1883, 'laravel-semaforo-bridge-listener');

        $settings = (new ConnectionSettings)
            ->setKeepAliveInterval(30)
            ->setReconnectAutomatically(true)
            ->setMaxReconnectAttempts(100)
            ->setDelayBetweenReconnectAttempts(2000);

        $mqtt->connect($settings, false);
        $this->info('Conectado. Aguardando mensagens em semaforo/estado...');

        $mqtt->subscribe('semaforo/estado', function (string $topic, string $message) {
            $data = json_decode($message, true);

            // ignora mensagens que não são JSON (ex.: comandos de texto como "iniciar")
            if (!is_array($data)) {
                $this->warn("Ignorado (não é JSON): {$message}");
                return;
            }

            $validator = Validator::make($data, [
                'dispositivo' => 'required|string',
                'cor'         => 'required|string',
                'porta'       => 'required|integer',
                'estado'      => 'required|boolean',
                'ciclo'       => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $this->error('Payload inválido: ' . $message);
                return;
            }

            $log = Log::create($validator->validated());
            $this->info("Salvo #{$log->id}: {$log->cor} (estado=" . ($log->estado ? 'on' : 'off') . ')');
        }, 0);

        $mqtt->loop(true); // loop infinito

        $mqtt->disconnect();
    }
}