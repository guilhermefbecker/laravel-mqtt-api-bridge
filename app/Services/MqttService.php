<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;

class MqttService
{
    /**
     * Publica um payload JSON no tópico informado e desconecta.
     * O client id é aleatório para não conflitar com outras conexões.
     */
    public function publicar(string $topico, array $payload): void
    {
        $clientId = 'laravel-semaforo-' . bin2hex(random_bytes(4));
        $mqtt = new MqttClient(config('mqtt.host'), config('mqtt.port'), $clientId);

        $mqtt->connect(null, false);

        $mqtt->publish(
            $topico,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            MqttClient::QOS_AT_MOST_ONCE,
            false,
        );

        $mqtt->disconnect();
    }
}
