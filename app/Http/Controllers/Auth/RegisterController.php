<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegisterController extends MainController
{
    public function __invoke(RegisterRequest $request){
        try{
            $data = $request->validated();
            User::create($data);

            return response()->json(['message' => 'пользователь создан'], 201);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
        catch(Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
