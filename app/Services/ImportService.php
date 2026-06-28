<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Services\TmdbApiClient;

class ImportService
{
    protected $api;
    public function __construct() {
        $this->api = new TmdbApiClient();
    }

    public function importTopMovies(int $count = 200, string $method): void
    {
        $genres = Genre::all()->keyBy('name');
        $data = $this->api->getTopMovies($count, ['method' => $method]);

        foreach ($data as $tmdbMovie) {
            $exists = Movie::where('tmdb_id', $tmdbMovie['id'])->exists();
            if(!$exists) {
                Movie::updateOrCreate(
                    ['tmdb_id' => $tmdbMovie['id']],
                    [
                        'name' => $tmdbMovie['title'],
                        'year' => !empty($tmdbMovie['release_date']) ? substr($tmdbMovie['release_date'],0,4) : null,
                        'description' => $tmdbMovie['overview'],
                        'language' => $tmdbMovie['original_language'],
                        'tmdb_rating' => $tmdbMovie['vote_average'],
                        'poster_url' => $tmdbMovie['poster_path'],
                    ]
                );

            }
        }

        foreach ($data as $tmdbMovie) {
            $movieInfo = $this->api->getMovieWithExtras($tmdbMovie['id']);
            $movie = Movie::where('tmdb_id', $tmdbMovie['id'])->first();

            // directors
            $directorIdsWithRole = [];
            $directors = array_filter($movieInfo['credits']['crew'], fn($p) => $p['job'] === 'Director');
            foreach ($directors as $director) {
                $person = Person::firstWhere('tmdb_id', $director['id']);
                if(!$person) {
                    $directorData = $this->api->personData($director['id']);
                    $nameParts = explode(' ', $director['name']);
    
                    $person = Person::updateOrCreate(
                        ['tmdb_id' => $director['id']],
                        [
                            'first_name' => array_shift($nameParts),
                            'last_name' => implode(' ', $nameParts),
                            'profile_path' => $directorData['profile_path'],
                            'biography' => $directorData['biography'],
                        ]
                    );

                }
                $directorIdsWithRole[$person->id] = ['role' => 'director'];
            }
            foreach ($directorIdsWithRole as $personId => $pivot) {
                $movie->people()->attach($personId, $pivot);
            }

            // actors
            $actorIdsWithRole = [];
            // selecting 5 actors
            $actorInfo = array_slice($movieInfo['credits']['cast'], 0, 5);

            foreach ($actorInfo as $actor) {
                $person = Person::firstWhere('tmdb_id', $actor['id']);
                if(!$person) {
                    $actorData = $this->api->personData($actor['id']);
                    $nameParts = explode(' ', $actor['name']);
                    $person = Person::updateOrCreate(
                        ['tmdb_id' => $actor['id']],
                        [
                            'first_name' => array_shift($nameParts),
                            'last_name' => implode(' ', $nameParts),
                            'profile_path' => $actorData['profile_path'],
                            'biography' => $actorData['biography'],
                        ]
                    );
                }
                $actorIdsWithRole[$person->id] = ['role' => 'actor'];
            }
            
            // prevents overwrites
            foreach ($actorIdsWithRole as $personId => $pivot) {
                $movie->people()->attach($personId, $pivot);
            }

            // extras
            $movie->duration = $movieInfo['runtime'];
            $movie->trailer_url = $this->api->trailerKey($movie->tmdb_id);
            $movie->save();

            // genres
            $movieGenres = $movieInfo['genres'] ?? [];
            $genreIds = collect($movieGenres)->map(function ($genreData) use (&$genres) {
                $genre = $genres->firstWhere('name', $genreData['name']);
                if (!$genre) {
                    $genre = Genre::create(['name' => $genreData['name']]);
                    $genres->push($genre);
                }
                return $genre->id;
            })->toArray();

            $movie->genres()->syncWithoutDetaching($genreIds);
        }
    }
}
