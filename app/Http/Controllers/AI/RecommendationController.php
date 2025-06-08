<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function index(GeminiService $gemini){
        $user = auth()->user();
        $tags = Tag::all()->pluck('name')->toArray();
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
        $prompt .= "\nНа основе этих книг определи предпочтения пользователя и предложи ему 3 тэга из списка тэгов: [\"{$tags['name']}\"], которые ему точно подойдут. Учитывай жанры, атмосферу и стиль произведений.

        Формат ответа:
        1. Название книги — краткое объяснение, почему она подойдёт.";
        Log::info('Отправка запроса к Gemini');
        $response = $gemini->predict($prompt);
        Log::info('Получен ответ от Gemini: ' . $response);
        return response()->json(['tags' => $response]);
    }
}
