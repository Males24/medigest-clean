<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Criaremos esta view depois
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            return match ($role) {
                'admin'    => redirect()->intended('/admin/dashboard'),
                'medico'   => redirect()->intended('/medico/dashboard'),
                'paciente' => redirect()->intended('/home/paciente'),
                default    => abort(403, 'Acesso não autorizado.'),
            };
        }

        return back()->withErrors([
            'email' => 'As credenciais estão incorretas.',
        ]);
    }
}