<?php

use App\Http\Controllers\ComandoController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

// Logs recebidos do MQTT.
Route::get('/logs', [LogController::class, 'index'])->name('api.logs.index');
Route::get('/logs/ultimo', [LogController::class, 'ultimo'])->name('api.logs.ultimo');

// Publica comando no broker MQTT (tópico semaforo/comando, diferente
// de semaforo/estado para o listener não receber de volta).
Route::post('/comandos', [ComandoController::class, 'store'])->name('api.comandos.store');
