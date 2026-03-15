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
        return $this->field !== '' && $this->operator !== '';
    }
}
