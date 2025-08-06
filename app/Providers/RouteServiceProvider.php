<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectIfAuthenticatedToRole;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        // Regista o middleware globalmente se quiseres
        $router->aliasMiddleware('redirect.authenticated', RedirectIfAuthenticatedToRole::class);

        // Define os grupos se necessário (exemplo: guest-only)
        $router->middlewareGroup('guest-only', [
            'redirect.authenticated',
        ]);

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
