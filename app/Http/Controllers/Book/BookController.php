<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
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
    public function store(BookRequest $request)
    {
        $data = $request->validated();
        $book = Book::create($data);
        return new BookResource($book);
    }
    public function update($id, BookRequest $request){
        $data = $request->validated();
        $book = Book::findOrFail($id);
        $book->update($data);
        return new BookResource($book);
    }
    public function destroy($id){
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json(['message'=>'Книга удалена'], 204);
    }
}
