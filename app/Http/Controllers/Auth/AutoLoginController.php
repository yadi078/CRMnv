<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Login vía enlace firmado (p. ej. tras aprobación del administrador).
 */
class AutoLoginController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        auth()->login($user);
        $request->session()->regenerate();

        if ($user->esAdmin()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }
}
