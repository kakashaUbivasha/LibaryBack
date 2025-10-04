<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function canceledReserv($authUser, $data)
    {
        $book = Book::findOrFail($data['book_id']);

        $isAdmin = $authUser->role === 'Admin';
        $targetUserId = $isAdmin && !empty($data['user_id'])
            ? $data['user_id']
            : $authUser->id;

        // Ищем бронь
        $reservation = Reservation::where('book_id', $book->id)
            ->where('user_id', $targetUserId)
            ->orderByDesc('created_at')
            ->first();

        if (!$reservation) {
            throw new \Exception('Бронь не найдена', 400);
        }

        if ($reservation->status === 'passed') {
            throw new \Exception('Эта книга уже была выдана', 400);
        }

        if ($reservation->status === 'canceled') {
            throw new \Exception('Эта книга уже была отменена', 400);
        }

        DB::transaction(function () use ($reservation, $book) {
            $reservation->update(['status' => 'canceled']);
            $book->increment('count');
        });
    }
    public function issuance($user, $data)
    {
        $book = Book::findOrFail($data['book_id']);
        $reservation = Reservation::where('book_id', $book->id)
            ->where('user_id', $data['user_id'])
            ->orderByDesc('created_at')
            ->first();

        if (!$reservation) {
            throw new \Exception('Бронь не найдена', 404);
        }

        if ($reservation->status === 'passed') {
            throw new \Exception('Эта книга уже была выдана', 400);
        }
        if ($reservation->status === 'canceled') {
            throw new \Exception('Эта книга уже была отменена', 400);
        }
            $reservation->update(['status' => 'passed']);
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
                if (in_array($reservation->status, ['active'])) {
                    throw new \Exception('Вы уже забронировали эту книгу', 400);
                }
                elseif ($reservation->status === 'expired'){
                    throw new \Exception('Нужно вернуть книгу!', 400);
                }
                elseif ($reservation->status === 'canceled') {
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
    public function returnedBook($data)
    {
        $book = Book::findOrFail($data['book_id']);
        $reservation = Reservation::where('book_id', $book->id)
            ->where('user_id', $data['user_id'])
            ->orderByDesc('created_at')
            ->first();

        if (!$reservation) {
            throw new \Exception('Бронь не найдена', 404);
        }
        if ($reservation->status !== 'passed') {
            throw new \Exception('Эта книга не выдана данному пользователю', 400);
        }
        $reservation->update(['status' => 'returned', 'returned_date'=> now()]);
        $book->increment('count');
    }

}
