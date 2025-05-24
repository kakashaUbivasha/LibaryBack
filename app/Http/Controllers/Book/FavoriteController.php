<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookIdRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $favorites = $user->favoriteBooks()->get();
        return BookResource::collection($favorites);
    }
    public function show($id)
    {
        $user = auth()->user();
        $favorite = $user->favoriteBooks()->findOrFail($id);
        return new BookResource($favorite);
    }
    public function store(BookIdRequest $request){
        $user = auth()->user();
        $data = $request->validated();
        $user->favoriteBooks()->syncWithoutDetaching($data['book_id']);
        return response(['message'=>'Книга добавлена в извбранное'], 201);
    }
    public function destroy($id){
        $user = auth()->user();
        $book = Book::findOrFail($id);
        $user->favoriteBooks()->detach($book->$id);
        return response(['message'=>'Книга успешно удалена из избранного'], 201);

    }
}
