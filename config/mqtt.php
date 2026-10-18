<?php

return [
    'host' => env('MQTT_HOST', 'broker.hivemq.com'),
    'port' => (int) env('MQTT_PORT', 1883),
    'topico_comando' => env('MQTT_TOPICO_COMANDO', 'semaforo/comando'),
];
