<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return new UserResource($user);
    }
    public function topUsers()
    {
        $topUsers = User::orderByDesc('activity_score')->limit(10)->get();
        return UserResource::collection($topUsers);
    }
}
