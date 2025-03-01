<?php

namespace App\Http\Filters;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;

class BookFilter
{
    public const GENRE = 'genre';
    public const AUTHOR = 'author';
    public const TAGS = 'tags';
    protected function getCallbacks(): array
    {
        return[
            self::GENRE => [$this, 'genre'],
            self::AUTHOR => [$this, 'author'],
            self::TAGS => [$this, 'tags'],
        ];
    }
    public function title(Builder $builder, $value)
    {
        $genre = Genre::whereRaw('LOWER(name) = LOWER(?)', [$value])->firstOrFail();
        if($genre){
            $builder->where('genre_id', $genre->id);
        }
    }
    public function author(Builder $builder, $value)
    {
        $builder->where('author', 'like', "%${value}%");
    }
    public function tags(Builder $builder, $value)
    {
        $builder->where('tags', $value['category_id']);
    }
}
