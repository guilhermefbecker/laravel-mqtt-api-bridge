<?php

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;

Route::get('/logs', [LogController::class, 'index']);
Route::get('/logs/ultimo', [LogController::class, 'ultimo']);
