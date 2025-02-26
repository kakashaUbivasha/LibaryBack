<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $favorites = $user->favoriteBooks()->get();
        return BookResource::collection($favorites);
//        return response()->json([
//           'favorite' => $user->favoriteBooks()->get()
//        ]);
    }
}
