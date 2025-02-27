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
    public function store(BookIdRequest $request){
        $user = auth()->user();
        $data = $request->validated();
        $user->favoriteBooks()->syncWithoutDetaching($data['book_id']);
        return response(['message'=>'Книга добавлена в извбранное'], 201);
    }
    public function destroy(BookIdRequest $request){
        $user = auth()->user();
        $data = $request->validated();
        $user->favoriteBooks()->detach($data['book_id']);
        return response(['message'=>'Книга успешно удалена из избранного'], 201);

    }
}
