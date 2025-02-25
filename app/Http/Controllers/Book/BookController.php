<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use http\Env\Request;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::paginate(20));
    }
    public function show($id){
        $book = Book::findOrFail($id);
        return new BookResource($book);
    }
    public function create(Request $request)
    {
        $data = $request->validate([
           'title' => 'required',
           'author' => 'required',

        ]);
    }
}
