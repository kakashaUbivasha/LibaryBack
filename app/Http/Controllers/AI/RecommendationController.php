<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Tag;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function index(GeminiService $gemini){
        $user = auth()->user();
        $tags = Tag::all()->pluck('name')->filter()->values()->toArray();
        $tagsString = empty($tags) ? null : '["' . implode('", "', $tags) . '"]';
        $views_books = $user->views()->with('book')->orderBy('updated_at', 'desc')->take(10)->get();
        $reservations_books = $user->reservations()->with('book')->orderBy('updated_at', 'desc')->take(10)->get();
        if($views_books->count() < 10 && $reservations_books->count() < 5)
        {
            return response()->json(['message'=>'Не достаточно данных. Просмотрите или забронируйте больше книг'], 422);
        }
        $prompt = "Ты — рекомендательная система библиотеки. Пользователь ранее бронировал и просматривал следующие книги:\n\n";
        $prompt .= "Забронированные:\n";
        foreach ($reservations_books as $book) {
            $prompt .= "1. Название: \"{$book->book->title}\", Описание: \"{$book->book->description}\"\n";
        }
        $prompt .= "\nПросмотренные:\n";
        foreach ($views_books as $book) {
            $prompt .= "1. Название: \"{$book->book->title}\", Описание: \"{$book->book->description}\"\n";
        }
        if ($tagsString === null) {
            $prompt .= "\nНа основе этих книг определи предпочтения пользователя и объясни, что список доступных тэгов пуст, поэтому ты не можешь предложить конкретные тэги. Предложи пользователю обратиться к библиотекарю или уточнить интересующие направления.

        Формат ответа:
        1. Рекомендация — краткое объяснение.";
        } else {
            $prompt .= "\nНа основе этих книг определи предпочтения пользователя и предложи ему 3 тэга из списка тэгов: {$tagsString}, которые ему точно подойдут. Учитывай жанры, атмосферу и стиль произведений.

        Формат ответа:
        1. Название книги — краткое объяснение, почему она подойдёт.";
        }
        Log::info('Отправка запроса к Gemini');
        $response = $gemini->predict($prompt);
        Log::info('Получен ответ от Gemini: ' . $response);

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedResponse)) {
            Log::error('Ошибка парсинга ответа Gemini', [
                'response' => $response,
                'error' => json_last_error_msg(),
            ]);

            return response()->json([
                'message' => 'Некорректный ответ от сервиса рекомендаций.',
            ], 502);
        }

        $tagsFromAi = array_values(array_filter($decodedResponse, function ($value) {
            return is_string($value) && $value !== '';
        }));

        if (empty($tagsFromAi) || count($tagsFromAi) !== count($decodedResponse)) {
            Log::error('Ответ Gemini не содержит валидный список тэгов', [
                'response' => $decodedResponse,
            ]);

            return response()->json([
                'message' => 'Сервис рекомендаций вернул неверный формат данных.',
            ], 502);
        }

        $uniqueTags = array_values(array_unique($tagsFromAi));

        try {
            $books = Book::with('tags')
                ->whereHas('tags', function ($query) use ($uniqueTags) {
                    $query->whereIn('name', $uniqueTags);
                }, '=', count($uniqueTags))
                ->get()
                ->map(function (Book $book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'author' => $book->author,
                        'tags' => $book->tags->pluck('name')->all(),
                    ];
                })
                ->values();
        } catch (\Throwable $exception) {
            Log::error('Ошибка при поиске книг по тэгам', [
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'Не удалось получить рекомендации по тэгам.',
            ], 500);
        }

        return response()->json([
            'tags' => $uniqueTags,
            'books' => $books,
        ]);
    }
}
