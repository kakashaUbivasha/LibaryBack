<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id){
        $comments = Comment::where('book_id', $id)->with('user')->latest()->get();
        return CommentResource::collection($comments);
    }
    public function store(CommentRequest $request){
        $data = $request->validated();
        $user = auth()->user();
        $comment = $user->comments()->create($data);
        $user->increment('activity_score', 2);
        return CommentResource::make($comment);
    }
    public function update(CommentRequest $request, Comment $comment)
    {
        $user = auth()->user();
        $data = $request->validated();
        $comment->update($data);
        return CommentResource::make($comment);
    }
    public function destroy(Comment $comment){
        $user = auth()->user();

        if($comment->user_id == $user->id || $user->role == 'Admin'){
            $comment->delete();
            return response()->json(['message' => 'Комментарий удалён'], 200);
        }
        return response()->json(['message' => 'У вас нет прав на удаление'], 403);
    }
//    public function like(Comment $comment){
//        $user = auth()->user();
//        $comment->increment('likes');
//        return CommentResource::make($comment);
//    }
//    public function dislike(Comment $comment){
//        $user = auth()->user();
//        $comment->increment('dislikes');
//        return CommentResource::make($comment);
//    }
}
