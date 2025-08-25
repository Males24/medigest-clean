<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

/**
 * Registo de utilizadores (fluxo simples).
 *
 * - register(): cria o utilizador como 'paciente' por omissão
 *   e faz redirect para o ecrã de login com sucesso.
 */
class RegisterController extends Controller
{
    /**
     * (Opcional, com modais quase não usas)
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Regista um novo utilizador (sempre como 'paciente').
     * No fim, redireciona para a home abrindo o modal de login.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'paciente',
        ]);

        // abre o modal de login
        return redirect()
            ->route('login.form')
            ->with('status', 'Conta criada com sucesso! Faz login para continuar.');
    }
}
