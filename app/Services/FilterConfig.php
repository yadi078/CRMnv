<?php

namespace App\Services;

/**
 * Configuración de campos y operadores para filtros dinámicos.
 */
class FilterConfig
{
    public const OPERATOR_TEXT = [
        'contains'      => 'Contiene',
        'not_contains'  => 'No contiene',
        'starts_with'   => 'Empieza con',
        'ends_with'     => 'Termina con',
        'equals'        => 'Igual a',
        'not_equals'    => 'Diferente de',
    ];

    public const OPERATOR_EXISTENCE = [
        'is_empty'      => 'Está vacío',
        'is_not_empty'  => 'No está vacío',
    ];

    public const OPERATOR_VALUE = [
        'has_value' => 'Tiene valor',
        'no_value'  => 'No tiene valor',
    ];

    public static function textOperators(): array
    {
        return array_keys(self::OPERATOR_TEXT);
    }

    public static function existenceOperators(): array
    {
        return array_keys(self::OPERATOR_EXISTENCE);
    }

    /** Campos para CONTACTOS */
    public static function contactFields(): array
    {
        $textAndExistence = array_merge(self::textOperators(), self::existenceOperators());
        $withValue = array_merge($textAndExistence, ['has_value', 'no_value']);

        return [
            'genero' => ['label' => 'Género', 'column' => 'genero', 'type' => 'select', 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'nombre_completo' => ['label' => 'Nombre', 'column' => 'nombre_completo', 'type' => 'text', 'operators' => $textAndExistence],
            'telefono' => ['label' => 'Teléfono', 'column' => 'telefono', 'type' => 'text', 'operators' => $withValue],
            'celular' => ['label' => 'Celular', 'column' => 'celular', 'type' => 'text', 'operators' => $withValue],
            'email' => ['label' => 'Email', 'column' => 'email', 'type' => 'text', 'operators' => $withValue],
            'departamento' => ['label' => 'Área de trabajo', 'column' => 'departamento', 'type' => 'text', 'operators' => $textAndExistence],
            'puesto_de_trabajo' => ['label' => 'Puesto de trabajo', 'column' => 'puesto_de_trabajo', 'type' => 'text', 'operators' => $textAndExistence],
            'municipio' => ['label' => 'Ciudad', 'column' => 'municipio', 'type' => 'text', 'operators' => $textAndExistence],
            'estado' => ['label' => 'Estado', 'column' => 'estado', 'type' => 'text', 'operators' => $textAndExistence],
            'notas' => ['label' => 'Notas', 'column' => 'notas', 'type' => 'text', 'operators' => $textAndExistence],
            'domicilio' => ['label' => 'Domicilio', 'column' => null, 'type' => 'existence', 'operators' => self::existenceOperators()],
            'email_activo' => ['label' => 'No desea recibir correos', 'column' => 'email_activo', 'type' => 'boolean', 'operators' => ['equals'], 'options' => ['1' => 'Sí desea', '0' => 'No desea']],
        ];
    }

    /** Campos para EMPRESAS */
    public static function companyFields(): array
    {
        $textAndExistence = array_merge(self::textOperators(), self::existenceOperators());
        return [
            'sector' => ['label' => 'Giro', 'column' => 'sector', 'type' => 'text', 'operators' => $textAndExistence],
            'municipio' => ['label' => 'Ciudad', 'column' => 'municipio', 'type' => 'text', 'operators' => $textAndExistence],
            'estado' => ['label' => 'Estado', 'column' => 'estado', 'type' => 'text', 'operators' => $textAndExistence],
            'datos_fiscales' => ['label' => 'Domicilio', 'column' => 'datos_fiscales', 'type' => 'existence', 'operators' => self::existenceOperators()],
            'nombre_comercial' => ['label' => 'Comercial', 'column' => 'nombre_comercial', 'type' => 'text', 'operators' => $textAndExistence],
        ];
    }

    public static function allOperatorLabels(): array
    {
        return array_merge(self::OPERATOR_TEXT, self::OPERATOR_EXISTENCE, self::OPERATOR_VALUE);
    }

    public static function contactGeneroOptions(): array
    {
        return ['' => '—', 'Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro'];
    }

    /** Operadores por campo para contactos (para uso en vistas). */
    public static function contactFieldsWithOptions(): array
    {
        $fields = self::contactFields();
        $fields['genero']['options'] = self::contactGeneroOptions();
        $fields['email_activo']['options'] = ['1' => 'Sí desea', '0' => 'No desea'];
        return $fields;
    }

    /** Operadores por defecto cuando no hay campo seleccionado. */
    public static function defaultOperators(): array
    {
        return array_merge(self::textOperators(), self::existenceOperators(), ['has_value', 'no_value']);
    }
}
