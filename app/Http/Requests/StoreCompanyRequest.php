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
    use Concerns\NormalizesCompanySector;

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
                'nullable',
                'string',
                'min:12',
                'max:13',
                Rule::when($this->filled('rfc'), [
                    'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/',
                    'unique:companies,rfc',
                ]),
            ],
            'sector' => 'required|string|max:2000',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
            'ejecutivo_asignado' => 'nullable|string|max:255',
            'assigned_user_id' => Rule::when($this->user()->esAdmin(), ['nullable', 'integer', 'exists:users,id']),
            'datos_fiscales' => 'nullable|string',
            'status_color' => 'nullable|in:seguimiento,interesado,si_le_interesa_nos_llaman_o_no_compro,vendido,no_estaba',
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
            'sector.required' => 'El sector o giro es obligatorio.',
            'sector.max' => 'El sector o giro no puede superar :max caracteres.',
            'rfc.min' => 'El RFC debe tener entre 12 y 13 caracteres.',
            'rfc.max' => 'El RFC debe tener entre 12 y 13 caracteres.',
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
            // Validación adicional de RFC en backend (solo si se proporciona)
            $rfc = $this->filled('rfc') ? strtoupper($this->input('rfc')) : '';
            
            if ($rfc !== '' && !$this->validarRFC($rfc)) {
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
