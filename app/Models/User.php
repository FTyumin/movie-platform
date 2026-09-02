<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Maize\Markable\Models\Favorite;

class User extends Authenticatable 
{
    use HasFactory, Notifiable;
    protected static $markableTable = 'markables';

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static $marks = [
        Seen::class,
        WantToWatch::class,
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function ratedMovies()
    {
        return $this->belongsToMany(Movie::class, 'reviews')
            ->withPivot('rating', 'created_at');
    }

    public function wantToWatch()
    {
        return $this->hasMany(WantToWatch::class);
    }

    public function seenMovies() {
        return $this->hasMany(Seen::class);
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }

    public function lists()
    {
        return $this->hasMany(MovieList::class);
    }

    public function favoriteGenres() {
        return $this->belongsToMany(Genre::class, 'user_favorite_genres')
            ->withTimestamps();
    }

    public function favoritePeople() {
        return $this->belongsToMany(Person::class, 'user_favorite_people', 
            'user_id', 'person_id')
            ->withTimestamps();
    }

    public function getRedirectRoute()
    {
        if (! $this->quiz_completed) {
            return route('quiz.show'); 
        }
        return view('quiz.show');
    }
  
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function followers()
    {
        return $this->hasMany(UserRelationship::class, 'followee_id');
    }

    public function followees()
    {
        return $this->hasMany(UserRelationship::class, 'follower_id');
    }

    public function likedReviews() {
        return $this->belongsToMany(Review::class, 'review_likes');
    }
}
