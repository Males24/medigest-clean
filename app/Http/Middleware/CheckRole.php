<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'medico' => redirect()->route('medico.dashboard'),
                'paciente' => redirect()->route('paciente.home'),
                default => abort(403),
            };
        }

        return $next($request);
    }
}
