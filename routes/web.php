<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Web da Aplicação
|--------------------------------------------------------------------------
*/

// Página Principal do Visitante (Calendário e Formulário)
Route::get('/', function () {
    return view('public.calendar');
})->name('public.calendar');

// Fluxo Administrativo (Login / Auth)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Rotas protegidas pelo middleware AdminAuthMiddleware
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::put('/agendamentos/{id}', [AdminController::class, 'updateStatus'])->name('agendamentos.update');
        Route::post('/bloquear', [AdminController::class, 'bloquear'])->name('bloquear');
        Route::delete('/desbloquear/{id}', [AdminController::class, 'desbloquear'])->name('desbloquear');
    });
});
