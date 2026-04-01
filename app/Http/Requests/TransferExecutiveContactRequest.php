<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TransferExecutiveContactRequest extends FormRequest
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
            'contact_id' => ['required', 'integer', 'exists:contacts,id'],
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $exec = $this->route('user');
            if (! $exec instanceof User) {
                return;
            }
            if ((int) $this->input('to_user_id') === (int) $exec->id) {
                $validator->errors()->add('to_user_id', 'Seleccione un ejecutivo distinto al de esta pantalla.');
            }
        });
    }
}
