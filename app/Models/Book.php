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
}
