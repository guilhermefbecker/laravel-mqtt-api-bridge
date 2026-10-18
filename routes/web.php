<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');
Route::view('/monitoramento', 'monitoramento')->name('monitoramento');
Route::view('/simulador', 'simulador')->name('simulador');
Route::view('/logs', 'logs')->name('logs');
Route::view('/logs-alteracao', 'logs-alteracao')->name('logs-alteracao');
