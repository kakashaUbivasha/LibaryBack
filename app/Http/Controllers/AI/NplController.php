<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\NplRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\TagResource;
use App\Models\Book;
use App\Models\Tag;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class NplController extends Controller
{
    public function index(NplRequest $request, GeminiService $gemini){
        $data = $request->validated();
        $tags = Tag::all()->pluck('name')->filter()->values()->toArray();
        $tagsString = empty($tags) ? null : '["' . implode('", "', $tags) . '"]';
        $prompt = "Ты — помощник библиотеки, который подбирает тэги по пользовательскому запросу.\n";
        $prompt .= "Пользователь написал: \"{$data['text']}\"\n";

        if ($tagsString === null) {
            $prompt .= "Список доступных тэгов пуст.";
            $prompt .= "\nОтветь строго валидным JSON-массивом []. Никаких пояснений, текста до или после массива не добавляй.";
        } else {
            $prompt .= "Список доступных тэгов: {$tagsString}.";
            $prompt .= "\nВыбери подходящие тэги из списка и ответь строго валидным JSON-массивом с названиями выбранных тэгов (например, [\"фантастика\",\"детектив\"]). Никаких пояснений, текста до или после массива не добавляй.";
        }
        Log::info('Отправка запроса к Gemini');
        $response = $gemini->predict($prompt);
        Log::info('Получен ответ от Gemini: ' . $response);

        $tagNames = json_decode($response, true);
        if (!is_array($tagNames)) {
            $tagNames = [];
        }

        $tagsCollection = Tag::whereIn('name', $tagNames)->get();
        $tagIds = $tagsCollection->pluck('id');

        $books = collect();
        if ($tagIds->isNotEmpty()) {
            $books = Book::query()
                ->whereHas(
                    'tags',
                    fn ($query) => $query->whereIn('tags.id', $tagIds),
                    '=',
                    $tagIds->count()
                )
                ->limit(10)
                ->get();
        }

        return response()->json([
            'tags' => TagResource::collection($tagsCollection)->resolve(),
            'books' => BookResource::collection($books)->resolve(),
        ]);
    }

}
