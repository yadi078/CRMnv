<?php

namespace App\DataTransferObjects;

/**
 * Especificación de un filtro dinámico.
 * Estructura: { field, operator, value }
 */
final class FilterSpec
{
    public function __construct(
        public string $field,
        public string $operator,
        public mixed $value = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            field: $data['field'] ?? '',
            operator: $data['operator'] ?? 'equals',
            value: $data['value'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'operator' => $this->operator,
            'value' => $this->value,
        ];
    }

    public function isValid(): bool
    {
        if ($this->field === '' || $this->operator === '') {
            return false;
        }

        // Operadores que NO requieren valor.
        // Para los demás, si el valor viene vacío/null, el filtro se ignora.
        $opNoValue = ['is_empty', 'is_not_empty', 'has_value', 'no_value'];
        if (in_array($this->operator, $opNoValue, true)) {
            return true;
        }

        if (is_array($this->value)) {
            $normalized = array_values(array_filter(
                array_map(
                    fn ($v) => is_string($v) ? trim($v) : $v,
                    $this->value
                ),
                fn ($v) => $v !== null && $v !== ''
            ));
            return count($normalized) > 0;
        }

        return $this->value !== null && $this->value !== '';
    }
}
