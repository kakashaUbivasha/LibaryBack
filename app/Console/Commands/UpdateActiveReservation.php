<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

class UpdateActiveReservation extends Command
{
    protected $signature = 'reservations:update-active';
    protected $description = 'Обновляет статус бронирований, срок которых истёк';

    public function handle()
    {
        $expired = Reservation::where('status', 'active')
            ->where('reserved_until', '<', now())
            ->get();

        $count = 0;

        foreach ($expired as $reservation) {
            DB::transaction(function () use ($reservation, &$count) {
                $reservation->update(['status' => 'canceled']);

                $book = $reservation->book;
                if ($book) {
                    $book->increment('count');
                }

                $count++;
            });
        }

        $this->info("Обновлено $count просроченных бронирований.");
        Log::info("Команда reservations:update-active: Обновлено $count записей.");

        return Command::SUCCESS;
    }
}
