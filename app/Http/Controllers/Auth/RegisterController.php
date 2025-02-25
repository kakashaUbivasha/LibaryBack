<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends MainController
{
    public function __invoke(Request $request){
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
            return response()->json([$e],400);
        }
        return response()->json(['massage'=>'пользователь создан'], 200);
    }
}
