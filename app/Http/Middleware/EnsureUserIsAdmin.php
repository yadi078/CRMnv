<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a rutas de administración.
 * Solo usuarios con rol "admin" pueden continuar.
 * Los usuarios normales son redirigidos a la vista de usuario.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->esAdmin()) {
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
