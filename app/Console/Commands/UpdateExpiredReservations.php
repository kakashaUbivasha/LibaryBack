<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of expired reservations to "expired"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = DB::table('reservations')
            ->where('status', 'active')
            ->where('reserved_until', '<', Carbon::now())
            ->update(['status' => 'expired']);
        $this->info("Updated $count expired reservations successfully!");
        \Log::info("Expired $count reservations at " . now());
    }
}
