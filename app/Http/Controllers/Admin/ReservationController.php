<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservationsQuery = Reservation::with(['book', 'user'])
            ->orderByDesc('updated_at');

        $search = trim((string) $request->input('user'));

        if ($search !== '') {
            $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);

            $reservationsQuery->whereHas('user', function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where('name', 'like', "%{$term}%");
                }
            });

            $reservations = $reservationsQuery->get();
        } else {
            $reservations = $reservationsQuery->paginate(20);
        }
        return ReservationResource::collection($reservations);
    }
}
