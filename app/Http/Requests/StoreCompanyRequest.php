<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request de validación para crear empresas
 * 
 * Incluye validación estricta de RFC y detección de duplicados
 */
class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('companies.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_comercial' => [
                'required',
                'string',
                'max:255',
                'unique:companies,nombre_comercial',
            ],
            'rfc' => [
                'required',
                'string',
                'size:12',
                'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/',
                'unique:companies,rfc',
            ],
            'sector' => 'nullable|string|max:500',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'ejecutivo_asignado' => 'nullable|string|max:255',
            'datos_fiscales' => 'nullable|string',
            'status_color' => 'nullable|in:verde,amarillo,rojo',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_comercial.required' => 'El nombre comercial es obligatorio.',
            'nombre_comercial.unique' => 'Ya existe una empresa con este nombre comercial.',
            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.size' => 'El RFC debe tener exactamente 12 caracteres.',
            'rfc.regex' => 'El formato del RFC no es válido.',
            'rfc.unique' => 'Ya existe una empresa con este RFC.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación adicional de RFC en backend (capa dual)
            $rfc = strtoupper($this->input('rfc'));
            
            if (!$this->validarRFC($rfc)) {
                $validator->errors()->add('rfc', 'El RFC no es válido según las reglas fiscales mexicanas.');
            }
        });
    }

    /**
     * Valida el formato del RFC según las reglas fiscales mexicanas
     */
    private function validarRFC(string $rfc): bool
    {
        // Validación básica de formato
        if (!preg_match('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
            return false;
        }

        // Validaciones adicionales pueden agregarse aquí
        return true;
    }
}
