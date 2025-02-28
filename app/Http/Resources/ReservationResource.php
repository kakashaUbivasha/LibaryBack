<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'author'=>$this->author,
            'description'=>$this->description,
            'publication_date'=>$this->publication_date->format('Y-m-d H:i:s'),
            'isbn'=>$this->isbn,
            'image'=>$this->image,
            'genre' => $this->genre ? $this->genre->name : null,
            'count'=>$this->count,
            'reserved_until'=>$this->pivot->reserved_until,
            'status'=>$this->pivot->status,
            'updated_at'=>$this->pivot->updated_at
        ];
    }
}
