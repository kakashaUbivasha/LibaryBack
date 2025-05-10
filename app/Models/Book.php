<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'publication_date' => 'datetime'
    ];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
    public function users(){
        return $this->belongsToMany(User::class, 'favorites', 'book_id', 'user_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function reservations(){
        return $this->belongsToMany(User::class, 'reservations', 'book_id', 'user_id')
            ->withPivot('reserved_until', 'status')->withTimestamps();
    }
}
