<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferExecutivePortfolioRequest extends FormRequest
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
            'from_user_id' => ['required', 'integer', 'exists:users,id', 'different:to_user_id'],
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from_user_id.different' => 'El ejecutivo de origen y el de destino deben ser distintos.',
        ];
    }
}
