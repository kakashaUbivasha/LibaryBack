<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class ReservationService
{
        public function update($user, $data)
        {
            $book = Book::findOrFail($data['book_id']);
            $reservation = $user->reservationBooks()->where('book_id', $book->id)->first();
            if(!$reservation){
                throw new \Exception('Вы не забронировали эту книгу', 400);
            }
            if($reservation->pivot->status === 'canceled'){
                throw new \Exception('Эта книга уже была сдана', 400);
            }
            DB::transaction(function () use ($user, $book) {
                $user->reservationBooks()->updateExistingPivot($book->id, ['status' => 'canceled']);
                $book->increment('count');
            });
        }
        public function store($user, $data){
            $book = Book::findOrFail($data['book_id']);

            if ($book->count <= 0) {
                throw new \Exception('Все книги забронированы', 404);
            }

            $reservation = $user->reservationBooks()->where('book_id', $book->id)->first();

            DB::transaction(function () use ($user, $book, $reservation) {
                if ($reservation) {
                    if (in_array($reservation->pivot->status, ['active', 'expired'])) {
                        throw new \Exception('Вы уже забронировали эту книгу', 400);
                    } elseif ($reservation->pivot->status === 'canceled') {
                        $user->reservationBooks()->updateExistingPivot($book->id, [
                            'status' => 'active',
                            'reserved_until' => now()->addDays(7),
                        ]);
                    }
                } else {
                    $user->reservationBooks()->attach($book->id, [
                        'status' => 'active',
                        'reserved_until' => now()->addDays(7),
                    ]);
                }

                $book->decrement('count');
            });
        }
}
