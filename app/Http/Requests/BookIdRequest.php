<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookIdRequest extends FormRequest
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
            'book_id'=>'required|integer|exists:books,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
    public function messages(): array
    {
        return [
            'book_id.required' => 'key book_id обезателен',
            'book_id.integer' => 'El campo :attribute debe ser un entero.',
            'book_id.exists' => 'El campo :attribute no existe.',
        ];
    }
}
