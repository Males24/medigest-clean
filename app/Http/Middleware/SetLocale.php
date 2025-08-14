<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SetLocale
{
    /** Normaliza valores (cookie/settings) para códigos suportados */
    private function normalize(string $val): string
    {
        $val = str_replace('_','-', strtolower($val));
        return match ($val) {
            'pt', 'pt-pt'           => 'pt',
            'en', 'en-us', 'en-gb'  => 'en',
            'es', 'es-es'           => 'es',
            default                 => config('app.locale', 'pt'),
        };
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Cookie -> vale imediatamente após guardar nas Configurações
        $fromCookie = $request->cookie('mg_locale');

        // 2) Preferências guardadas no utilizador
        $userLang = optional($request->user())->settings['language'] ?? null;

        // 3) Fallback .env/config
        $candidate = $fromCookie ?: $userLang ?: config('app.locale');

        // Normaliza + valida
        $lang = $this->normalize((string) $candidate);
        $supported = ['pt', 'en', 'es'];
        if (!in_array($lang, $supported, true)) {
            $lang = config('app.fallback_locale', 'pt');
        }

        app()->setLocale($lang);
        Carbon::setLocale($lang); // opcional: nomes de meses/dias em Carbon

        return $next($request);
    }
}
