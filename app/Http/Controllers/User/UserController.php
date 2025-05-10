<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Не удалось найти пользователя'], 404);
        }
    }
}
