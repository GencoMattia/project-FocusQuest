<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => "required|email",
            "password" => "required|string|min:8"
        ];
    }

    /**
     * Get custom messages for validators errors
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "emai.required" => "L'indirizzo email è obbligatorio",
            "email.email" => "Inserisci un indirizzo email valido",
            "password.required" => "La password è obbligatoria",
            "password.string" => "La password deve essere una stringa",
            "password.min" => "La password deve essere lunga almeno 8 caratteri",
        ];
    }
}
