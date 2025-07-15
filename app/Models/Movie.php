<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'poster',
        'release_year',
        'genre',
        'watched',
        'score',
        'review',
    ];

    protected $casts = [
        'release_year' => 'integer',
        'watched' => 'boolean',
        'score' => 'integer',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
