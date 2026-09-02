<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Movie;
use App\Models\Suggestion;
use App\Models\User;
use App\Notifications\SuggestionAccepted;
use App\Jobs\ImportMoviesJob;
use App\Notifications\SuggestionRejected;

class AdminController extends Controller
{
  
    public function dashboard() {

        // select top reviews, lists
        $userWithMostFollowers = User::withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->first();

        $topReview = Review::withCount('likedBy')
            ->orderBy('liked_by_count', 'desc')
            ->first();

        // movies with most favorites, most watched
        $mostFavorites = Movie::withCount('favoriters')
            ->orderBy('favoriters_count', 'desc')
            ->take(5)
            ->get();

        $mostWatched = Movie::withCount('watchers')
            ->orderBy('watchers_count', 'desc')
            ->take(5)
            ->get();

        $suggestions = Suggestion::whereNull('accepted')->get();

        return view('admin', compact('suggestions', 'userWithMostFollowers', 'topReview', 
            'mostFavorites', 'mostWatched'));
    }

    public function approveSuggestion(Suggestion $suggestion) {
        $suggestion->update(['accepted' => 1]);
        $user = $suggestion->user;
        $user->notify(new SuggestionAccepted());
        return back();
    }

    public function rejectSuggestion(Suggestion $suggestion) {
        $suggestion->update(['accepted' => 0]);
        $user = $suggestion->user;
        $user->notify(new SuggestionRejected());
        return back();
    }

    public function load() {
        return view('movies.load');
    }

    public function loadMovies(Request $request) {
        $request->validate([
            'count' => 'required|integer|min:1|max:1000',
        ]);

        $count = $request->count;
        $method = $request->method;

        // call import from api
        
        ImportMoviesJob::dispatch($count, $method);

        return back()->with('success', 'Started loading movies');
    }
}
