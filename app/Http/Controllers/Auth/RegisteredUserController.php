<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * El usuario queda pendiente de aprobación del administrador.
     * No puede iniciar sesión hasta ser aprobado.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'approval_status' => 'pendiente',
        ]);

        $user->assignRole('usuario');

        event(new Registered($user));

        return redirect()->route('register.pending')
            ->with('status', 'Registro exitoso. Un administrador debe aprobar tu cuenta antes de que puedas iniciar sesión.');
    }
}
