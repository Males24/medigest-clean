<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ConsultaController as AdminConsultaController;
use App\Http\Controllers\Admin\ConfiguracaoHorarioController as AdminConfiguracaoHorarioController;
use App\Http\Controllers\Admin\EspecialidadeController as AdminEspecialidadeController;
use App\Http\Controllers\Admin\MedicoController as AdminMedicoController;

// Médico
use App\Http\Controllers\Medico\DashboardController as MedicoDashboardController;
use App\Http\Controllers\Medico\CalendarioController as MedicoCalendarioController;
use App\Http\Controllers\Medico\ConsultaController as MedicoConsultaController;

// Paciente
use App\Http\Controllers\Paciente\PacienteController;
use App\Http\Controllers\Paciente\ConsultaController as PacienteConsultaController;
use App\Http\Controllers\Paciente\EspecialidadesController;
use App\Http\Controllers\Paciente\MedicosController;

// Diversos / Conta / API
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\SettingsController;
use App\Http\Controllers\Api\SlotsController as ApiSlotsController;
use App\Http\Controllers\Api\ConsultaTiposController as ApiConsultaTiposController;

use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| PÁGINA INICIAL
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| AVATAR FALLBACK (SVG com iniciais)
|--------------------------------------------------------------------------
*/
Route::get('/avatar/{user}.svg', [AvatarController::class, 'initials'])
    ->whereNumber('user')
    ->name('avatar.initials');

/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO (apenas guests)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect()->route('home')->with('auth_modal', 'login'))->name('login.form');
    Route::get('/register', fn () => redirect()->route('home')->with('auth_modal', 'register'))->name('register.form');
    Route::get('/forgot-password', fn () => redirect()->route('home')->with('auth_modal', 'forgot'))->name('password.request');

    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return redirect()->route('home')->with('auth_modal', 'forgot')->with('status', __($status));
    })->name('password.email');
});

/*
|--------------------------------------------------------------------------
| LOGOUT (autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->post('/logout', [LogoutController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| API INTERNA (autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('api')
    ->as('api.')
    ->group(function () {
        Route::get('/slots', [ApiSlotsController::class, 'index'])->name('slots');
        Route::get('/consulta-tipos', [ApiConsultaTiposController::class, 'index'])->name('consulta_tipos.index');
        Route::get('/consulta-tipos/{slug}', [ApiConsultaTiposController::class, 'show'])->name('consulta_tipos.show');

        Route::get('/especialidades/{id}/medicos', [MedicosController::class, 'medicosPorEspecialidade'])
            ->whereNumber('id')
            ->name('especialidades.medicos');
    });

/*
|--------------------------------------------------------------------------
| ÁREA ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class . ':admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/charts', [AdminDashboardController::class, 'charts'])->name('dashboard.charts');

        Route::get('/horarios/configurar', [AdminConfiguracaoHorarioController::class, 'configurar'])->name('horarios.configurar');
        Route::put('/horarios/configurar', [AdminConfiguracaoHorarioController::class, 'atualizarTodos'])->name('horarios.atualizarTodos');
        Route::resource('horarios', AdminConfiguracaoHorarioController::class)
            ->names('horarios')
            ->except(['create', 'store', 'show', 'destroy']);

        Route::resource('especialidades', AdminEspecialidadeController::class)
            ->names('especialidades')
            ->except(['show']);

        Route::resource('medicos', AdminMedicoController::class)->names('medicos');
        Route::get('/especialidades/{especialidade}/medicos', [AdminMedicoController::class, 'porEspecialidade'])
            ->name('especialidades.medicos');

        Route::get('/consultas', [AdminConsultaController::class, 'index'])->name('consultas.index');
        Route::get('/consultas/criar', [AdminConsultaController::class, 'create'])->name('consultas.create');
        Route::post('/consultas', [AdminConsultaController::class, 'store'])->name('consultas.store');
        Route::post('/consultas/{consulta}/cancelar', [AdminConsultaController::class, 'cancelar'])->name('consultas.cancelar');
    });

/*
|--------------------------------------------------------------------------
| ÁREA MÉDICO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class . ':medico'])
    ->prefix('medico')
    ->as('medico.')
    ->group(function () {
        Route::get('/dashboard', [MedicoDashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendario', [MedicoCalendarioController::class, 'index'])->name('calendario');

        Route::get('/consultas', [MedicoConsultaController::class, 'index'])->name('consultas.index');
        Route::get('/consultas/criar', [MedicoConsultaController::class, 'create'])->name('consultas.create');
        Route::post('/consultas', [MedicoConsultaController::class, 'store'])->name('consultas.store');

        // >>> NOVAS ROTAS (CONFIRMAR / REJEITAR) <<<
        Route::post('/consultas/{consulta}/confirmar', [MedicoConsultaController::class, 'confirmar'])
            ->name('consultas.confirmar');
        Route::post('/consultas/{consulta}/rejeitar', [MedicoConsultaController::class, 'rejeitar'])
            ->name('consultas.rejeitar');

        // Mantida (não aparece no UI)
        Route::post('/consultas/{consulta}/cancelar', [MedicoConsultaController::class, 'cancelar'])->name('consultas.cancelar');
    });

/*
|--------------------------------------------------------------------------
| ÁREA PACIENTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class . ':paciente'])->group(function () {
    Route::get('/home/paciente', [PacienteController::class, 'index'])->name('paciente.home');

    Route::get('/consultas', [PacienteConsultaController::class, 'index'])->name('paciente.consultas.index');
    Route::get('/consultas/todas', [PacienteConsultaController::class, 'todas'])->name('paciente.consultas.todas');
    Route::get('/consultas/criar', [PacienteConsultaController::class, 'create'])->name('paciente.consultas.create');
    Route::post('/consultas', [PacienteConsultaController::class, 'store'])->name('paciente.consultas.store');
    Route::post('/consultas/{consulta}/cancelar', [PacienteConsultaController::class, 'cancelar'])->name('paciente.consultas.cancelar');

    Route::get('/especialidades', [EspecialidadesController::class, 'index'])->name('paciente.especialidades.index');
    Route::get('/especialidades/{especialidade}', [EspecialidadesController::class, 'show'])->whereNumber('especialidade')->name('paciente.especialidades.show');
    Route::get('/corpo-clinico', [MedicosController::class, 'index'])->name('paciente.medicos.index');
    Route::get('/corpo-clinico/{medico}', [MedicosController::class, 'show'])->whereNumber('medico')->name('paciente.medicos.show');

    Route::get('/canais', fn () => view('paciente.canais.index'))->name('paciente.canais.index');

    Route::get('/privacidade', fn () => view('paciente.legal.privacy'))->name('paciente.legal.privacy');
    Route::get('/termos', fn () => view('paciente.legal.terms'))->name('paciente.legal.terms');

    Route::post('/alertas/{notification}/dismiss', [PacienteController::class, 'dismissAlert'])
    ->name('paciente.alerts.dismiss');
});

/*
|--------------------------------------------------------------------------
| CONTA (perfil / settings) – autenticados
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account/profile',  [ProfileController::class, 'edit'])->name('account.profile');
    Route::put('/account/profile',  [ProfileController::class, 'update'])->name('account.profile.update');
    Route::put('/account/password', [ProfileController::class, 'updatePassword'])->name('account.password.update');

    Route::get('/account/settings', [SettingsController::class, 'edit'])->name('account.settings');
    Route::put('/account/settings', [SettingsController::class, 'update'])->name('account.settings.update');
});
