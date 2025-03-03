<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Filters\BookFilter;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookSearchRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;


class BookController extends Controller
{
    public function index(BookFilter $filter, Request $request)
    {
        $perPage = $request->query('perPage', 20);
        $books = $filter->apply(Book::query(), $request->query())->paginate($perPage);
        return BookResource::collection($books);
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
    public function search(BookSearchRequest $request){
        $data = $request->validated()['query'];
        $books = Book::whereFullText(['title', 'description', 'author'], $data);
        return BookResource::collection($books->paginate(20));

    }
    public function top()
    {
        $books = Book::withCount('reservations')->orderBy('reservations_count', 'desc')->limit(10)->get();
        return BookResource::collection($books);
    }
}
