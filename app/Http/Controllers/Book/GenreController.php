<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\GenreResource;
use App\Imports\GenresImport;
use App\Models\Genre;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        return GenreResource::collection($genres);
    }
    public function import(ImportRequest $request)
    {
        Excel::import(new GenresImport, $request->file('file'));
        return response()->json(['message' => 'Импорт успешно завершён']);
    }
}
