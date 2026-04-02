<?php

namespace App\Services;

use App\Models\Contact;

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
            // MULTI (select multiple + IN/OR)
            'genero' => ['label' => 'Género', 'column' => 'genero', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'departamento' => ['label' => 'Área de trabajo', 'column' => 'departamento', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'puesto_de_trabajo' => ['label' => 'Puesto de trabajo', 'column' => 'puesto_de_trabajo', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'municipio' => ['label' => 'Ciudad', 'column' => 'municipio', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'estado' => ['label' => 'Estado', 'column' => 'estado', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],

            // Semáforo de prospecto (mismo status_color que en listados / badges)
            'status_color' => ['label' => 'Estado de prospecto (color)', 'column' => 'status_color', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],

            // RELACIONAL: ejecutivo asignado (usuario + administradores; vía assigned_user_id / empresa)
            'comercial' => ['label' => 'Ejecutivo', 'column' => null, 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],

            // TEXT (LIKE / equals)
            'nombre_completo' => ['label' => 'Nombre', 'column' => 'nombre_completo', 'type' => 'text', 'operators' => $textAndExistence],
            'telefono' => ['label' => 'Teléfono', 'column' => 'telefono', 'type' => 'text', 'operators' => $withValue],
            'celular' => ['label' => 'Celular', 'column' => 'celular', 'type' => 'text', 'operators' => $withValue],
            'email' => ['label' => 'Email', 'column' => 'email', 'type' => 'text', 'operators' => $withValue],
            'notas' => ['label' => 'Notas', 'column' => 'notas', 'type' => 'text', 'operators' => $textAndExistence],

            'domicilio' => ['label' => 'Domicilio', 'column' => null, 'type' => 'existence', 'operators' => self::existenceOperators()],
            'no_recibir_correos' => ['label' => 'No desea recibir correos', 'column' => 'email_activo', 'type' => 'checkbox', 'operators' => ['equals']],
        ];
    }

    /** Campos para EMPRESAS */
    public static function companyFields(): array
    {
        $textAndExistence = array_merge(self::textOperators(), self::existenceOperators());
        return [
            'sector' => ['label' => 'Giro', 'column' => 'sector', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'municipio' => ['label' => 'Ciudad', 'column' => 'municipio', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'estado' => ['label' => 'Estado', 'column' => 'estado', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],
            'status_color' => ['label' => 'Estado de prospecto (color)', 'column' => 'status_color', 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],

            // Ejecutivo asignado (mismo catálogo que en contactos: roles usuario + admin)
            'comercial' => ['label' => 'Ejecutivo', 'column' => null, 'type' => 'select', 'multiple' => true, 'operators' => ['equals', 'not_equals', 'is_empty', 'is_not_empty']],

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
        return [
            'H' => 'H',
            'M' => 'M',
            // Compatibilidad con registros historicos previos al formato H/M
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Valores status_color (contactos y empresas) con etiqueta + color del semáforo en UI.
     */
    public static function prospectStatusColorOptions(): array
    {
        $hints = [
            'seguimiento' => 'verde',
            'interesado' => 'rojo',
            'si_le_interesa_nos_llaman_o_no_compro' => 'azul',
            'vendido' => 'amarillo',
            'no_estaba' => 'morado',
        ];
        $out = [];
        foreach (Contact::PROSPECT_STATUS_LABELS as $key => $label) {
            $hint = $hints[$key] ?? '';
            $out[$key] = $hint !== '' ? "{$label} ({$hint})" : $label;
        }

        return $out;
    }

    /** Operadores por campo para contactos (para uso en vistas). */
    public static function contactFieldsWithOptions(): array
    {
        $fields = self::contactFields();
        $fields['genero']['options'] = self::contactGeneroOptions();
        $fields['no_recibir_correos']['options'] = ['1' => 'Sí (no recibir correos)'];
        $fields['status_color']['options'] = self::prospectStatusColorOptions();
        return $fields;
    }

    /** Operadores por defecto cuando no hay campo seleccionado. */
    public static function defaultOperators(): array
    {
        return array_merge(self::textOperators(), self::existenceOperators(), ['has_value', 'no_value']);
    }
}
