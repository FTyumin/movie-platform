<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\Person;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TmdbApiClient {
    protected Client $http;
    protected string $base;
    protected ?string $bearer;
    protected ?string $apiKey;

    public function __construct() {
        $this->base   = rtrim(config('services.tmdb.base', env('TMDB_BASE', 'https://api.themoviedb.org/3')), '/');
        $this->bearer = env('TMDB_BEARER_TOKEN');
        $this->apiKey = env('TMDB_API_KEY');

        $this->http = new Client([
            'base_uri' => $this->base . '/',
            'timeout'  => 10,
        ]);
    }


    public function getMovieWithExtras(int $movieId, array $append = ['credits', 'images']) {
        $query = [];

        if (!empty($append)) {
            $query['append_to_response'] = implode(',', $append);
        }

        try {
            $res = $this->http->get("movie/{$movieId}", $this->buildOptions($query));
            $data = json_decode((string) $res->getBody(), true);
            return $data;
        } catch (GuzzleException $e) {
            \Log::warning('Api request failed');
            return null;
        }
    }

    public function trailerKey(int $movieId) {

        try {
            $res = $this->http->get("movie/{$movieId}/videos", $this->buildOptions());
            $data = json_decode($res->getBody(), true);

            $videos = $data['results'] ?? [];

            $trailers = array_filter($videos, fn($v) => $v['type'] === 'Trailer');

            $trailer = reset($trailers);
            $trailer_key = $trailer['key'] ?? null;
            return $trailer_key;
        } catch (GuzzleException $e) {
            \Log::warning('Api request failed');
            return null;
        }
    }
    
    public function PosterUrl(?string $path, string $size = 'w500'): ?string {
        if (empty($path)) return null;
        
        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }

    public function loadAdditionalActors(int $movieId) {

        $movieInfo = $this->getMovieWithExtras($movieId);
        $actorIdsWithRole = [];
        // selecting 15 actors
        $actorInfo = array_slice($movieInfo['credits']['cast'], 5, 10);

  
        $actorNames = [];

        foreach ($actorInfo as $actor) {
            $nameParts = explode(' ', $actor['name']);
            $actorNames[] = [
                'first_name' => array_shift($nameParts),
                'last_name' => implode(' ', $nameParts),
            ];

        }

        // dd($actorNames);

        return $actorNames;
    }

    public function personData(int $id) {
        $res = $this->http->get("person/{$id}", $this->buildOptions());
        $data = json_decode((string) $res->getBody(), true);
        return $data;
    }
    
    public function getTopMovies(int $limit = 50, array $opts = []): array {
        $method = $opts['method'] ?? 'discover';
        $collected = [];
        $page = 1;
        $maxPages = 1000;
        
        $discoverDefaults = [
            'sort_by' =>  'vote_average.desc',
            'vote_count.gte' => 1000,
            'with_original_language' => 'en',
            'without_genres' => '10402,10749,99,16'
        ];

        $endpoints = [
            'discover' => 'discover/movie',
            'popular' => 'movie/popular',
            'top-rated' => 'movie/top_rated',
            'now-playing' => 'movie/now_playing',
        ];
        
        $endpoint = $endpoints[$method] ?? $endpoints['discover'];
        
        while(count($collected) < $limit && $page <=$maxPages) {
            $query = ['page' => $page];
            
            if ($endpoint === 'discover/movie') {
                $query = array_merge($query, $discoverDefaults);
            }

            // add extra options
            foreach ($opts as $k => $v) {
                if (!in_array($k, ['method', 'page_size'])) {
                    $query[$k] = $v;
                }
            }

            $options = $this->buildOptions($query);

            try {
                $res = $this->http->get($endpoint, $options);
                $data = json_decode((string) $res->getBody(), true);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                \Log::error("TMDb getTopMovies failed on page {$page}: " . $e->getMessage());
                break;
            }

            $results = $data['results'] ?? [];

            $ids = collect($results)->pluck('id')->all();

            // skip existing movies
            $existingIds = Movie::whereIn('tmdb_id', $ids)->pluck('tmdb_id')->all();
            $existing = array_flip($existingIds);

            if (empty($results)) break;
            
            foreach ($results as $r) {
                $releaseDate = $r['release_date'] ?? null;
                
                $isTooOld = empty($releaseDate) || $releaseDate < '1970-01-01';
                $isExisting = isset($existing[$r['id']]);

                if ($isTooOld || $isExisting) {
                    continue;
                }

                $collected[] = $r;

                if (count($collected) >= $limit) {
                    break 2;
                }
            }
            $page++;
        }
        return array_slice($collected, 0, $limit);
    }

    public function importMovie(int $tmdbId): ?Movie
    {
        $genres = Genre::all()->keyBy('name');
        $movie_info = $this->getMovieWithExtras($tmdbId);
        if (!$movie_info) {
            return null;
        }

        $movie = Movie::updateOrCreate(
            ['tmdb_id' => $movie_info['id']],
            [
                'tmdb_id' => $movie_info['id'],
                'name' => $movie_info['title'],
                'year' => !empty($movie_info['release_date']) ? substr($movie_info['release_date'], 0, 4) : null,
                'description' => $movie_info['overview'],
                'language' => $movie_info['original_language'],
                'tmdb_rating' => $movie_info['vote_average'],
                'poster_url' => $movie_info['poster_path'],
            ]
        );

        $actor_info = array_slice($movie_info['credits']['cast'] ?? [], 0, 5);

        $crew = $movie_info['credits']['crew'] ?? [];
        $director = array_filter($crew, function ($person) {
            return ($person['job'] ?? null) === 'Director';
        });

        $director = reset($director) ?: null;

        if ($director) {
            $nameParts = explode(" ", $director['name']);
            $director_data = $this->personData($director['id']);
            $director = Person::updateOrCreate(
                ['tmdb_id' => $director['id']],
                [
                    'first_name' => array_shift($nameParts),
                    'last_name' => implode(' ', $nameParts),
                    'profile_path' => $director_data['profile_path'],
                    'biography' => $director_data['biography'],
                ]
            );
            $directorIdsWithRole[$director->id] = ['role' => 'director'];
        }

        foreach ($directorIdsWithRole as $personId => $pivot) {
            $movie->people()->attach($personId, $pivot);
        }

        $movie->duration = $movie_info['runtime'] ?? null;
        $movie->trailer_url = $this->trailerKey($movie->tmdb_id);
        $movie->save();

        $movieGenres = $movie_info['genres'] ?? [];
        foreach ($actor_info as $actor) {
            $nameParts = explode(" ", $actor['name']);
            $actor_data = $this->personData($actor['id']);
            $person = Person::updateOrCreate(
                ['tmdb_id' => $actor['id']],
                [
                    'first_name' => array_shift($nameParts),
                    'last_name' => implode(' ', $nameParts),
                    'type' => 'actor',
                    'profile_path' => $actor_data['profile_path'] ?? null,
                    'biography' => $actor_data['biography'] ?? null,
                ]
            );
            $actorIdsWithRole[$person->id] = ['role' => 'actor'];
        }

        foreach ($actorIdsWithRole as $personId => $pivot) {
                $movie->people()->attach($personId, $pivot);
        }
        $genreIds = collect($movieGenres)->map(function ($genreData) use (&$genres) {
            $genre = $genres->firstWhere('name', $genreData['name']);

            if (!$genre) {
                $genre = Genre::create(['name' => $genreData['name']]);
                $genres->push($genre);
            }

            return $genre->id;
        })->toArray();

        $movie->genres()->syncWithoutDetaching($genreIds);

        return $movie;
    }

    protected function buildOptions(array $query = []): array
    {
        if ($this->apiKey && empty($this->bearer)) {
            $query['api_key'] = $this->apiKey;
        }

        $options = ['query' => $query];

        if ($this->bearer) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $this->bearer,
                'Accept' => 'application/json',
            ];
        }

        return $options;
    }
}