<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Filters\BookFilter;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookSearchRequest;
use App\Http\Requests\BookViewRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\BookResource;
use App\Imports\BooksImport;
use App\Models\Book;
use App\Models\BookView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class BookController extends Controller
{
    public function index(BookFilter $filter, Request $request)
    {
        $perPage = $request->query('perPage', 20);
        $filters = $request->query();

        $cacheKey = 'books_' . md5(json_encode($filters));

        $books = Cache::remember($cacheKey, 3600, function () use ($filter, $filters, $perPage) {
            return $filter->apply(Book::query(), $filters)->paginate($perPage);
        });

        return BookResource::collection($books);
    }
    public function show($id){
        $book = Book::findOrFail($id);
        return new BookResource($book);
    }
    public function addViewBook(BookViewRequest $request)
    {
        Log::info('Пользователь: ');
        $user = auth()->user();
        Log::info('Пользователь: '. auth()->user());
        $data = $request->validated();
        BookView::updateOrCreate(
            [
                'user_id'=>$user->id,
                'book_id'=>$data['book_id']
            ]
        );
    }
    public function store(BookRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        $book = Book::create($data);
        if(isset($data['tags']))
        {
            $book->tags()->attach($data['tags']);
        }
        return new BookResource($book);
    }
    public function update($id, BookRequest $request){
        $data = $request->validated();
        $book = Book::findOrFail($id);
        if($request->hasFile('image')){
            if($book->image){
                Storage::delete($book->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        if(isset($data['tags']))
        {
            $book->tags()->sync($data['tags']);
        }
        $book->update($data);
        return new BookResource($book);
    }
    public function destroy($id){
        $book = Book::findOrFail($id);
        if($book->image){
            Storage::delete($book->image);
        }
        $book->delete();
        $book->tags()->detach();
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

    public function random()
    {
        $book = Book::inRandomOrder()->first();
        return new BookResource($book);
    }

    public function import(ImportRequest $request)
    {
        Excel::import(new BooksImport, $request->file('file'));
        return response()->json(['message' => 'Импорт успешно завершён']);
    }
}
