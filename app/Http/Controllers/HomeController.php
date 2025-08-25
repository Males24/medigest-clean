<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Página inicial.
 *
 * Se o utilizador estiver autenticado, redireciona para a
 * área certa consoante o role (admin|medico|paciente).
 * Caso contrário, devolve a view de landing page (guests).
 */
class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'medico'   => redirect()->route('medico.dashboard'),
                'paciente' => redirect()->route('paciente.home'),
                default    => abort(403, 'Acesso não autorizado.'),
            };
        }

        return view('home');
    }
}
