<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Consulta si el usuario autenticado ya fue aprobado (p. ej. polling tras registro).
 */
class CheckApprovalController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'approved' => false,
            ]);
        }

        $user = Auth::user();

        return response()->json([
            'authenticated' => true,
            'approved' => $user->esAdmin() || $user->estaAprobado(),
        ]);
    }
}
