<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ConsultaController as AdminConsultaController;
use App\Http\Controllers\Admin\ConfiguracaoHorarioController as AdminConfiguracaoHorarioController;
use App\Http\Controllers\Admin\MedicoController as AdminMedicoController;
use App\Http\Controllers\Medico\DashboardController as MedicoDashboardController;
use App\Http\Controllers\Medico\ConsultaController as MedicoConsultaController;
use App\Http\Controllers\Paciente\PacienteController;
use App\Http\Controllers\Paciente\ConsultaController as PacienteConsultaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\SlotsController as ApiSlotsController;
use App\Http\Controllers\Api\ConsultaTiposController as ApiConsultaTiposController;
use App\Http\Middleware\CheckRole;

// PÁGINA INICIAL
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth (guests)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    Route::get('/forgot-password', fn () => 'Recuperação de senha ainda não implementada.')->name('password.request');
});

// LOGOUT
Route::middleware('auth')->post('/logout', [LogoutController::class, 'logout'])->name('logout');

/**
 * API interna (protegida por auth via sessão web)
 * – Slots de disponibilidade
 * – Metadados dos tipos de consulta (lead/horizonte/duração)
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/api/slots', [ApiSlotsController::class, 'index'])->name('api.slots');
    Route::get('/api/consulta-tipos', [ApiConsultaTiposController::class, 'index'])->name('api.consulta_tipos.index');
    Route::get('/api/consulta-tipos/{slug}', [ApiConsultaTiposController::class, 'show'])->name('api.consulta_tipos.show');
});

// ADMIN
Route::middleware(['auth', CheckRole::class . ':admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Configuração de horários
        Route::get('/horarios/configurar', [AdminConfiguracaoHorarioController::class, 'configurar'])
            ->name('admin.horarios.configurar');
        Route::put('/horarios/configurar', [AdminConfiguracaoHorarioController::class, 'atualizarTodos'])
            ->name('admin.horarios.atualizarTodos');

        Route::resource('horarios', AdminConfiguracaoHorarioController::class)
            ->names('admin.horarios')
            ->except(['create', 'store', 'show', 'destroy']);

        // Especialidades
        Route::resource('especialidades', \App\Http\Controllers\Admin\EspecialidadeController::class)
            ->names('admin.especialidades');

        // Médicos
        Route::resource('medicos', AdminMedicoController::class)
            ->names('admin.medicos');

        // JSON: médicos por especialidade
        Route::get('/especialidades/{especialidade}/medicos', [AdminMedicoController::class, 'porEspecialidade'])
            ->name('admin.especialidades.medicos');

        // Consultas
        Route::get('/consultas', [AdminConsultaController::class, 'index'])->name('admin.consultas.index');
        Route::get('/consultas/criar', [AdminConsultaController::class, 'create'])->name('admin.consultas.create');
        Route::post('/consultas', [AdminConsultaController::class, 'store'])->name('admin.consultas.store');
        Route::post('/consultas/{consulta}/cancelar', [AdminConsultaController::class, 'cancelar'])->name('admin.consultas.cancelar');
        
    });

// MÉDICO
Route::middleware(['auth', CheckRole::class . ':medico'])
    ->prefix('medico')
    ->group(function () {
        Route::get('/dashboard', [MedicoDashboardController::class, 'index'])->name('medico.dashboard');

        Route::get('/consultas', [MedicoConsultaController::class, 'index'])->name('medico.consultas.index');
        Route::get('/consultas/criar', [MedicoConsultaController::class, 'create'])->name('medico.consultas.create');
        Route::post('/consultas', [MedicoConsultaController::class, 'store'])->name('medico.consultas.store');
        Route::post('/consultas/{consulta}/cancelar', [MedicoConsultaController::class, 'cancelar'])->name('medico.consultas.cancelar');

    });

// PACIENTE
Route::middleware(['auth', CheckRole::class . ':paciente'])->group(function () {
    Route::get('/home/paciente', [PacienteController::class, 'index'])->name('paciente.home');

    Route::get('/consultas', [PacienteConsultaController::class, 'index'])->name('paciente.consultas.index');
    Route::get('/consultas/criar', [PacienteConsultaController::class, 'create'])->name('paciente.consultas.create');
    Route::post('/consultas', [PacienteConsultaController::class, 'store'])->name('paciente.consultas.store');
    Route::post('/consultas/{consulta}/cancelar', [PacienteConsultaController::class, 'cancelar'])->name('paciente.consultas.cancelar');

});
