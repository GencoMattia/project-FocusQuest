<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            "name"=>'required|min:3|max:50',
            "surname"=>'required|min:3|max:50',
            "email"=>'required|email|min:3|unique:users,email',
            "password"=>'required|min:8|confirmed'
        ];
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            "name.required" => "Il nome è obbligatorio",
            "name.min" => "Il nome deve avere almeno 3 caratteri",
            "name.max" => "Il nome non può superare i 50 caratteri",

            "surname.required" => "Il cognome è obbligatorio",
            "surname.min" => "Il nome deve avere almeno 3 caratteri",
            "surname.max" => "Il nome non può superare i 50 caratteri",

            "email.required" => "La mail è obbligatoria",
            "email.email" => "Inserisci un indirizzo email valido",
            "email.min" => "L'email deve avere almeno 3 caratteri",
            "email.unique" => "Questo indirizzo email è già stato utilizzato",

            'password.required' => 'Il campo password è obbligatorio.',
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'password.confirmed' => 'Le password non corrispondono.',
        ];
    }
}
