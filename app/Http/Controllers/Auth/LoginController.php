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
//        $user->tokens()->delete();
        $token = $user->createToken('my-app-token')->plainTextToken;
        return response()->json(['token' => $token,'user'=>$user], 200);
    }
}
