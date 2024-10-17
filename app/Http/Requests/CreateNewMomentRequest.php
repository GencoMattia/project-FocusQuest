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
            "task_id"=>'required|integer|exists:tasks,id',
            "name"=>'required|string|min:3',
            'message'=>'string',
            'emotion_id'=>'required|integer|exists:emotions,id',
            'moments_type_id'=>'required|integer|exists:moments_types,id'
        ];
    }
}
