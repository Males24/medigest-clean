<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Perfil do utilizador autenticado (dados pessoais + password).
 * Pacientes usam a view "account.profile_app"; restantes usam "account.profile".
 */
class ProfileController extends Controller
{
    /**
     * GET /account/profile
     * Mostra o formulário de perfil (view por role).
     */
    public function edit()
    {
        $user = auth()->user();
        $view = $user && $user->role === 'paciente'
            ? 'account.profile_app'
            : 'account.profile';

        return view($view, ['user' => $user]);
    }

    /**
     * PUT /account/profile
     * Atualiza nome/email/phone e avatar (upload/remoção).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'          => ['required','string','max:255'],
            'email'         => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone'         => ['nullable','string','max:30'],
            'avatar'        => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:2048'],
            'remove_avatar' => ['nullable','boolean'],
        ]);

        // Avatar
        $remove = $request->boolean('remove_avatar');
        if ($remove) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        // Dados
        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ])->save();

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    /**
     * PUT /account/password
     * Atualiza a password (nova + confirmação).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $user = $request->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password atualizada com sucesso.');
    }
}
