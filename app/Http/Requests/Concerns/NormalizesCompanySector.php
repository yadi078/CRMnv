<?php

namespace App\Http\Requests\Concerns;

/**
 * Convierte sector enviado como sector[] (array) en un string para validación y persistencia.
 */
trait NormalizesCompanySector
{
    protected function prepareForValidation(): void
    {
        $sector = $this->input('sector');

        if (is_array($sector)) {
            $parts = array_values(array_filter(
                array_map(static fn ($s) => trim((string) $s), $sector),
                static fn ($s) => $s !== ''
            ));
            $sector = implode(', ', $parts);
        } elseif (is_string($sector)) {
            $sector = trim($sector);
        } else {
            $sector = '';
        }

        $this->merge(['sector' => $sector]);
    }
}
