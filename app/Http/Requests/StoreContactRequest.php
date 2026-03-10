<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de validaci?n para crear contactos
 */
class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contacts.create');
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'nombre_completo' => 'required|string|min:4|max:255',
            'genero' => 'nullable|string|max:50',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|digits_between:8,15',
            'telefono' => 'nullable|digits_between:7,15',
            'extension' => 'nullable|digits_between:1,6',
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:contacts,email',
                'regex:/^[^@]+@[^@]+\.com$/i',
            ],
            'municipio' => 'nullable|string|max:70',
            'estado' => 'nullable|string|max:70|regex:/^[^0-9]*$/',
            'notas' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Debe seleccionar una empresa.',
            'company_id.exists' => 'La empresa seleccionada no existe.',
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.min' => 'El nombre completo debe tener al menos 4 caracteres.',
            'nombre_completo.max' => 'El nombre completo no puede tener m?s de 255 caracteres.',
            'email.required' => 'El correo electr?nico es obligatorio.',
            'email.email' => 'El correo electr?nico debe tener un formato v?lido.',
            'email.unique' => 'Ya existe un contacto con este correo electr?nico.',
            'email.max' => 'El correo electr?nico no puede tener m?s de 255 caracteres.',
            'email.regex' => 'El correo electr?nico debe terminar en .com.',
            'celular.digits_between' => 'El celular debe contener solo n?meros y tener entre 8 y 15 d?gitos.',
            'telefono.digits_between' => 'El tel?fono debe contener solo n?meros y tener entre 7 y 15 d?gitos.',
            'extension.digits_between' => 'La extensi?n debe contener solo n?meros y tener entre 1 y 6 d?gitos.',
            'municipio.max' => 'El municipio no puede tener m?s de 70 caracteres.',
            'estado.max' => 'El estado no puede tener m?s de 70 caracteres.',
            'estado.regex' => 'El estado no puede contener n?meros.',
        ];
    }
}