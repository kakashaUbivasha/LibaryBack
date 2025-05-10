<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\NplRequest;
use App\Models\Tag;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class NplController extends Controller
{
    public function index(NplRequest $request, GeminiService $gemini){
        $data = $request->validated();
        $tags = Tag::all()->pluck('name')->toArray();
        $prompt = "
Пользователь написал: \"{$data['text']}\"
Список тэгов: [Фэнтези, научная фантастика, детектив, триллер, романтика, исторический роман, приключения, ужасы, биография, нон-фикшн, поэзия, драма, комедия, философия, психология, саморазвитие, бизнес, научпоп, классика, современная литература, молодёжная литература, детская литература, мистика, сатира, эпос]
Выбери подходящие тэги из списка. Ответи только тэгами через запятую:
";
        Log::info('Отправка запроса к Gemini');
        $response = $gemini->predict($prompt);
        Log::info('Получен ответ от Gemini: ' . $response);
        return response()->json(['tags' => $response]);
    }

}
