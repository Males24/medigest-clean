<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'medico' => redirect()->route('medico.dashboard'),
                'paciente' => redirect()->route('paciente.home'),
                default => abort(403, 'Acesso não autorizado.'),
            };
        }

        return view('home'); // Página inicial só para guests
    }
}
