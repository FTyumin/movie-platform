<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Person extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'tmdb_id',
        'first_name',
        'last_name',
        'nationality',
        'birth_year',
        'birth_date',
        'profile_path',
        'biography',
    ];

    protected $casts = [
        'birth_year' => 'integer',
        'birth_date' => 'date',
    ];

    public function moviesAsActor()
    {
        return $this->belongsToMany(Movie::class, 'person_movie', 'person_id', 'movie_id')
        ->wherePivot('role', 'actor')
        ->withTimestamps();
    }

    public function moviesAsDirector()
    {
        return $this->belongsToMany(Movie::class, 'person_movie', 'person_id', 'movie_id')
        ->wherePivot('role', 'director');
    }

    // generate url from first_name + last_name
    public function getSlugOptions() : SlugOptions 
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['first_name', 'last_name'])
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
