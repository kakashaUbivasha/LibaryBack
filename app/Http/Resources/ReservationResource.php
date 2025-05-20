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
            'reservation_time' => $this->created_at,
            'reserved_until'=>$this->reserved_until,
            'returned_date'=>$this->returned_date,
            'book_title' => $this->book->title,
            'book_id' => $this->book->id,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
