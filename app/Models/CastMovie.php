<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CastMovie extends Model
{
    use HasFactory, HasUuids;

    protected $table = "cast_movies";

    protected $fillable = [
        'cast_id',
        'movie_id'
    ];

    protected $guarded = ['id'];

    public function cast()
    {
        return $this->belongsTo(Cast::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
