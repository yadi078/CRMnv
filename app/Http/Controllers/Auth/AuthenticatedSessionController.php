<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Guardar información básica de la empresa en sesión para uso rápido en el dashboard
        if ($user->relationLoaded('company') || method_exists($user, 'company')) {
            $company = $user->company;
            if ($company) {
                $request->session()->put('company_id', $company->id);
                $request->session()->put('company_name', $company->nombre_comercial);
            }
        }

        if ($user->esAdmin()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
