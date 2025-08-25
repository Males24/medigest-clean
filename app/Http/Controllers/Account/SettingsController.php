<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Preferências de interface/notificações/idioma.
 * Pacientes usam "account.settings_app"; restantes usam "account.settings".
 */
class SettingsController extends Controller
{
    /**
     * GET /account/settings
     * Escolhe a view conforme o role.
     */
    public function edit()
    {
        $user  = auth()->user();

        // defaults + merge com o que estiver guardado
        $prefs = array_merge([
            'theme'         => 'system',
            'language'      => 'pt',
            'notify_email'  => true,
            'notify_push'   => false,
            'weekly_digest' => true,
        ], (array) ($user->settings ?? []));

        $view = $user && $user->role === 'paciente'
            ? 'account.settings_app'
            : 'account.settings';

        return view($view, compact('user', 'prefs'));
    }

    /**
     * PUT /account/settings
     * Grava e define cookie de locale (1 ano).
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'theme'         => ['required', 'in:light,dark,system'],
            'language'      => ['required', 'in:pt,en,es'],
            'notify_email'  => ['nullable', 'boolean'],
            'notify_push'   => ['nullable', 'boolean'],
            'weekly_digest' => ['nullable', 'boolean'],
        ]);

        $prefs = [
            'theme'         => $data['theme'],
            'language'      => $data['language'],
            'notify_email'  => (bool) ($data['notify_email']  ?? false),
            'notify_push'   => (bool) ($data['notify_push']   ?? false),
            'weekly_digest' => (bool) ($data['weekly_digest'] ?? false),
        ];

        $user = $request->user();
        $user->settings = array_merge((array) $user->settings, $prefs);
        $user->save();

        return back()
            ->withCookie(cookie('mg_locale', $prefs['language'], 60 * 24 * 365))
            ->with('success_key', 'messages.settings.saved');
    }
}
