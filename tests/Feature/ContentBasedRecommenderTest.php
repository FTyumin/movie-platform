<?php

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Review;
use App\Models\Seen;
use App\Models\User;
use App\Models\WantToWatch;
use App\Services\ContentBasedRecommender;
use Maize\Markable\Models\Favorite;

beforeEach(function () {
    $this->recommender = new ContentBasedRecommender();
});

/**
 * Attaches a person to a movie with a given pivot role.
 */
function attachPerson(Movie $movie, Person $person, string $role): void
{
    $movie->people()->attach($person->id, ['role' => $role]);
}

// -----------------------------------------------------------------
// calculateMovieSimilarity — weighting per shared attribute
// -----------------------------------------------------------------

test('similarity is 0 when two movies share nothing', function () {
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();

    $sim = $this->recommender->calculateMovieSimilarity(
        $movie1->load('genres', 'actors', 'director'),
        $movie2->load('genres', 'actors', 'director'),
    );

    expect($sim)->toBe(0.0);
});

test('a shared genre alone contributes 0.3', function () {
    $genre = Genre::factory()->create();
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();
    $movie1->genres()->attach($genre);
    $movie2->genres()->attach($genre);

    $sim = $this->recommender->calculateMovieSimilarity(
        $movie1->load('genres', 'actors', 'director'),
        $movie2->load('genres', 'actors', 'director'),
    );

    expect($sim)->toBe(0.3);
});

test('a shared director alone contributes 0.4', function () {
    $director = Person::factory()->create();
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();
    attachPerson($movie1, $director, 'director');
    attachPerson($movie2, $director, 'director');

    $sim = $this->recommender->calculateMovieSimilarity(
        $movie1->load('genres', 'actors', 'director'),
        $movie2->load('genres', 'actors', 'director'),
    );

    expect($sim)->toBe(0.4);
});

test('a shared actor alone contributes 0.3', function () {
    $actor = Person::factory()->create();
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();
    attachPerson($movie1, $actor, 'actor');
    attachPerson($movie2, $actor, 'actor');

    $sim = $this->recommender->calculateMovieSimilarity(
        $movie1->load('genres', 'actors', 'director'),
        $movie2->load('genres', 'actors', 'director'),
    );

    expect($sim)->toBe(0.3);
});

test('genre + director + actor overlap sums to a perfect 1.0 match', function () {
    $genre = Genre::factory()->create();
    $director = Person::factory()->create();
    $actor = Person::factory()->create();

    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();

    foreach ([$movie1, $movie2] as $movie) {
        $movie->genres()->attach($genre);
        attachPerson($movie, $director, 'director');
        attachPerson($movie, $actor, 'actor');
    }

    $sim = $this->recommender->calculateMovieSimilarity(
        $movie1->load('genres', 'actors', 'director'),
        $movie2->load('genres', 'actors', 'director'),
    );

    expect($sim)->toBe(1.0);
});

// -----------------------------------------------------------------
// findSimilarMovies
// -----------------------------------------------------------------

test('findSimilarMovies excludes the target movie and low-similarity matches', function () {
    $genre = Genre::factory()->create();
    $target = Movie::factory()->create();
    $target->genres()->attach($genre);

    // shares the genre -> similarity 0.3, should be included
    $similar = Movie::factory()->create();
    $similar->genres()->attach($genre);

    // shares nothing -> similarity 0, should be excluded
    $unrelated = Movie::factory()->create();

    $results = $this->recommender->findSimilarMovies($target->id, 5);
    $resultIds = collect($results)->map(fn ($r) => $r['movie']->id);

    expect($resultIds)->toContain($similar->id)
        ->and($resultIds)->not->toContain($target->id)
        ->and($resultIds)->not->toContain($unrelated->id);
});

test('findSimilarMovies sorts results by similarity descending and respects the limit', function () {
    $genre = Genre::factory()->create();
    $director = Person::factory()->create();

    $target = Movie::factory()->create();
    $target->genres()->attach($genre);
    attachPerson($target, $director, 'director');

    // genre only -> 0.3
    $weaker = Movie::factory()->create();
    $weaker->genres()->attach($genre);

    // genre + director -> 0.7
    $stronger = Movie::factory()->create();
    $stronger->genres()->attach($genre);
    attachPerson($stronger, $director, 'director');

    $results = $this->recommender->findSimilarMovies($target->id, 1);

    expect($results)->toHaveCount(1)
        ->and($results[0]['movie']->id)->toBe($stronger->id)
        ->and($results[0]['similarity'])->toBe(0.7);
});

test('findSimilarMovies returns an empty array for an unknown movie id', function () {
    expect($this->recommender->findSimilarMovies(999999, 5))->toBe([]);
});

// -----------------------------------------------------------------
// getPopularMovies
// -----------------------------------------------------------------

test('getPopularMovies only returns movies rated above 8, sorted descending', function () {
    $low = Movie::factory()->create(['tmdb_rating' => 7.9]);
    $mid = Movie::factory()->create(['tmdb_rating' => 8.5]);
    $high = Movie::factory()->create(['tmdb_rating' => 9.5]);

    $results = $this->recommender->getPopularMovies(5);
    $ids = collect($results)->map(fn ($r) => $r['movie']->id)->all();

    expect($ids)->toBe([$high->id, $mid->id])
        ->and($ids)->not->toContain($low->id);
});

test('getPopularMovies respects the excludeIds list', function () {
    $high = Movie::factory()->create(['tmdb_rating' => 9.0]);

    $results = $this->recommender->getPopularMovies(5, [$high->id]);

    expect($results)->toBeEmpty();
});

// -----------------------------------------------------------------
// getRecommendationsForUser — scenario-based, shows how scores evolve
// -----------------------------------------------------------------

test('a brand-new user with no history or genre picks falls back to unweighted popular movies', function () {
    $user = User::factory()->create();
    Movie::factory()->create(['tmdb_rating' => 9.0]);

    $results = $this->recommender->getRecommendationsForUser($user->id, 5);

    expect($results)->not->toBeEmpty();
    foreach ($results as $rec) {
        expect($rec['similarity'])->toBe(0.2);
    }
});

test('favorite genres alone boost genre movies by 1.2x over the base 0.2 score', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create();
    $user->favoriteGenres()->attach($genre);

    // keep below the "popular" threshold (tmdb_rating > 8) so only genre movies qualify
    $movies = Movie::factory()->count(3)->create(['tmdb_rating' => 5.0]);
    foreach ($movies as $movie) {
        $movie->genres()->attach($genre);
    }

    $results = $this->recommender->getRecommendationsForUser($user->id, 3);

    expect($results)->toHaveCount(3);
    foreach ($results as $rec) {
        expect($rec['similarity'])->toBe(0.24); // 0.2 * 1.2
    }
});

test('similar movies to a favorite are weighted 1.4x and capped at a max similarity of 1.0', function () {
    $genre = Genre::factory()->create();
    $director = Person::factory()->create();
    $actor = Person::factory()->create();

    $user = User::factory()->create();
    $favorite = Movie::factory()->create();
    $favorite->genres()->attach($genre);
    attachPerson($favorite, $director, 'director');
    attachPerson($favorite, $actor, 'actor');
    Favorite::add($favorite, $user);

    // perfect match (similarity 1.0) -> weighted 1.4 -> capped back down to 1.0
    $perfectMatch = Movie::factory()->create();
    $perfectMatch->genres()->attach($genre);
    attachPerson($perfectMatch, $director, 'director');
    attachPerson($perfectMatch, $actor, 'actor');

    // genre-only match (similarity 0.3) -> weighted 1.4 -> 0.42, not capped
    $partialMatch = Movie::factory()->create();
    $partialMatch->genres()->attach($genre);

    $results = $this->recommender->getRecommendationsForUser($user->id, 5);
    $byId = collect($results)->keyBy(fn ($r) => $r['movie']->id);

    // the cap assigns a literal int 1, not a float 1.0
    expect($byId[$perfectMatch->id]['similarity'])->toBe(1)
        ->and($byId[$partialMatch->id]['similarity'])->toBe(0.42);
});

test('movies the user has already favorited, seen, or watchlisted are excluded from recommendations', function () {
    $genre = Genre::factory()->create();

    $user = User::factory()->create();
    $favorite = Movie::factory()->create();
    $favorite->genres()->attach($genre);
    Favorite::add($favorite, $user);

    // would otherwise match via shared genre, but the user has already seen it
    $alreadySeen = Movie::factory()->create();
    $alreadySeen->genres()->attach($genre);
    Seen::add($alreadySeen, $user);

    // would otherwise match via shared genre, but it's already on the watchlist
    $alreadyWatchlisted = Movie::factory()->create();
    $alreadyWatchlisted->genres()->attach($genre);
    WantToWatch::add($alreadyWatchlisted, $user);

    $results = $this->recommender->getRecommendationsForUser($user->id, 5);
    $ids = collect($results)->map(fn ($r) => $r['movie']->id);

    expect($ids)->not->toContain($favorite->id)
        ->and($ids)->not->toContain($alreadySeen->id)
        ->and($ids)->not->toContain($alreadyWatchlisted->id);
});

test('favoriting a recommended movie\'s director boosts its similarity by 1.2x', function () {
    $genre = Genre::factory()->create();
    $director = Person::factory()->create();
    $otherDirector = Person::factory()->create();

    $user = User::factory()->create();
    $seedMovie = Movie::factory()->create();
    $seedMovie->genres()->attach($genre);
    Seen::add($seedMovie, $user);

    $boosted = Movie::factory()->create();
    $boosted->genres()->attach($genre);
    attachPerson($boosted, $director, 'director');

    $notBoosted = Movie::factory()->create();
    $notBoosted->genres()->attach($genre);
    attachPerson($notBoosted, $otherDirector, 'director');

    // favoriting a person not tied to any recommended movie must not boost anything
    $user->favoritePeople()->attach($director->id);

    $results = $this->recommender->getRecommendationsForUser($user->id, 5);
    $byId = collect($results)->keyBy(fn ($r) => $r['movie']->id);

    expect($byId[$boosted->id]['similarity'])->toBe(0.378) // 0.3 (genre) * 1.05 (seen) * 1.2 (favorite director boost)
        ->and($byId[$notBoosted->id]['similarity'])->toBe(0.315); // 0.3 (genre) * 1.05 (seen), no boost
});
