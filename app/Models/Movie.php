<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'movies';

    protected $fillable = [
        'title',
        'summary',
        'poster',
        'genre_id',
        'year',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function casts()
    {
        return $this->belongsToMany(Cast::class, 'cast_movies')->withPivot('movie_id', 'cast_id');
    }
}
