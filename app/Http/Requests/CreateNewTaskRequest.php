<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewTaskRequest extends FormRequest
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
            "name" => 'required|string|min:3|max:150',
            "description" => 'nullable|string',
            "estimated_time" => 'required|integer|min:1',
            "deadline" => 'date|after_or_equal:today',
            "category_id" => 'required|integer|exists:categories,id',
            "priority_id" => 'required|integer|exists:priorities,id',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome della task è obbligatorio.',
            'name.string' => 'Il nome della task deve essere una stringa valida.',
            'name.min' => 'Il nome della task deve contenere almeno 3 caratteri.',
            'name.max' => 'Il nome della task non può superare i 150 caratteri.',

            'description.string' => 'La descrizione deve essere una stringa valida.',

            'estimated_time.required' => 'Il tempo stimato è obbligatorio.',
            'estimated_time.integer' => 'Il tempo stimato deve essere un numero intero.',
            'estimated_time.min' => 'Il tempo stimato deve essere maggiore di 0.',

            'deadline.date' => 'La deadline deve essere una data valida.',
            'deadline.after_or_equal' => 'La deadline deve essere una data odierna o successiva.',

            'category_id.required' => 'La categoria della task è obbligatoria.',
            'category_id.integer' => 'La categoria deve essere un numero intero valido.',
            'category_id.exists' => 'La categoria selezionata non esiste.',

            'priority_id.required' => 'La priorità della task è obbligatoria.',
            'priority_id.integer' => 'La priorità deve essere un numero intero valido.',
            'priority_id.exists' => 'La priorità selezionata non esiste.',
        ];
    }
}
