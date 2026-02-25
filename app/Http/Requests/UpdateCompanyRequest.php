<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request de validación para actualizar empresas
 * 
 * Incluye validación de RFC y detección de duplicados excluyendo el registro actual
 */
class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('companies.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyId = $this->route('company')->id ?? null;

        return [
            'nombre_comercial' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'nombre_comercial')->ignore($companyId),
            ],
            'rfc' => [
                'nullable',
                'string',
                'max:13',
                Rule::when($this->filled('rfc'), [
                    'size:12',
                    'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/',
                    Rule::unique('companies', 'rfc')->ignore($companyId),
                ]),
            ],
            'sector' => 'required|string|max:500',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'ejecutivo_asignado' => 'nullable|string|max:255',
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
        if (!preg_match('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
            return false;
        }

        return true;
    }
}
