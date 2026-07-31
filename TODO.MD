# TODO

Day-to-day task tracker. For the full audit and phased roadmap, see `IMPROVEMENT_PLAN.md`.

## In Progress
- `findSimilarMovies()` computes Jaccard against every overlapping movie in PHP on each request (the 1-hour cache hides this, but cold hits are O(catalog)). **Precompute an item–item similarity table** (`movie_similarities`: movie_id, similar_movie_id, score) refreshed by a scheduled artisan command after imports. Runtime recommendation becomes a single indexed query.


## Up Next

- fix slow database queries

## Backlog
- `buildOptions()` instantiates an unused Guzzle client and always appends `api_key` even when null; remove both.
- Repeated `attach()` on re-import duplicates pivot rows (`importMovie`, `ImportService`). Use `syncWithoutDetaching()` for people like you already do for genres.

- unused $actorIdsWithRole(line 83) in TmdbApiClient

## Blocked
- [ ]
<!-- note what it's blocked on -->

## Done
- [x] `getRecommendationsForUser()` returns `[]` for an unknown user, `calculateMovieSimilarity()` returns `[]` for a missing movie — verified by `tests/Feature/ContentBasedRecommenderTest.php` and `tests/Unit/ContentBasedRecommenderTest.php` (20 tests passing)
- [x] `checkUserFavorites()` boost now runs before the final sort/slice/clamp (fixed in 9e12402) — boosted items can rise in rank and the 1.0 clamp applies after boosting

---
Conventions: one task per line, most recent/relevant at top of each section. Move a task down through
sections as it progresses; check it off and leave it in **Done** rather than deleting, so there's a record.