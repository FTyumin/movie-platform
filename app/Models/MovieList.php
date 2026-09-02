<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;

class MovieList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_private',
    ];

    protected $table = 'lists';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function movies() {
        return $this->belongsToMany(Movie::class, 'movie_lists', 'list_id', 'movie_id')
        ->withTimestamps();
    }
    
    public function addMovie(int $movieId, ?int $position = null) {
        // Avoid duplicates
        if ($this->movies()->where('movie_id', $movieId)->exists()) {
            return; 
        }

        if (is_null($position)) {
            $position = $this->movies()->count() + 1;
        }

        $this->movies()->attach($movieId, ['position' => $position]);
    }

    public function removeMovie(int $movieId) {
        $this->movies()->detach($movieId);
    }

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->where('is_private', false);
        }

        return $query->where(function($q) use ($user) {
            $q->where('is_private', false)
              ->orWhere('user_id', $user->id);
        });
    }

    // Check if user can view this list
    public function canView($user)
    {
        if (!$this->is_private) {
            return true;
        }

        return $user && $this->user_id === $user->id;
    }

    protected static function booted() {
        static::created(function ($movieList) {
            Activity::create([
                'user_id' => $movieList->user_id,
                'activityable_type' => MovieList::class,
                'activityable_id' => $movieList->id,
            ]);
        });
    }
}
