<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    /**
     * Caracteres prohibidos para evitar inyecciones (SQL, XSS, etc.).
     */
    protected const DANGEROUS_CHARS_REGEX = '/[<>"\'\\;`|&\x00-\x08\x0B\x0C\x0E-\x1F]/';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Eliminar espacios al inicio y al final.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'email' => is_string($this->email) ? trim($this->email) : $this->email,
            'password' => is_string($this->password) ? trim($this->password) : $this->password,
            'password_confirmation' => is_string($this->password_confirmation) ? trim($this->password_confirmation) : $this->password_confirmation,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'not_regex:' . self::DANGEROUS_CHARS_REGEX,
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
                'not_regex:' . self::DANGEROUS_CHARS_REGEX,
            ],
            'password' => [
                'required',
                'confirmed',
                'max:255',
                'not_regex:' . self::DANGEROUS_CHARS_REGEX,
                Rules\Password::defaults(),
            ],
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
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.not_regex' => 'El nombre no puede contener caracteres especiales no permitidos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe una cuenta con este correo electrónico.',
            'email.lowercase' => 'El correo electrónico debe estar en minúsculas.',
            'email.not_regex' => 'El correo no puede contener caracteres especiales no permitidos.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.max' => 'La contraseña no puede exceder 255 caracteres.',
            'password.not_regex' => 'La contraseña no puede contener caracteres especiales no permitidos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ];
    }
}
