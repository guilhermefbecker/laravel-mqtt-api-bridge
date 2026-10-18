<?php

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ComandoController;

Route::get('/logs', [LogController::class, 'index']);
Route::get('/logs/ultimo', [LogController::class, 'ultimo']);

// Publica comando no broker MQTT (tópico semaforo/comando, diferente
// de semaforo/estado para o listener não receber de volta).
Route::post('/comandos', [ComandoController::class, 'store']);
