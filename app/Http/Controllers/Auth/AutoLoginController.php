<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Entrada automática cuando el admin aprueba a un usuario.
 * Ruta firmada (signed) para que solo el enlace enviado al usuario sea válido.
 */
class AutoLoginController extends Controller
{
    /**
     * Valida la firma, inicia sesión con el usuario aprobado y redirige a su panel.
     */
    public function __invoke(User $user): RedirectResponse
    {
        if ($user->approval_status !== 'aprobado') {
            return redirect()->route('login')
                ->with('error', 'Esta cuenta aún no ha sido aprobada.');
        }

        if ($user->is_active === false) {
            return redirect()->route('login')
                ->with('error', 'Esta cuenta está desactivada.');
        }

        Auth::login($user, true);

        return redirect()->route('user.dashboard')
            ->with('status', 'Bienvenido. Tu cuenta ha sido aprobada.');
    }
}
