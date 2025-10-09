<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'content'=>$this->content,
          'book_title' => $this->book ? $this->book->title : 'Книга не найдена',
          'book_id'=> $this->book ? $this->book->id : 1,
          'likes'=>$this->likes,
          'dislikes'=>$this->dislikes,
          'user_id'=>$this->user_id,
            'id'=>$this->id,
          'user_name'=>$this->user->name,
          'created_at'=>$this->created_at
        ];
    }
}
