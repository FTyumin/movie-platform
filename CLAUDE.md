# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

**Start all services (recommended for development):**
```bash
composer run dev
```
This runs concurrently: PHP server, queue worker, Pail log viewer, and Vite — all required for full functionality.

**Individual services:**
```bash
php artisan serve
php artisan queue:listen --tries=1   # required for movie imports
npm run dev
```

**Testing:**
```bash
composer test          # clears config cache then runs tests
php artisan test       # run all tests
php artisan test --filter TestName   # run a single test
```

**Database:**
```bash
php artisan migrate
php artisan db:seed    # creates admin (feodor.tjumin28@gmail.com) and demo (demo@example.com) users, both with password "password"
```

**Code formatting:**
```bash
./vendor/bin/pint
```

## Architecture

**Stack:** Laravel 12, PHP 8.2+, MariaDB, Blade + Tailwind CSS v4 + daisyUI + Alpine.js, Livewire 3, Pest for tests, database-backed queues.

Tailwind v4 is configured CSS-first in `resources/css/app.css` (`@import "tailwindcss"`, `@plugin "daisyui"`, `@source` directives) — there is no `tailwind.config.js`.

**Environment variables required:**
- `TMDB_BEARER_TOKEN` or `TMDB_API_KEY` — needed for any TMDB API call (movie imports, additional actor loading). Read through `config('services.tmdb.*')` (`config/services.php`), not `env()` directly, so they survive `config:cache`. **Known bug:** `TmdbApiClient` reads `config('services.tmdb.token')`, but `config/services.php` defines the key as `tmdb.bearer` — the bearer-token path is always null, so only `TMDB_API_KEY` actually works right now.
- `DB_CONNECTION=mariadb`, `DB_DATABASE=movie_platform`

### Core Services

**`TmdbApiClient`** (`app/Services/TmdbApiClient.php`) — Guzzle HTTP wrapper for the TMDB API. Handles auth via bearer token (preferred) or API key — see the config-key bug noted above. Methods: `getTopMovies`, `getMovieWithExtras`, `importMovie`, `loadAdditionalActors`, `trailerKey`, `personData`.

**`ImportService`** (`app/Services/ImportService.php`) — Bulk import using `importTopMovies()`. Dispatched via `ImportMoviesJob` (20-minute timeout) to the queue.

**`ContentBasedRecommender`** (`app/Services/ContentBasedRecommender.php`) — Jaccard similarity engine using genres (0.3), directors (0.4), and actors (0.3). `getRecommendationsForUser()` pulls from favorites, high-rated reviews, seen movies, and favorite genres — falls back to popular movies. Covered by `tests/Feature/ContentBasedRecommenderTest.php` and `tests/Unit/ContentBasedRecommenderTest.php`, backed by model factories in `database/factories/`.

Caching lives in the controller layer, not the service itself: `MovieController` wraps `getRecommendationsForUser()`/`findSimilarMovies()` in `Cache::remember` at `user:{id}:recs` / `movie:{id}:recs` (1-hour TTL). `MarkController`, `ReviewController`, and `QuizController` call `Cache::forget("user:{id}:recs")` on marks, reviews, and quiz completion respectively.

See `IMPROVEMENT_PLAN.md` for the current list of known bugs and the recommendation-engine roadmap.

### Key Patterns

**Movie routing uses slugs**, not IDs. `Movie::getRouteKeyName()` returns `'slug'` (via `spatie/laravel-sluggable`). Always use `route('movies.show', $movie)` — never construct URLs with IDs.

**Marking system** (`maize-tech/laravel-markable`): Three mark types — `Favorite`, `Seen` (custom), `WantToWatch` (custom). Both `Movie` and `User` use the `Markable` trait. Toggle methods live in `MarkController`. Mark adds invalidate the recommendation cache.

**Admin access** is gated by `is_admin` (boolean) on the `User` model. The `Admin` middleware (`app/Http/Middleware/Admin.php`) checks this. Admin routes cover: movie import UI, CRUD, suggestion approval, and admin feed.

**Quiz flow**: New users are redirected to `/quiz` (via `CheckQuizCompletion` middleware) to select favorite genres. This populates `user_favorite_genres` and enables genre-based recommendations.

**Activity feed**: `Activity` model with a polymorphic `activityable` relationship. Created automatically via `Review::booted()` when a review is posted. Feed shown at `/feed` for authenticated users.

**Movie import flow**: Admin submits `/load` form → `AdminController::loadMovies()` validates and dispatches `ImportMoviesJob` to the queue → job calls `ImportService::importTopMovies()` → iterates TMDB pages, creates `Movie`, `Person`, attaches genres via pivot. Import methods: `discover` (default, by vote average), `popular`, `top-rated`, `now-playing`.

### Data Model Relationships

- `Movie` ↔ `Person` (many-to-many via `person_movie`, pivot has `role`: `'actor'` or `'director'`)
- `Movie` ↔ `Genre` (many-to-many via `genre_movie`)
- `Movie` ↔ `MovieList` (many-to-many via `movie_lists`)
- `User` → `Review` → `Comment` (nested)
- `User` ↔ `User` via `UserRelationship` (follower/followee)
- `User` ↔ `Genre` via `user_favorite_genres` (quiz output)
- `User` ↔ `Person` via `user_favorite_people`
- Markable tables: `markables` (favorites), plus custom `seen` and `want_to_watch` tables

### Route Structure

- Public: `/`, `/movies`, `/movies/{slug}`, `/people`, `/genres/{genre}`, `/reviews`, `/search`
- Auth-only: `/dashboard`, `/feed`, `/quiz`, marking toggles, review/comment CRUD, lists, suggestions
- Admin-only (middleware `auth` + `admin`): `/admin`, `/load`, movie create/edit/delete, suggestion approve/reject
