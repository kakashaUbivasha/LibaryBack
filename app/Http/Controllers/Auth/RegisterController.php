<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends MainController
{
    public function __invoke(RegisterRequest $request){
        try{

            $data = $request->validated();
            User::create($data);
            return response()->json(['massage'=>'пользователь создан'], 200);
        }
        catch(ValidationException $e){
            return response()->json([$e],400);
        }
    }
}
