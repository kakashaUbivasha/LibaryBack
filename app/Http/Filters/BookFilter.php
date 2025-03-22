<?php

namespace App\Http\Filters;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;

class BookFilter extends AbstractFilter
{
    public const GENRE = 'genre';
    public const AUTHOR = 'author';
    public const TAGS = 'tags';
    public const SORT = 'sort';
    protected function getCallbacks(): array
    {
        return[
            self::GENRE => [$this, 'genre'],
            self::AUTHOR => [$this, 'author'],
            self::TAGS => [$this, 'tags'],
            self::SORT => [$this, 'sortByDate'],
        ];
    }
    public function genre(Builder $builder, $value)
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
        $tags = explode(',', $value);
        foreach ($tags as $tag) {
            $builder->whereHas('tags', function (Builder $query) use ($tag) {
                $query->where('tags.name', $tag);
            });
        }
    }
    public function sortByDate(Builder $builder, $value)
    {
        if ($value === 'newest') {
            $builder->orderBy('created_at', 'desc');
        } elseif ($value === 'oldest') {
            $builder->orderBy('created_at', 'asc');
        }
    }
}
