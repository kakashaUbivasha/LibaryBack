<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'publication_date' => 'required|date',
            'isbn' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_id' => 'required|integer|exists:genres,id',
        ];
    }
    public function messages(): array{
        return [
            'title.required' => 'Название книги обязательно.',
            'title.max' => 'Название книги не должно превышать 255 символов.',
            'author.max' => 'Имя автора не должно превышать 255 символов.',
            'publication_date.required' => 'Дата публикации обязательна.',
            'publication_date.date' => 'Дата публикации должна быть корректной датой.',
            'isbn.numeric' => 'ISBN должен содержать только цифры.',
            'isbn.digits' => 'ISBN должен состоять из 13 цифр.',
            'image.url' => 'Ссылка на изображение должна быть корректным URL.',
            'genre_id.required' => 'Жанр обязателен.',
            'genre_id.integer' => 'Жанр должен быть числом.',
            'genre_id.exists' => 'Выбранный жанр не существует.'
        ];
    }
}
