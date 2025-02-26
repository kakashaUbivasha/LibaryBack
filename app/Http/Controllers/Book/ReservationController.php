<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(){
        $user = auth()->user();
        $data = $user->reservationBooks;
        return BookResource::collection($data);
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
        ]);
        $book = Book::findOrFail($data['book_id']);
        if ($book->count <= 0) {
            return response(['message' => 'Все книги забронированы'], 404);
        }
        $reservation = $user->reservationBooks()->where('book_id', $book->id)->first();
        if ($reservation) {
            if ($reservation->pivot->status === 'active' || $reservation->pivot->status === 'expired') {
                return response(['message' => 'Вы уже забронировали эту книгу'], 400);
            } elseif ($reservation->pivot->status === 'canceled') {
                DB::transaction(function () use ($user, $book, $reservation) {
                    $user->reservationBooks()->updateExistingPivot($book->id, [
                        'status' => 'active',
                        'reserved_until' => now()->addDays(7),
                    ]);
                    $book->decrement('count');
                });
                return response(['message' => 'Книга успешно забронирована'], 201);
            }
        }
        DB::transaction(function () use ($user, $book) {
            $user->reservationBooks()->attach($book->id, [
                'status' => 'active',
                'reserved_until' => now()->addDays(7),
            ]);
            $book->decrement('count');
        });

        return response(['message' => 'Книга успешно забронирована'], 201);
    }
    public function update(Request $request){
        $user = auth()->user();
        $data = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
        ]);
        $book = Book::findOrFail($data['book_id']);
        $reservation = $user->reservationBooks()->where('book_id', $book->id)->first();
        if(!$reservation){
            return response(['message' => 'Вы не забронировали эту книгу'], 400);
        }
        if($reservation->pivot->status === 'canceled'){
            return response(['message' => 'Эта книга уже была сдана'], 400);
        }
        DB::transaction(function () use ($user, $book) {
            $user->reservationBooks()->updateExistingPivot($book->id, ['status' => 'canceled']);
            $book->increment('count');
        });
        return response(['message' => 'Книга успешно сдана'], 201);
    }
}
