<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyImportedExecutiveLabelRequest extends FormRequest
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
            'label' => ['required', 'string', 'max:255'],
        ];
    }
}
