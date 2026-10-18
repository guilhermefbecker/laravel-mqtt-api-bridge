<?php

use App\Http\Controllers\LogAlteracaoController;
use App\Http\Controllers\SemaforoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');
Route::view('/monitoramento', 'monitoramento')->name('monitoramento');
Route::view('/simulador', 'simulador')->name('simulador');
Route::view('/logs', 'logs')->name('logs');

Route::get('/logs-alteracao', [LogAlteracaoController::class, 'index'])->name('logs-alteracao');
Route::delete('/logs-alteracao/limpar', [LogAlteracaoController::class, 'limpar'])->name('logs-alteracao.limpar');

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

// Mesma convenção dos semáforos: limpar antes do resource,
// URIs criar/editar em português.
Route::delete('usuarios/limpar', [UsuarioController::class, 'limpar'])
    ->name('usuarios.limpar');

Route::get('usuarios/criar', [UsuarioController::class, 'create'])
    ->name('usuarios.create');
Route::get('usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
    ->name('usuarios.edit');

Route::resource('usuarios', UsuarioController::class)
    ->parameters(['usuarios' => 'usuario'])
    ->except(['show', 'create', 'edit']);
