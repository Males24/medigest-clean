<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Medico\DashboardController as MedicoDashboardController;
use App\Http\Controllers\Paciente\PacienteController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\CheckRole;


// PÁGINA INICIAL - visível por todos, mas redireciona logados
Route::get('/', [HomeController::class, 'index'])->name('home');

// Autenticação (apenas guests)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    Route::get('/forgot-password', function () {
        return 'Recuperação de senha ainda não implementada.';
    })->name('password.request');
});

// LOGOUT (para qualquer utilizador autenticado)
Route::middleware('auth')->post('/logout', [LogoutController::class, 'logout'])->name('logout');

// ROTAS PARA ADMIN COM ALIAS(APELIDO)
Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

// ROTAS PARA MÉDICO COM ALIAS(APELIDO)
Route::middleware(['auth', CheckRole::class . ':medico'])->prefix('medico')->group(function () {
    Route::get('/dashboard', [MedicoDashboardController::class, 'index'])->name('medico.dashboard');
});

// ROTAS PARA PACIENTE
Route::middleware(['auth', CheckRole::class . ':paciente'])->group(function () {
    Route::get('/home/paciente', [PacienteController::class, 'index'])->name('paciente.home');
});
