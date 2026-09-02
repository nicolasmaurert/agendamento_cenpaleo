<?php

use App\Http\Controllers\AgendamentoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API REST do Agendamento Cenpaleo
|--------------------------------------------------------------------------
*/

Route::get('/disponibilidade', [AgendamentoController::class, 'disponibilidade']);
Route::post('/agendar', [AgendamentoController::class, 'agendar']);
