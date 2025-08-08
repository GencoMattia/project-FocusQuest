<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewMomentRequest extends FormRequest
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
            // task_id comes from the route parameter
            "name"=>'required|string|min:3|max:150',
            'message'=>'nullable|string',
            'emotion_id'=>'required|integer|exists:emotions,id',
            'moments_type_id'=>'required|integer|exists:moments_types,id'
        ];
    }

    /**
     * Get the validation messages for the defined rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome è obbligatorio.',
            'name.string' => 'Il nome deve essere una stringa.',
            'name.min' => 'Il nome deve contenere almeno :min caratteri.',
            'name.max' => 'Il nome non può superare :max caratteri.',

            'message.string' => 'Il messaggio deve essere una stringa.',

            'emotion_id.required' => 'L\'emozione è obbligatoria.',
            'emotion_id.integer' => 'L\'ID dell\'emozione deve essere un numero intero.',
            'emotion_id.exists' => 'L\'ID dell\'emozione fornito non esiste nel database.',

            'moments_type_id.required' => 'Il tipo di momento è obbligatorio.',
            'moments_type_id.integer' => 'L\'ID del tipo di momento deve essere un numero intero.',
            'moments_type_id.exists' => 'Il tipo di momento fornito non esiste nel database.',
        ];
    }
}
