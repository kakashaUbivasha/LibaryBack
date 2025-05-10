<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookSearchRequest extends FormRequest
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
           'query' => 'required|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'query.required' => 'Введите строку поиска.',
            'query.string' => 'Поисковый запрос должен быть строкой.',
            'query.max' => 'Запрос не может быть длиннее 255 символов.',
        ];
    }
}
