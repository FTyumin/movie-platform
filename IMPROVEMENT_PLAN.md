# Movie Platform — Improvement Plan

Grounded in a code review on 2026-07-10 (branch `dev`). Organized by area, with a phased roadmap at the end. Items marked **[bug]** are defects found in the current code, not just polish.

---

## 1. Quick wins / bug fixes (do these first)

### Recommender bugs (`app/Services/ContentBasedRecommender.php`)

### TMDB client (`app/Services/TmdbApiClient.php`)
- `buildOptions()` instantiates an unused Guzzle client and always appends `api_key` even when null; remove both.
- Repeated `attach()` on re-import duplicates pivot rows (`importMovie`, `ImportService`). Use `syncWithoutDetaching()` for people like you already do for genres.

### Housekeeping
- CLAUDE.md still warns about a `dd("debug")` in `ImportService` line 20 — it's gone. Update CLAUDE.md.
- `MovieController::store()` has no validation on `movie_id`; `storeSuggestion()` redirects with `redirect('')` — use named routes.
- `Movie::all()->take(5)` on the homepage loads the whole table into memory. Use `Movie::orderByDesc('tmdb_rating')->take(5)->get()` (or a real "trending" query).

---

## 2. Recommendation algorithm

### Phase A — correctness & shape (after the bug fixes above)
- **Normalize scoring.** Today weights (1.4×, 1.3×, 1.2×, 1.05×) are multiplied onto Jaccard scores, then clamped to 1.0 — the clamp destroys ordering among strong matches. Instead: keep raw scores, sum contributions when the same movie is reached from multiple seeds (a movie similar to 3 of your favorites should beat one similar to 1), and normalize at the end.
- **Use rating signal properly.** Currently only 4–5★ reviews count. Also *penalize* similarity to movies the user rated 1–2★ (negative feedback), and weight seeds by rating (5★ seed > 4★ seed).
- **Deduplicate the pipeline.** `getRecommendationsForUser` merges four lists, dedupes, sorts, slices, backfills, dedupes and sorts *again* — extract a small `RecommendationSet` collector (add / boost / exclude / top-N) so the logic exists once.

### Phase B — performance
- `findSimilarMovies()` computes Jaccard against every overlapping movie in PHP on each request (the 1-hour cache hides this, but cold hits are O(catalog)). **Precompute an item–item similarity table** (`movie_similarities`: movie_id, similar_movie_id, score) refreshed by a scheduled artisan command after imports. Runtime recommendation becomes a single indexed query.
- Invalidate `movie:{id}:recs` caches when new movies are imported (currently only user caches are invalidated, on marks).

### Phase C — quality
- **Richer content features:** TMDB provides `keywords`, decade, runtime class, and original language — add them to the similarity blend (e.g. genres 0.25 / directors 0.30 / actors 0.25 / keywords 0.15 / decade 0.05).
- **Diversity:** apply a simple MMR-style re-rank so the top 10 isn't five sequels of the same franchise.
- **Collaborative filtering (later):** with reviews, favorites, and follows you have enough for user–user similarity ("users who liked what you liked also liked…"). Blend it with content-based scores; fall back to content-based for cold-start users (quiz genres already handle this).
- **Explainability:** carry the reason ("Because you liked *Heat*", "Directed by Denis Villeneuve") through to the UI — cheap to do since seeds are known, and it makes recommendations feel much smarter.
- **Evaluation:** add a Pest test suite with a fixed seeded catalog asserting known similarity orderings, plus a simple offline precision@10 script (hold out 20% of each user's favorites, check how many are recovered).

---

## 3. Backend

### TMDB integration
- **Move off raw Guzzle to Laravel's `Http` client.** You get `Http::fake()` for tests, built-in `retry()` with backoff, and rate limiting — currently a TMDB hiccup mid-import just logs "Api request failed" and continues with nulls.
- **Take the live TMDB call out of the page path.** `MovieController::show` calls `loadAdditionalActors()` (a synchronous TMDB request) on *every* movie page view — this is the single biggest page-speed and reliability issue in the app. Either import the extra cast at import time, or cache the result per movie (long TTL), or lazy-load it via a deferred Livewire component.
- **Unify the two import paths.** `ImportService::importTopMovies()` and `TmdbApiClient::importMovie()` duplicate the person/genre attachment logic with slightly different behavior (e.g. only one sets `type => 'actor'`). Extract a single `MovieImporter` that takes a TMDB payload; the client should only do HTTP.
- **Chunk the import job.** One `ImportMoviesJob` with a 20-minute timeout is fragile — a failure at movie 180/200 loses everything. Dispatch a job batch (one job per movie or per page) so failures retry individually and the admin can see progress (`Bus::batch`).

### General
- **Config & env:** create `config/services.php` entries for TMDB; audit for other `env()` calls outside config.
- **Validation:** move inline validation to FormRequests (`StoreReviewRequest`, `StoreSuggestionRequest`, `LoadMoviesRequest`, …).
- **Authorization:** audit controllers for policy coverage — review/comment/list edit-delete should be behind Policies, not ad-hoc `auth()->id()` checks.
- **Search:** `LIKE %term%` over `movies.name` and two columns of `people` with no pagination. Add pagination now; add a `FULLTEXT` index (MariaDB supports it) or Laravel Scout + Meilisearch later for typo-tolerant search.
- **N+1 audit:** enable `Model::shouldBeStrict()` in local env to surface lazy-loading; `home()` and the feed are likely offenders.
- **Static analysis & style:** add Larastan (level 5+) — it would have caught the undefined-variable and `director_id` bugs above. Enforce Pint in CI.
- **Tests:** Feature tests exist for social features, but **zero coverage** of the recommender, import flow, admin routes, and movie CRUD — the riskiest code. Priorities: recommender unit tests (pure logic, easy), import test with `Http::fake()`, admin authorization tests.
- **CI:** GitHub Actions running `pint --test`, `larastan`, `composer test` on PRs.

---

## 4. UI / Frontend

- **Commit to daisyUI.** It was just added (latest commit) but views are hand-rolled Tailwind with inconsistent patterns (e.g. `home.blade.php` has stray classes like `rounded-lg 0`, yellow-on-unknown-background buttons). Pick daisyUI components for buttons/cards/modals/inputs, define one theme (with the yellow/amber accent as `primary`), and migrate the existing Breeze components (`primary-button`, `modal`, `text-input`…) to it.
- **Dark mode:** `dark:` classes appear sporadically; with daisyUI you get light/dark themes nearly free — wire up a toggle and audit each page in both.
- **Use the tools you already ship.** Livewire 3 is installed but there are no components. Best first candidates:
  - Search with instant results/autocomplete (replaces full-page `/search` reload).
  - Mark toggles (favorite/seen/watchlist) without page reload.
  - Deferred "similar movies" and "additional cast" sections on the movie page (fixes the TMDB-in-page-path problem too).
- **Admin import UX:** the `/load` form dispatches a queue job with no feedback. Show a progress indicator (job batches expose progress) and a completion/failure state.
- **States & polish:** skeleton loaders for poster grids, empty states ("no recommendations yet — rate some movies"), consistent pagination styling, `loading="lazy"` + fixed aspect-ratio boxes on posters (prevents layout shift), a proper 404 for unknown slugs.
- **Recommendation cards:** show the "because you liked X" explanation (§2C) and the similarity context; distinguish "For You" picks from generic popular fills.
- **Accessibility pass:** alt text on posters, focus states (some exist), form labels, color-contrast check on the yellow-on-white accents.

---

## 5. Roadmap

| Phase | Scope | Effort |
|---|---|---|
| **1. Stabilize** | §1 bug fixes; TMDB config move; validation on `store()`; take TMDB call out of movie page; CLAUDE.md update | ~1–2 days |
| **2. Foundation** | Http client migration + retries; unify importers; job batching; FormRequests + policies; Larastan + CI; recommender & import tests | ~1 week |
| **3. Recommender v2** | Scoring normalization + negative feedback; precomputed similarity table + scheduled refresh; explanations | ~1 week |
| **4. UI overhaul** | daisyUI theme + component migration; Livewire search & mark toggles; loading/empty states; dark mode; admin import progress | 1–2 weeks |
| **5. Growth features** | Collaborative filtering blend; Scout/Meilisearch search; keyword features + diversity re-rank; offline evaluation script | ongoing |

Suggested order of first three PRs:
1. `fix/recommender-and-import-bugs` — everything in §1 (small, high value, easy review).
2. `refactor/tmdb-http-client` — config move + `Http` client + tests with `Http::fake()`.
3. `feat/deferred-movie-page-sections` — cache/defer additional actors, invalidate movie rec caches on import.
