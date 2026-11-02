<?php

use App\Http\Controllers\LogAlteracaoController;
use App\Http\Controllers\SemaforoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Páginas.
Route::view('/', 'index')->name('home');
Route::view('/monitoramento', 'monitoramento')->name('monitoramento');
Route::view('/simulador', 'simulador')->name('simulador');
Route::view('/logs', 'logs')->name('logs');

// Relatórios.
Route::controller(LogAlteracaoController::class)->group(function () {
    Route::get('/logs-alteracao', 'index')->name('logs-alteracao');
    Route::delete('/logs-alteracao/limpar', 'limpar')->name('logs-alteracao.limpar');
});

// Semáforos: "limpar" antes do resource (senão vira o {semaforo} da rota DELETE)
// e URIs de criar/editar em português.
Route::controller(SemaforoController::class)->group(function () {
    Route::delete('semaforos/limpar', 'limpar')->name('semaforos.limpar');
    Route::get('semaforos/criar', 'create')->name('semaforos.create');
    Route::get('semaforos/{semaforo}/editar', 'edit')->name('semaforos.edit');
});

Route::resource('semaforos', SemaforoController::class)
    ->parameters(['semaforos' => 'semaforo'])
    ->except(['show', 'create', 'edit']);

// Usuários: mesma convenção dos semáforos.
Route::controller(UsuarioController::class)->group(function () {
    Route::delete('usuarios/limpar', 'limpar')->name('usuarios.limpar');
    Route::get('usuarios/criar', 'create')->name('usuarios.create');
    Route::get('usuarios/{usuario}/editar', 'edit')->name('usuarios.edit');
});

Route::resource('usuarios', UsuarioController::class)
    ->parameters(['usuarios' => 'usuario'])
    ->except(['show', 'create', 'edit']);

// Redirects de compatibilidade: URLs antigas do frontend HTML estático
// (public/semaforo/application/... e caminhos relativos usados no original).
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
