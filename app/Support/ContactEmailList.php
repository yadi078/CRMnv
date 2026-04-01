<?php

namespace App\Support;

class ContactEmailList
{
    /**
     * Normaliza una lista de correos: separa por comas, recorta espacios, quita duplicados y vacíos.
     */
    public static function normalize(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $parts = array_map('trim', explode(',', $value));
        $parts = array_values(array_unique(array_filter($parts, fn ($p) => $p !== '')));

        return implode(', ', $parts);
    }
}
