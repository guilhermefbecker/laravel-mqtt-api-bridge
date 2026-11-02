<?php

namespace App\Console\Commands;

use App\Services\LogEstadoService;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Escuta o tópico semaforo/estado e salva no banco';

    public function handle(LogEstadoService $logs): int
    {
        $topico = config('mqtt.topico_estado');
        $clientId = 'laravel-semaforo-listener-' . bin2hex(random_bytes(4));

        $mqtt = new MqttClient(config('mqtt.host'), config('mqtt.port'), $clientId);

        $settings = (new ConnectionSettings)
            ->setKeepAliveInterval(30)
            ->setReconnectAutomatically(true)
            ->setMaxReconnectAttempts(100)
            ->setDelayBetweenReconnectAttempts(2000);

        $mqtt->connect($settings, false);
        $this->info("Conectado. Aguardando mensagens em {$topico}...");

        $mqtt->subscribe($topico, function (string $topic, string $message) use ($logs) {
            $resultado = $logs->salvarMensagem($message);

            if ($resultado['status'] === 'ignorado') {
                $this->warn("Ignorado (não é JSON): {$message}");
            } elseif ($resultado['status'] === 'invalido') {
                $this->error('Payload inválido: ' . $message);
            } else {
                $log = $resultado['log'];
                $this->info("Salvo #{$log->id}: {$log->cor} (estado=" . ($log->estado ? 'on' : 'off') . ')');
            }
        }, 0);

        $mqtt->loop(true); // loop infinito

        $mqtt->disconnect();

        return self::SUCCESS;
    }
}
