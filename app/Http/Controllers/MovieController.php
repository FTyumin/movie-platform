<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\{Movie, Genre, MovieList, Person, Review, Suggestion};
use App\Services\ContentBasedRecommender;
use App\Services\TmdbApiClient;

class MovieController extends Controller
{
    protected ContentBasedRecommender $contentRecommender;
    protected TmdbApiClient $apiClient;
    public function __construct(ContentBasedRecommender $contentRecommender,
     TmdbApiClient $apiClient) {
        $this->contentRecommender = $contentRecommender;
        $this->apiClient = $apiClient;
    }

    public function home() {
        if (!auth()->check()) {
            // first 3 headline the hero poster stack, the rest fill "Trending now"
            $movies = Movie::orderByDesc('tmdb_rating')->take(9)->get();

            $topReviews = Review::with(['user', 'movie'])
                ->withCount(['likedBy', 'comments'])
                ->where('spoilers', false)
                ->whereNotNull('description')
                ->orderByDesc('rating')
                ->latest()
                ->take(3)
                ->get();

            return view('home-guest', compact('movies', 'topReviews'));
        }

        // first 3 headline the hero poster stack, the rest fill "Trending now"
        $movies = Movie::orderByDesc('tmdb_rating')->take(9)->get();
        $genres = Genre::withCount('movies')->orderByDesc('movies_count')->take(10)->get();

        // selecting public lists — only ones with films to show as thumbnails
        $lists = MovieList::visibleTo(auth()->user())
            ->has('movies')
            ->withCount('movies')
            ->with(['user', 'movies' => fn ($q) => $q->take(4)])
            ->orderByDesc('movies_count')
            ->take(3)
            ->get();

        // strongest spoiler-free reviews — the rest fill the "Fresh reviews" rail
        $topReviews = Review::with(['user', 'movie'])
            ->withCount(['likedBy', 'comments'])
            ->where('spoilers', false)
            ->whereNotNull('description')
            ->orderByDesc('rating')
            ->latest()
            ->take(5)
            ->get();

        $id = auth()->id();
        $userRecommendations = [];

        // display recs for logged in user
        if($id) {
            $userRecommendations = Cache::remember("user:{$id}:recs", 3600, function () use ($id) {
                return $this->contentRecommender->getRecommendationsForUser($id, 10);
            });

        }
        return view('home', compact('movies', 'genres', 'lists', 'userRecommendations', 'topReviews'));
    }

    public function index(Request $request) {
        $genres = Genre::orderBy('name')->get();

        $query = Movie::query()->with(['genres']);

        // remove empty filter parameters so pagination links stay clean
        $clean = array_filter($request->query(), fn($v) => $v !== null && $v !== '' && $v !== []);

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('people', function ($p) use ($search) {
                        $p->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('genres')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->whereIn('genres.id', $request->genres);
            });
        }

        $minYear = max(1950, (int) $request->input('min_year', 1950));
        if ($minYear > 1950) {
            $query->where('year', '>=', $minYear);
        }

        $minRating = min(9, max(0, (float) $request->input('min_rating', 0)));
        if ($minRating > 0) {
            $query->where('tmdb_rating', '>=', $minRating);
        }

        $statuses = array_values(array_intersect(
            (array) $request->input('status', []),
            ['favorite', 'want_to_watch', 'seen']
        ));

        if (auth()->check() && ! empty($statuses)) {
            $user = auth()->user();
            $markedIds = collect();
            if (in_array('favorite', $statuses)) $markedIds = $markedIds->merge($user->favorites()->pluck('markable_id'));
            if (in_array('want_to_watch', $statuses)) $markedIds = $markedIds->merge($user->wantToWatch()->pluck('markable_id'));
            if (in_array('seen', $statuses)) $markedIds = $markedIds->merge($user->seenMovies()->pluck('markable_id'));
            $query->whereIn('id', $markedIds->unique());
        }

        $sort = in_array($request->input('sort'), ['year', 'title']) ? $request->input('sort') : 'rating';
        switch ($sort) {
            case 'year':
                $query->orderBy('year', 'desc');
                break;
            case 'title':
                $query->orderBy('name');
                break;
            default:
                $query->orderBy('tmdb_rating', 'desc');
        }

        $perPage = in_array((int) $request->input('per_page'), [8, 12, 16]) ? (int) $request->input('per_page') : 12;

        $movies = $query->paginate($perPage)->appends($clean);
        $totalMovies = Movie::count();

        $watchlistIds = collect();
        $statusCounts = ['favorite' => 0, 'want_to_watch' => 0, 'seen' => 0];
        if (auth()->check()) {
            $user = auth()->user();
            $watchlistIds = $user->wantToWatch()->pluck('markable_id');
            $statusCounts = [
                'favorite' => $user->favorites()->count(),
                'want_to_watch' => $watchlistIds->count(),
                'seen' => $user->seenMovies()->count(),
            ];
        }

        // filter chips shown above the grid — each links back to the same
        // query with just that one value removed
        $buildUrl = function (array $overrides) use ($request) {
            $params = $request->except('page');
            foreach ($overrides as $key => $value) {
                if ($value === null) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
            return route('movies.index', array_filter($params, fn($v) => $v !== null && $v !== '' && $v !== []));
        };

        $statusLabels = ['favorite' => 'Favorites', 'want_to_watch' => 'Watchlist', 'seen' => 'Seen'];

        $chips = [];
        foreach ($request->input('genres', []) as $gid) {
            $genre = $genres->firstWhere('id', (int) $gid);
            if ($genre) {
                $remaining = array_values(array_diff($request->input('genres', []), [$gid]));
                $chips[] = ['label' => $genre->name, 'url' => $buildUrl(['genres' => $remaining ?: null])];
            }
        }
        if ($minYear > 1950) {
            $chips[] = ['label' => "After {$minYear}", 'url' => $buildUrl(['min_year' => null])];
        }
        if ($minRating > 0) {
            $chips[] = ['label' => number_format($minRating, 1) . '+ rating', 'url' => $buildUrl(['min_rating' => null])];
        }
        foreach ($statuses as $s) {
            $remaining = array_values(array_diff($statuses, [$s]));
            $chips[] = ['label' => $statusLabels[$s], 'url' => $buildUrl(['status' => $remaining ?: null])];
        }

        return view('movies.index', compact(
            'movies', 'genres', 'minYear', 'minRating', 'statuses',
            'sort', 'perPage', 'totalMovies', 'watchlistIds', 'statusCounts', 'chips'
        ));
    }

    public function show(Movie $movie)
    {
        $id = $movie->id;

        $similarMovies =  Cache::remember("movie:{$id}:recs", 3600, function () use ($id) {
                return $this->contentRecommender->findSimilarMovies($id, 8);
            });

        $reviews = $movie->reviews()
            ->with(['user', 'likedBy', 'comments'])
            ->latest()
            ->get();

        $userReview = null;
        if (auth()->check()) {
            $userReview = $reviews->firstWhere('user_id', auth()->id());
        }

        $additionalActors = $this->apiClient->loadAdditionalActors($movie->tmdb_id);

        return view('movies.show', compact('movie', 'similarMovies', 'reviews', 'userReview', 'additionalActors'));
    }

    // search bar on homepage
    public function search(Request $request) {
        $search = trim((string) $request->input('search', ''));
        $tab = in_array($request->input('tab'), ['films', 'people', 'reviews', 'lists'])
            ? $request->input('tab')
            : 'all';

        $movies = Movie::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('genres', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('director', fn ($p) => $p->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%"));
                });
            })
            ->with(['genres', 'director'])
            ->latest()
            ->get();

        $people = Person::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->withCount(['moviesAsActor', 'moviesAsDirector'])
            ->get();

        $reviews = Review::query()
            ->with(['user', 'movie'])
            ->withCount(['comments', 'likedBy'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('movie', fn ($m) => $m->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get();

        $lists = MovieList::visibleTo(auth()->user())
            ->with('user')
            ->withCount('movies')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $watchlistIds = auth()->check() ? auth()->user()->wantToWatch()->pluck('markable_id') : collect();

        return view('movies.search', compact('search', 'tab', 'movies', 'people', 'reviews', 'lists', 'watchlistIds'));
    }

    public function create() {
        return view('movies.add');
    }

    public function store(Request $request) {
        $movie = Movie::find($request->movie_id);
        if($movie) {
            return redirect()->back()->with('error', 'Movie already exists in database');
        }
        $movie = $this->apiClient->importMovie($request->movie_id);
        if (!$movie) {
            return redirect()->back()->with('error', 'Movie TMDB ID was not found.');
        }

        return redirect()->route('movies.show', $movie);
    }

    public function storeSuggestion(Request $request) {
        $id = auth()->id();

        $request->validate([
            'title' => 'required|string|min:3|max:30',
        ]);
        
        Suggestion::create([
            'user_id' => $id,
            'title' => $request->title,
        ]);

        session()->flash('success', 'Your suggestion has been sent!');
        return redirect('');
    }

    public function sendSuggestion(Request $request) {
        return view('suggestion');
    }

    public function edit(Movie $movie) {
        return view('movies.edit', compact('movie'));
    }

    public function update(Movie $movie, Request $request) {
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'description' => 'required|string|max:2000',
        ]);

        $movie->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        session()->flash('success', 'Movie updated!');
        return redirect('/movies');
    }

    public function destroy(Movie $movie) {
        $movie->delete();

        session()->flash('success', 'Movie deleted!');
        return redirect('/movies');
    }
}
