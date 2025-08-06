<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedToRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            return match ($role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'medico'   => redirect()->route('medico.dashboard'),
                'paciente' => redirect()->route('paciente.home'),
                default    => abort(403, 'Acesso não autorizado.'),
            };
        }

        return $next($request);
    }
}
