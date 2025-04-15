<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function update($user, $data)
    {
        $book = Book::findOrFail($data['book_id']);

        $reservation = $user->reservations()
            ->where('book_id', $book->id)
            ->orderByDesc('created_at')
            ->first();

        if (!$reservation) {
            throw new \Exception('Вы не бронировали эту книгу', 400);
        }

        if ($reservation->status === 'canceled') {
            throw new \Exception('Эта книга уже была сдана', 400);
        }

        DB::transaction(function () use ($reservation, $book) {
            $reservation->update(['status' => 'canceled']);
            $book->increment('count');
        });
    }
    public function store($user, $data)
    {
        $book = Book::findOrFail($data['book_id']);

        if ($book->count <= 0) {
            throw new \Exception('Все книги забронированы', 404);
        }

        // Ищем бронирование этой книги у пользователя
        $reservation = Reservation::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->latest() // вдруг было несколько бронирований раньше
            ->first();

        DB::transaction(function () use ($user, $book, $reservation) {
            if ($reservation) {
                if (in_array($reservation->status, ['active', 'expired'])) {
                    throw new \Exception('Вы уже забронировали эту книгу', 400);
                } elseif ($reservation->status === 'canceled') {
                    Reservation::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'status' => 'active',
                        'reserved_until' => now()->addDays(7),
                    ]);
                }
            } else {
                Reservation::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'status' => 'active',
                    'reserved_until' => now()->addDays(7),
                ]);
            }

            $book->decrement('count');
        });
    }

    public function history($user)
    {
        $data = Reservation::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($data->isEmpty()) {
            throw new \Exception('У вас нет забронированных книг!', 400);
        }

        return $data;
    }

}
