<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\StoreGenreRequest;
use App\Http\Requests\Genre\UpdateGenreRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\GenreResource;
use App\Imports\GenresImport;
use App\Models\Genre;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();

        return GenreResource::collection($genres);
    }

    public function show(Genre $genre)
    {
        return new GenreResource($genre);
    }

    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->validated());

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return new GenreResource($genre);
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->noContent();
    }

    public function import(ImportRequest $request)
    {
        Excel::import(new GenresImport, $request->file('file'));

        return response()->json(['message' => 'Импорт успешно завершён']);
    }
}
