<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExpiredReservations extends Command
{
    protected $signature = 'reservations:update-expired';
    protected $description = 'Обновляет статус бронирований, срок которых истёк';

    public function handle()
    {
        $expired = Reservation::where('status', 'active')
            ->where('reserved_until', '<', now())
            ->get();

        $count = 0;

        foreach ($expired as $reservation) {
            $reservation->update(['status' => 'expired']);
            $count++;
        }

        $this->info("Обновлено $count просроченных бронирований.");

        Log::info("Команда reservations:update-expired: Обновлено $count записей.");

        return 0;
    }
}
