<?php

namespace App\Http\Resources;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
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
            'image'=>$this->getImage(),
            'genre' => $this->genre ? $this->genre->name : null,
            'count'=>$this->count,
            'language'=>$this->language
        ];
    }
    private function getImage(): string{
        if($this->image){
            return Storage::url("images/{$this->image}");
        }
        return asset('storage/images/default.png');
    }
}
