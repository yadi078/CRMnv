<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Validation\ValidationException;

/**
 * Servicio para resolver y vincular empresas a usuarios usando el RFC.
 */
class UserCompanyLinkService
{
    /**
     * Busca la empresa por RFC y lanza error de validación si no existe.
     *
     * @throws ValidationException
     */
    public function resolveCompanyByRfc(string $rfc): Company
    {
        $cleanRfc = strtoupper(trim($rfc));

        $company = Company::where('rfc', $cleanRfc)->first();

        if (! $company) {
            throw ValidationException::withMessages([
                'company_rfc' => 'La empresa aún no ha sido registrada por el administrador.',
            ]);
        }

        return $company;
    }
}

