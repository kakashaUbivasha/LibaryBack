<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(){
        $user = auth()->user();
        $data = $user->reservationBooks;
        return BookResource::collection($data);
    }
    public function store(Request $request){
        $user = auth()->user();

    }
}
