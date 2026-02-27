<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\View\View;

/**
 * Trait para resolver la vista según rol admin vs usuario normal.
 * Elimina la repetición de if (!auth()->user()->esAdmin()) en controladores.
 */
trait ResolvesAdminUserView
{
    /**
     * Devuelve la vista correspondiente según el rol del usuario.
     */
    protected function resolveView(string $adminView, string $userView, array $data = []): View
    {
        $view = auth()->user()->esAdmin() ? $adminView : $userView;
        return view($view, $data);
    }
}
