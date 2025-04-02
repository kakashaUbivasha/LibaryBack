<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends MainController
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user|| !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['Неверные данные'],
            ]);
        }
        $token = $user->createToken('my-app-token')->plainTextToken;
        $user->increment('activity_score', 1);
        return response()->json(['token' => $token], 200);
    }
}
