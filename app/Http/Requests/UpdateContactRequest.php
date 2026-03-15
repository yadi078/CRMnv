<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de validación para actualizar contactos
 */
class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('contacts.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contact = $this->route('contact');

        return [
            'company_id' => 'required|exists:companies,id',
            'nombre_completo' => 'required|string|max:255',
            'genero' => 'nullable|string|max:50',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:30',
            'extension' => 'nullable|string|max:10',
            'email' => 'required|email|unique:contacts,email,' . $contact->id . '|max:255',
            'email_activo' => 'sometimes|boolean',
            'fecha_cumpleanos' => 'nullable|date',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'calle_numero' => 'nullable|string|max:500',
            'colonia_cp' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:20',
            'regimen_fiscal' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'status_color' => 'nullable|string|in:seguimiento,interesado,si_le_interesa_nos_llaman_o_no_compro,vendido,no_estaba',
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
            'company_id.required' => 'Debe seleccionar una empresa.',
            'company_id.exists' => 'La empresa seleccionada no existe.',
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.max' => 'El nombre completo no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe un contacto con este correo electrónico.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'extension.max' => 'La extensión no puede tener más de 10 caracteres.',
            'celular.max' => 'El celular no puede tener más de 20 caracteres.',
            'telefono.max' => 'El teléfono no puede tener más de 30 caracteres.',
        ];
    }
}
