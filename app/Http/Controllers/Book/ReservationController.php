<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookIdRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReservationResource;
use App\Models\Book;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(){
        $user = auth()->user();
        $data = $user->reservationBooks;
        return BookResource::collection($data);
    }
    public function store(BookIdRequest $request, ReservationService $reservation)
    {
        $user = auth()->user();
        $data = $request->validated();
        try {
            $reservation->store($user, $data);
            $user->increment('activity_score', 5);
            return response(['message' => 'Книга успешно забронирована'], 201);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
    public function update(BookIdRequest $request, ReservationService $reservation){
        $user = auth()->user();
        $data = $request->validated();
        try {
            $reservation->update($user, $data);
            return response(['message' => 'Книга успешно сдана'], 200);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
    public function history(ReservationService $reservation){
        $user = auth()->user();
        try {
             $data = $reservation->history($user);
             return ReservationResource::collection($data);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
