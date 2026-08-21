<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function index(Request $request) {
        $sort = $request->get('sort') === 'top' ? 'top' : 'latest';
        $search = trim((string) $request->get('search', ''));
        $genreId = $request->get('genre');

        $reviews = Review::query()
            ->with(['user', 'movie'])
            ->withCount(['comments', 'likedBy'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('movie', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($genreId, function ($query) use ($genreId) {
                $query->whereHas('movie.genres', fn ($q) => $q->where('genres.id', $genreId));
            })
            ->when($sort === 'top', fn ($query) => $query->orderByDesc('rating'), fn ($query) => $query->latest())
            ->paginate(10)
            ->withQueryString();

        $genres = Genre::orderBy('name')->get();

        $topReviewers = User::withCount(['reviews' => fn ($q) => $q->where('created_at', '>=', now()->subWeek())])
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        return view('reviews.index', compact('reviews', 'genres', 'topReviewers', 'sort', 'search', 'genreId'));
    }

    public function show(Review $review) {
        $review->load('comments');
        return view('reviews.show', compact('review'));
    }

    public function showUserReviews(User $user) {
        $reviews = Review::where('user_id', $user->id)->get();
        return view('reviews.user', compact('reviews', 'user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return back()->with('warning', 'You must be logged in to write a review');
        }

        $validated = $request->validate([
            'movie_id' => 'required|integer|exists:movies,id',
            'title' => 'required|string|min:5|max:30',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);

        if (Review::where('user_id', $user->id)->where('movie_id', $movie->id)->exists()) {
            return back()->with('error', 'You have already reviewed this movie.');
        }

        Review::create([
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'description' => $validated['comment'],
            'spoilers' => $request->boolean('spoilers'),
        ]);

        Cache::forget("user:" . $user->id . ":recs");

        return back()->with('success', 'Review successfully posted.');
    }

    public function update(Request $request, Review $review)
    {
        $user = $request->user();
        if (!$user || $review->user_id !== $user->id) {
            return back()->with('error', 'You can only edit your own reviews.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:30',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'description' => $validated['comment'],
            'spoilers' => $request->boolean('spoilers'),
        ]);

        Cache::forget("user:" . $user->id . ":recs");

        return back()->with('success', 'Review updated.');
    }

    public function destroy(Request $request, Review $review)
    {
        $user = $request->user();
        if (!$user || (!$user->is_admin && $review->user_id !== $user->id)) {
            return back()->with('error', 'You can only delete your own reviews.');
        }

        $review->delete();

        Cache::forget("user:" . $user->id . ":recs");

        return back()->with('status', 'Review successfully deleted.');
    }

    public function toggleLike(Review $review)
    {
        $user = auth()->user();
        if ($review->likedBy()->where('user_id', $user->id)->exists()) {
            // Unlike
            $review->likedBy()->detach($user->id);
        } else {
            // Like
            $review->likedBy()->attach($user->id);
        }
        // user remains on same page
        return back();
    }
}
