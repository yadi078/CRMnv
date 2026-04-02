<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'from_user_id' => ['required'],
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $fromRaw = $this->input('from_user_id');
            $toId = (int) $this->input('to_user_id');

            $to = User::find($toId);
            if ($to === null) {
                return;
            }
            if ($to->esAdmin() || ! $to->is_active || $to->approval_status !== 'aprobado') {
                $validator->errors()->add(
                    'to_user_id',
                    'El ejecutivo destino debe estar activo, aprobado y no ser administrador.'
                );
            }

            $fromStr = is_string($fromRaw) ? $fromRaw : (string) $fromRaw;
            if (str_starts_with($fromStr, 'E:')) {
                $decoded = base64_decode(substr($fromStr, 2), true);
                if ($decoded === false || trim((string) $decoded) === '') {
                    $validator->errors()->add('from_user_id', 'Seleccione un origen válido (ficha).');
                }

                return;
            }

            if (! ctype_digit($fromStr)) {
                $validator->errors()->add('from_user_id', 'Seleccione un ejecutivo de origen válido.');

                return;
            }

            $fromId = (int) $fromStr;
            $from = User::find($fromId);
            if ($from === null || $from->esAdmin()) {
                $validator->errors()->add('from_user_id', 'El origen debe ser un ejecutivo de cartera.');

                return;
            }

            if ($fromId === $toId) {
                $validator->errors()->add('from_user_id', 'El ejecutivo de origen y el de destino deben ser distintos.');
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [];
    }
}
