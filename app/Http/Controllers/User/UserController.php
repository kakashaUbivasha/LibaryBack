<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return new UserResource($user);
    }
    public function topUsers()
    {
        $topUsers = User::orderByDesc('activity_score')->limit(20)->get();
        return UserResource::collection($topUsers);
    }
    public function guest($id)
    {
        try {
            $user = User::findOrFail($id);
            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Не удалось найти пользователя'], 404);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Произошла ошибка'], 500);
        }
    }
}
