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

// ---------------------------------------------------------------------------
// Redirects de compatibilidade: URLs antigas do frontend HTML estático
// (public/semaforo/application/... e caminhos relativos usados no original).
// Mantidos para links antigos não retornarem 404.
// ---------------------------------------------------------------------------
$redirectsAntigos = [
    '/simulador/index.html' => '/simulador',
    '/monitoramento/index.html' => '/monitoramento',
    '/cadastros_semaforo/index.html' => '/semaforos',
    '/cadastros_usuarios/index.html' => '/usuarios',
    '/logs_alteracao/index.html' => '/logs-alteracao',
    '/logs/index.html' => '/logs',
    '/semaforo/application/simulador/index.html' => '/simulador',
    '/semaforo/application/monitoramento/index.html' => '/monitoramento',
    '/semaforo/application/cadastros_semaforo/index.html' => '/semaforos',
    '/semaforo/application/cadastros_usuarios/index.html' => '/usuarios',
    '/semaforo/application/logs_alteracao/index.html' => '/logs-alteracao',
    '/semaforo/application/logs/index.html' => '/logs',
    '/semaforo/application/index.html' => '/',
];

foreach ($redirectsAntigos as $antiga => $nova) {
    Route::redirect($antiga, $nova);
}
