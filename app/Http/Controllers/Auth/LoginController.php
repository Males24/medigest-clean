<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Login com suporte a AJAX (modais) e redirect por role.
 */
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // (não usado com o modal, mas mantido)
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;
            $target = match ($role) {
                'admin'    => '/admin/dashboard',
                'medico'   => '/medico/dashboard',
                'paciente' => '/home/paciente',
                default    => abort(403, 'Acesso não autorizado.'),
            };

            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'redirect_to' => $target]);
            }

            return redirect()->intended($target);
        }

        $msg = __('auth.failed');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $msg,
                'form'    => 'login',
                'errors'  => ['email' => [$msg]],
            ], 422);
        }

        return back()
            ->withInput(['form_name' => 'login'])
            ->withErrors(['email' => $msg]);
    }
}
