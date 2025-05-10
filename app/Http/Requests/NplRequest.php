<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NplRequest extends FormRequest
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
            'text' => 'required|string|min:10|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'text.required' => 'Пожалуйста, введите поисковый запрос.',
            'text.string' => 'Запрос должен быть текстом.',
            'query.min' => 'Запрос должен содержать хотя бы 10 символов.',
            'text.max' => 'Запрос слишком длинный.',
        ];
    }
}
