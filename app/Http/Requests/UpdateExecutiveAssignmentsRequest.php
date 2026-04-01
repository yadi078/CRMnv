<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExecutiveAssignmentsRequest extends FormRequest
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
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['integer', 'exists:companies,id'],
            'contact_ids' => ['nullable', 'array'],
            'contact_ids.*' => ['integer', 'exists:contacts,id'],
        ];
    }
}
