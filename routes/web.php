<?php

use App\Http\Controllers\SemaforoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');
Route::view('/monitoramento', 'monitoramento')->name('monitoramento');
Route::view('/simulador', 'simulador')->name('simulador');
Route::view('/logs', 'logs')->name('logs');
Route::view('/logs-alteracao', 'logs-alteracao')->name('logs-alteracao');

// A rota "limpar" precisa vir antes do resource: senão "limpar" é
// interpretado como o {semaforo} da rota DELETE do resource.
Route::delete('semaforos/limpar', [SemaforoController::class, 'limpar'])
    ->name('semaforos.limpar');

// URIs de create/edit em português (o resource usa "create"/"edit").
Route::get('semaforos/criar', [SemaforoController::class, 'create'])
    ->name('semaforos.create');
Route::get('semaforos/{semaforo}/editar', [SemaforoController::class, 'edit'])
    ->name('semaforos.edit');

Route::resource('semaforos', SemaforoController::class)
    ->parameters(['semaforos' => 'semaforo'])
    ->except(['show', 'create', 'edit']);
