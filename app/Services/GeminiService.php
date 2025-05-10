<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key';
    public function predict(string $prompt): string
    {
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::withoutVerifying()->timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' .$apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return $response->json('candidates.0.content.parts.0.text') ?? 'Нет ответа от модели';
        }

        return 'Ошибка: ' . $response->body();
    }
}
