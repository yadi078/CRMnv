<?php

namespace App\Rules;

use App\Support\ContactEmailList;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class CommaSeparatedEmails implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $normalized = ContactEmailList::normalize($value);
        $parts = $normalized === '' ? [] : explode(', ', $normalized);

        if ($parts === []) {
            $fail('Debe indicar al menos un correo electrónico válido.');

            return;
        }

        foreach ($parts as $part) {
            $part = trim($part);
            $v = Validator::make(
                ['e' => $part],
                ['e' => ['required', 'email:rfc']]
            );

            if ($v->fails()) {
                $fail('Cada correo debe ser válido. Separe varias direcciones con comas solo entre correos completos (ej.: uno@dominio.com, otro@dominio.com). No use la coma dentro de un correo (como en nombre@servidor,com).');

                return;
            }
        }
    }
}
