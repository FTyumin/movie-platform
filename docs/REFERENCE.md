# Architecture Reference

Technical reference for the movie-platform backend: core services, data model, and routes. This is a lookup document, not a walkthrough — use it to check exact signatures, fields, and relationships while you're working in the code. Frontend (Blade views, Livewire components, Alpine.js, Tailwind/daisyUI) is out of scope; see the templates themselves for those.

All line references are relative to the repository root and may drift as the code changes — if something looks off, trust the source file over this document and update the doc.

---

## 1. Services

All service classes live in `app/Services/`. There are exactly three.

### `TmdbApiClient`

`app/Services/TmdbApiClient.php` — Guzzle HTTP client wrapping the TMDB API (base URI `https://api.themoviedb.org/3/`, 10s timeout).

Constructed with `$bearer = config('services.tmdb.token')` and `$apiKey = config('services.tmdb.api_key')`.

> **Known bug:** `config/services.php` defines the bearer key as `services.tmdb.bearer`, but the client reads `services.tmdb.token`. `$bearer` is therefore always `null`, so the `Authorization: Bearer` path in `buildOptions()` never fires. Only `TMDB_API_KEY` (query-string auth) currently works. See [§4 Known Issues](#4-known-issues).

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `getMovieWithExtras` | `(int $movieId, array $append = ['credits','images'])` | `?array` | GETs `movie/{id}` with `append_to_response`. Returns decoded JSON, or `null` on `GuzzleException`. |
| `trailerKey` | `(int $movieId)` | `?string` | GETs `movie/{id}/videos`, filters to `type === 'Trailer'`, returns the first match's `key`, or `null` if none. |
| `PosterUrl` | `(?string $path, string $size = 'w500')` | `?string` | Builds `https://image.tmdb.org/t/p/{size}{path}`. Returns `null` if `$path` is empty. Note the capitalized method name — inconsistent with the rest of the class's camelCase, but PHP method calls are case-insensitive so it still works. |
| `loadAdditionalActors` | `(int $movieId)` | `array` | Re-fetches movie extras and takes cast slice `[5:15]` (actors 6–15, since the first 5 are loaded on initial import). Splits each name into `first_name`/`last_name`. Returns `[]` if the fetch fails. |
| `personData` | `(int $id)` | `array` | GETs `person/{id}`. **Not wrapped in try/catch** — unlike the other methods, a `GuzzleException` here propagates uncaught. |
| `getTopMovies` | `(int $limit = 50, array $opts = [])` | `array` | Paginates one of four discovery endpoints, up to 1000 pages, skipping movies already in the DB (by `tmdb_id`) and any with a missing or pre-1970 release date. |
| `importMovie` | `(int $tmdbId)` | `?Movie` | Single-movie import: upserts `Movie` by `tmdb_id`, resolves one director + first 5 cast members via `personData()` (N+1 API calls — one request per person), attaches `person_movie` pivot roles, syncs genres (creating any missing `Genre` rows), sets `duration`/`trailer_url`. Returns `null` if the initial TMDB fetch fails. |
| `buildOptions` *(protected)* | `(array $query = [])` | `array` | Always appends `api_key` to the query string; additionally sets a `Bearer` Authorization header when `$this->bearer` is truthy (see bug note above — currently dead code). |

**`getTopMovies` endpoint/option details:**

- `$opts['method']` selects the TMDB endpoint: `discover` (default), `popular`, `top-rated`, or `now-playing`.
- `$opts['page_size']` controls TMDB's per-page count.
- Any other `$opts` keys are merged directly into the query string.
- The `discover` method applies hardcoded defaults: `sort_by=vote_average.desc`, `vote_count.gte=1000`, `with_original_language=en`, `without_genres=10402,10749,99,16` (talk shows, TV movies, documentary, animation).

### `ImportService`

`app/Services/ImportService.php` — bulk import orchestration, invoked from `ImportMoviesJob` (20-minute queue timeout).

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `importTopMovies` | `(int $count = 200, string $method)` | `void` | Note the parameter order: `$count` has a default but `$method` does not, so every call site must still pass `$method` explicitly (legal in PHP, easy to misread). Two-phase: (1) bulk-upsert bare `Movie` rows from `TmdbApiClient::getTopMovies()`, (2) loop again fetching full extras per movie to attach directors, actors (first 5 cast), genres, duration, and trailer. Heavier on TMDB API calls than `TmdbApiClient::importMovie()`. Does not cache `Person::firstWhere('tmdb_id', ...)` lookups across the loop, so repeated people trigger repeated queries. |

### `ContentBasedRecommender`

`app/Services/ContentBasedRecommender.php` — Jaccard-similarity recommendation engine.

**Similarity weights** (used throughout): genres **0.3**, directors **0.4**, actors **0.3**.

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `jaccardIndex` | `($set1, $set2)` | `float` | `\|A ∩ B\| / \|A ∪ B\|`. Returns `0` if both sets are empty. |
| `findSimilarMovies` | `(int $movieId, int $limit = 5)` | `array<{movie: Movie, similarity: float}>` | Candidates are pre-filtered via a SQL subquery to movies sharing at least one genre or person with the target (not a full N² scan), then scored with the weighted Jaccard combination above. Only similarities `> 0.1` are kept; results are sorted descending and sliced to `$limit`. |
| `calculateMovieSimilarity` | `(Movie $movie1, Movie $movie2)` | `float\|array` | Direct pairwise comparison using the same weights. **Inconsistent return type:** returns `[]` (not `0`) if either movie argument is falsy. |
| `getPersonMovies` | `(array $ids)` | `array` | For each person ID, pulls `moviesAsActor` or `moviesAsDirector` depending on the given `type`. Every result gets a flat `similarity => 0.2`. |
| `collectSimilarMovies` *(private)* | `(array $ids, float $weight)` | `array` | Calls `findSimilarMovies($id, 5)` per ID and multiplies each result's similarity by `$weight`. |
| `checkUserFavorites` *(private)* | `(array $recs, User $user)` | `array` | Boosts similarity ×1.2 when the recommended movie's director or any actor appears in `$user->favoritePeople`. |
| `getRecommendationsForUser` | `(int $userId, int $limit)` | `array` | Orchestrator (see flow below). Falls back to `getRecommendationsForNewUser()` if the user has no favorites, reviews, seen movies, or favorite genres at all. |
| `getRecommendationsForNewUser` *(private)* | `(User $user, int $limit)` | `array` | Genre-based via `getGenreMovies()` if the user has favorite genres, otherwise `getPopularMovies($limit)`. |
| `getGenreMovies` *(private)* | `(Genre $genre, int $count)` | `array` | Takes `$count` movies from the genre with **no ordering** in the query itself (an arbitrary `limit()`), backfills any shortfall from `getPopularMovies()`, then sorts the combined set by `tmdb_rating` descending. Flat `similarity => 0.2`. |
| `getPopularMovies` | `(int $limit, $excludeIds = [])` | `array` | Movies with `tmdb_rating > 8`, excluding the given IDs, ordered descending. Flat `similarity => 0.2`. |

**`getRecommendationsForUser` flow:** pulls from four weighted sources and merges them — favorites (×1.4), reviews rated ≥4 (×1.3), seen movies (×1.05), favorite genres (×1.2, via `getGenreMovies`). Already-seen, watchlisted, or favorited movies are excluded from the candidate pool. Results are deduplicated, any shortfall against `$limit` is backfilled via `getPopularMovies()`, the favorite-person boost is applied, the list is re-sorted, and final similarity scores are clamped to a maximum of `1`.

**Caching** is *not* handled inside this service — `MovieController` wraps `getRecommendationsForUser()` / `findSimilarMovies()` in `Cache::remember` at keys `user:{id}:recs` / `movie:{id}:recs` (1-hour TTL). `MarkController`, `ReviewController`, and `QuizController` call `Cache::forget("user:{id}:recs")` on marks, reviews, and quiz completion respectively.

---

## 2. Data model

### Models, tables, fields

| Model | Table | Key fillable fields | Casts |
|---|---|---|---|
| `Movie` | `movies` | `tmdb_id`, `name`, `year`, `description`, `duration`, `rating`, `poster_url`, `trailer_url`, `director_id`†, `language`, `tmdb_rating` | `year:int`, `duration:int`, `rating:decimal:1` |
| `Person` | `people` | `tmdb_id`, `first_name`, `last_name`, `nationality`, `birth_year`, `birth_date`, `profile_path`, `biography` | `birth_year:int`, `birth_date:date` |
| `Genre` | `genres` | `name` | — |
| `User` | `users` | `name`, `email`, `password`, `image`, `is_admin` | `is_admin:bool`, `email_verified_at:datetime`, `password:hashed` |
| `Review` | `reviews` | `user_id`, `movie_id`, `title`, `rating`, `description`, `spoilers` | — |
| `Comment` | `comments` | `user_id`, `review_id`, `description` | — |
| `Activity` | `activities` | `user_id`, `activityable_type`, `activityable_id` | — |
| `MovieList` | `lists` (custom `$table`) | `user_id`, `name`, `description`, `is_private` | — |
| `Suggestion` | `suggestions` | `user_id`, `title`, `accepted` | — |
| `UserRelationship` | `user_relationships` | `follower_id`, `followee_id` | — |
| `Seen` (Mark) | `markable_seen` | — (Mark subclass) | — |
| `WantToWatch` (Mark) | `markable_watchlist` | — (Mark subclass) | — |
| `Favorite` (`Maize\Markable\Models\Favorite`) | `markable_favorites` | — (vendor package model) | — |

† `director_id` is listed in `Movie::$fillable` but no such column exists in the `movies` migration and no matching relationship is defined — dead fillable entry. The director is actually resolved through the `person_movie` pivot's `role` column, not a direct FK.

### Slugs

`Movie` and `Person` both use `Spatie\Sluggable\HasSlug` and override `getRouteKeyName()` to return `'slug'`. Always build URLs with `route('movies.show', $movie)` / `route('people.show', $person)` — never construct them from IDs.

- `Movie` slugs are generated from `name`.
- `Person` slugs are generated from `['first_name', 'last_name']`.

### Relationships

**`Movie`**
- `people()` — `belongsToMany(Person, 'person_movie', 'movie_id', 'person_id')`, pivot column `role` (`actor` | `director`), timestamped.
- `director()` / `actors()` — the same `people()` relation filtered by `wherePivot('role', ...)`.
- `genres()` — `belongsToMany(Genre)` via the default `genre_movie` pivot, timestamped.
- `reviews()` — `hasMany(Review)`.
- `lists()` — `belongsToMany(MovieList, 'movie_lists')`, timestamped.
- `interestedUsers()` / `viewers()` / `fans()` — Markable relations to `WantToWatch` / `Seen` / `Favorite` respectively (used for `withCount('favoriters')` / `watchers` in `AdminController`).

**`Person`**
- `moviesAsActor()` / `moviesAsDirector()` — inverse `belongsToMany(Movie, 'person_movie', 'person_id', 'movie_id')`, filtered by pivot `role`.

**`Genre`**
- `movies()` — `belongsToMany(Movie)` via the default `genre_movie` pivot.
- `favoritedByUsers()` — `belongsToMany(User, 'user_favorite_genres')`. This is the real genre↔user link (quiz output).
- `users()` — declared as `hasMany(User)`, but no `genre_id` FK exists on `users`. **Broken/unused** — use `favoritedByUsers()` instead. See [§4 Known Issues](#4-known-issues).

**`User`**
- `reviews()` / `comments()` — `hasMany`.
- `ratedMovies()` — `belongsToMany(Movie, 'reviews')` with pivot `rating, created_at` — an alternate view of reviews expressed as a pivot relation.
- `wantToWatch()` / `seenMovies()` / `favorites()` — `hasMany(WantToWatch|Seen|Favorite)`, querying the mark tables directly by `user_id`.
- `lists()` — `hasMany(MovieList)`.
- `favoriteGenres()` — `belongsToMany(Genre, 'user_favorite_genres')`.
- `favoritePeople()` — `belongsToMany(Person, 'user_favorite_people', 'user_id', 'person_id')`.
- `followers()` — `hasMany(UserRelationship, 'followee_id')`.
- `followees()` — `hasMany(UserRelationship, 'follower_id')`.
- `likedReviews()` — `belongsToMany(Review, 'review_likes')`.

**`Review`**
- `user()` / `movie()` — `belongsTo`.
- `comments()` — `hasMany(Comment)`.
- `likedBy()` — `belongsToMany(User, 'review_likes')`.

**`Comment`**
- `user()` / `review()` — `belongsTo`.

**`Activity`**
- `user()` — `belongsTo`.
- `activityable()` — `morphTo()`. `activityable_type` stores the fully-qualified class name (no Laravel morph map is configured) and `activityable_id` the row ID.

**`MovieList`**
- `user()` — `belongsTo`.
- `movies()` — `belongsToMany(Movie, 'movie_lists', 'list_id', 'movie_id')`.
- `addMovie()` / `removeMovie()` — helper methods for managing the pivot.
- `scopeVisibleTo($query, $user)` / `canView($user)` — enforce `is_private` visibility.

**`Suggestion`**
- `user()` — `belongsTo`.

**`UserRelationship`**
- `follower()` / `followee()` — `belongsTo(User, ...)`.

### Markable marks

`Movie` and `User` both use the `Markable` trait (`maize-tech/laravel-markable`). Three mark types are in play: `Favorite` (vendor default), `Seen` and `WantToWatch` (custom subclasses).

> **Table naming discrepancy:** package tables resolve as `config('markable.table_prefix', 'markable_') . <snake_case class name>` — there is no `markable.php` config file, so the prefix is always the `markable_` default. The favorites table is therefore **`markable_favorites`**, not `markables`. `User::$markableTable = 'markables'` is set on the model but is not read anywhere by the installed package version — it's dead/vestigial code. See [§4 Known Issues](#4-known-issues).

### Activity creation

`Activity` rows are created from three separate `booted()` static hooks, not a centralized observer:
- `Review::created`
- `MovieList::created`
- `UserRelationship::created`

(`Review` additionally has `#[ObservedBy(ReviewObserver::class)]`, but activity creation itself lives inline in `Review::booted()`, not in the observer.)

---

## 3. Routes

Defined in `routes/web.php` unless noted. `routes/auth.php` (Breeze-style login/register/etc.) is included but not detailed here.

### Admin-only

`middleware(['auth', 'admin'])`. The `Admin` middleware (`app/Http/Middleware/Admin.php`) checks `auth()->check() && auth()->user()->is_admin`, redirecting to `/` otherwise.

| Method | URI | Action | Name |
|---|---|---|---|
| GET | `/admin` | `AdminController@dashboard` | — |
| GET | `/load` | `AdminController@load` | `movies.load` |
| POST | `/load` | `AdminController@loadMovies` | `movies.load.store` |
| resource | `/movies` (except `index`, `show`) | `MovieController` create/store/edit/update/destroy | `movies.*` |
| POST | `/suggestions/{suggestion}/approve` | `AdminController@approveSuggestion` | `suggestions.approve` |
| POST | `/suggestions/{suggestion}/reject` | `AdminController@rejectSuggestion` | `suggestions.reject` |
| GET | `/admin/feed` | `FeedController@adminFeed` | `admin.feed` |

### Public

| Method | URI | Action | Name | Notes |
|---|---|---|---|---|
| GET | `/` | `MovieController@home` | `home` | |
| resource | `/movies` (`index`, `show` only) | `MovieController` | — | |
| GET | `/actors/search` | `PeopleController@search` | `actors.search` | ⚠️ broken — see [§4](#4-known-issues) |
| GET | `/directors/search` | `PeopleController@directorSearch` | `directors.search` | ⚠️ broken — see [§4](#4-known-issues) |
| resource | `/people` (`index`, `show` only) | `PeopleController` | — | ⚠️ `index` broken — see [§4](#4-known-issues) |
| GET | `/genres/{genre}` | `GenreController@show` | `genres.show` | ⚠️ broken — see [§4](#4-known-issues) |
| GET | `/profile/{user}` | `ProfileController@show` | — | |
| GET | `/profile/{user}/reviews` | `ReviewController@showUserReviews` | — | |
| GET | `/profile/{user}/favorites` | `MarkController@listFavorites` | — | |
| GET | `/profile/{user}/seen` | `MarkController@listSeen` | — | |
| GET | `/profile/{user}/watchlist` | `MarkController@listWatchlist` | — | |
| GET | `/users/{userId}/followers` | `UserRelationshipController` | (unnamed) | |
| GET | `/users/{userId}/followees` | `UserRelationshipController` | (unnamed) | |
| resource | `/lists` (`index`,`show`,`create`,`store`,`update`,`destroy`,`edit`) | `MovieListController` | — | ⚠️ sits outside `auth` middleware — see [§4](#4-known-issues) |
| GET | `/reviews` | `ReviewController@index` | `reviews` | |
| GET | `/reviews/{review}` | `ReviewController@show` | `reviews.show` | |
| GET | `/search` | `MovieController@search` | `movies.search` | |

### Auth-only

`middleware('auth')`. Covers: profile edit/update/destroy, `/dashboard`, `/quiz` (show/store), person-favorite toggle, `/feed`, follow/unfollow (`/api/users/{userId}/follow|unfollow` — literal URI prefix, not an actual API route group; still session-authenticated via `web.php`), favorite/watchlist/seen toggles, suggestion send/store, list add/remove-movie, review store/update/destroy/like, comment store/edit/update/destroy.

### Middleware reference

| Middleware | Registered? | Checks | Notes |
|---|---|---|---|
| `Admin` | Yes, aliased `'admin'` in `bootstrap/app.php` | `auth()->check() && auth()->user()->is_admin` | Confirmed working as documented. |
| `CheckQuizCompletion` | **No** — not aliased in `bootstrap/app.php`, not attached to any route or the global stack | Intended to redirect users who haven't completed the quiz | **Dead and broken.** Uses `Auth::check()`/`Auth::user()` with no `use Illuminate\Support\Facades\Auth;` import, and `request->is('quiz*')` is missing the `$` on `$request`. Would fatal-error if it were ever invoked. Quiz gating is **not currently enforced** anywhere in the reachable code path. See [§4 Known Issues](#4-known-issues). |

---

## 4. Known issues

Bugs and dead code surfaced while compiling this reference. Worth knowing before you build on top of the affected areas.

1. **TMDB bearer-token config mismatch** — `TmdbApiClient` reads `config('services.tmdb.token')`, but `config/services.php` defines the key as `services.tmdb.bearer`. The bearer-token auth path is always inert; only `TMDB_API_KEY` works. (`app/Services/TmdbApiClient.php`, `config/services.php`)
2. **`CheckQuizCompletion` middleware is unregistered and broken** — not wired into `bootstrap/app.php` or any route, and would fatal-error on invocation due to a missing `Auth` import and a missing `$` on `$request->is(...)`. Quiz completion is not currently gated. (`app/Http/Middleware/CheckQuizCompletion.php`)
3. **Markable favorites table is `markable_favorites`, not `markables`** — `User::$markableTable = 'markables'` is set but never read by the installed `maize-tech/laravel-markable` version; the package always resolves to its `markable_` prefix default. (`app/Models/User.php`)
4. **`PeopleController` is missing methods referenced by routes** — `search`, `directorSearch`, and `index` are routed but not defined on the controller (only `show()` exists). Hitting `/actors/search`, `/directors/search`, or `/people` (index) will error. (`app/Http/Controllers/PeopleController.php`)
5. **`GenreController::show` references undefined variables** — `$genres`, `$years`, and `$directors` are passed to `compact()` without being defined, so `/genres/{genre}` throws an undefined-variable error on render. (`app/Http/Controllers/GenreController.php`)
6. **`Genre::users()` is broken/unused** — declared as `hasMany(User)` but no `genre_id` FK exists on `users`. The real genre↔user link is `Genre::favoritedByUsers()`. (`app/Models/Genre.php`)
7. **`Movie::$fillable` includes `director_id`**, a column that doesn't exist in the `movies` schema. Dead entry — director resolution goes through the `person_movie` pivot instead. (`app/Models/Movie.php`)
8. **`/lists` resource routes sit outside the `auth` middleware group** despite `store`/`update`/`destroy` requiring a logged-in user; they currently rely on controller-level `Auth::id()` / `auth()->user()` checks instead of route-level enforcement. (`routes/web.php`, `app/Http/Controllers/MovieListController.php`)
