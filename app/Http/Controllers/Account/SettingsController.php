<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $user  = auth()->user();
        $prefs = array_merge([
            'theme'         => 'system',
            'language'      => 'pt', // default simples
            'notify_email'  => true,
            'notify_push'   => false,
            'weekly_digest' => true,
        ], (array) ($user->settings ?? []));

        return view('account.settings', compact('user', 'prefs'));
    }

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

        // guarda cookie para o próximo request já vir no idioma escolhido
        return back()
            ->withCookie(cookie('mg_locale', $prefs['language'], 60 * 24 * 365)) // 1 ano
            // guarda a CHAVE de tradução; o texto é resolvido já no novo locale (ver view)
            ->with('success_key', 'messages.settings.saved');
    }
}
