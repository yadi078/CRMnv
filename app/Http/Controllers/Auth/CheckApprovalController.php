<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Comprueba si el usuario recién registrado (pendiente en sesión) ya fue aprobado.
 * Si lo fue, devuelve la URL firmada para entrar automáticamente a su panel.
 */
class CheckApprovalController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->session()->get('pending_approval_user_id');

        if (! $userId) {
            return response()->json(['approved' => false]);
        }

        $user = User::find($userId);

        if (! $user || $user->approval_status !== 'aprobado') {
            return response()->json(['approved' => false]);
        }

        $url = URL::temporarySignedRoute(
            'auth.auto-login',
            now()->addMinutes(5),
            ['user' => $user->id]
        );

        $request->session()->forget('pending_approval_user_id');

        return response()->json(['approved' => true, 'url' => $url]);
    }
}
