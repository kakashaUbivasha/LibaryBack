<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends MainController
{
    public function __invoke(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Вы вышли из системы'], 200);
    }
}
