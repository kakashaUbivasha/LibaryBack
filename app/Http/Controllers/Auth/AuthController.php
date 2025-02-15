<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);
            $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make(request('password')),
            ]);
        }
        catch(ValidationException $e){
            return response()->json(['massage'=>'Пользователь уже зарегестрирован'],400);
        }
        return response()->json(['massage'=>'пользователь создан'], 200);
    }
    public function login(Request $request){
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
        DB::table('personal_access_tokens')->where('token', hash('sha256', explode('|', $token)[1]))
        ->update(['expires_at' => \Carbon\Carbon::now()->addDays(10)]);
        return response()->json(['token' => $token,'user'=>$user], 200);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Вы вышли из системы'], 200);
    }
}
