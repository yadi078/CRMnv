<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignContactExecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'contact_id' => ['required', 'exists:contacts,id'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contact_id.required' => 'Indique un contacto: use el filtro Contacto, el botón Asignar en la fila o deje solo un contacto sin ejecutivo en el listado.',
            'user_id.required' => 'Seleccione un responsable en la lista antes de pulsar Añadir.',
        ];
    }
}
