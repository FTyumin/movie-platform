<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;

class ContentBasedRecommender
{
    function findSimilarMovies($movieId, $limit = 5)
    {
        $target = Movie::with(['genres:id,name', 'people:id'])->find($movieId);
        if (!$target) return [];

        // get movie's genres and actors/directors
        $targetGenres = $target->genres->pluck('id')->all();
        $targetPeople = $target->people->pluck('id')->all();

        // separate actors and directors
        $targetActors = $target->people->where('pivot.role', 'actor')->pluck('id')->all();
        $targetDirectors = $target->people->where('pivot.role', 'director')->pluck('id')->all();

        $similarities = [];

        $candidateIds = Movie::query()
            ->where('id', '!=', $movieId)
            ->where(function ($q) use ($targetGenres, $targetPeople) {
                if (!empty($targetGenres)) {
                    $q->whereIn('id', function ($sub) use ($targetGenres) {
                        $sub->select('movie_id')
                            ->from('genre_movie')
                            ->whereIn('genre_id', $targetGenres);
                    });
                }
                if (!empty($targetPeople)) {
                    $q->orWhereIn('id', function ($sub) use ($targetPeople) {
                        $sub->select('movie_id')
                            ->from('person_movie')
                            ->whereIn('person_id', $targetPeople);
                    });
                }
            })
            ->pluck('id')
            ->all();

        
        if (empty($candidateIds)) return [];

        Movie::whereIn('id', $candidateIds)
            ->with(['genres:id,name', 'people:id'])
            ->chunkById(500, function ($movies) use (
                $targetGenres, $targetActors, $targetDirectors, &$similarities
            ) {
                foreach ($movies as $movie) {
                    $genres2 = $movie->genres->pluck('id')->all();
                    $actors2 = $movie->people->where('pivot.role', 'actor')->pluck('id')->all();
                    $directors2 = $movie->people->where('pivot.role', 'director')->pluck('id')->all();

                    $genreJ = $this->jaccardIndex($targetGenres, $genres2);
                    $actorJ = $this->jaccardIndex($targetActors, $actors2);
                    $directorJ = $this->jaccardIndex($targetDirectors, $directors2);

                    $sim = (0.3 * $genreJ) + (0.4 * $directorJ) + (0.3 * $actorJ);

                    if ($sim > 0.1) {
                        $similarities[] = ['movie' => $movie, 'similarity' => $sim];
                    }
                }
            });

        // sort by similarity, desc order
        usort($similarities, fn ($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($similarities, 0, $limit);
    }

    function calculateMovieSimilarity(Movie $movie1, Movie $movie2) {
        if((!$movie1) or (!$movie2)) {
            return;
        }
        
        // Get parameter IDs as arrays
        $genres1 = $movie1->genres->pluck('id')->all();
        $genres2 = $movie2->genres->pluck('id')->all();
        
        $actors1 = $movie1->actors->pluck('id')->all();
        $actors2 = $movie2->actors->pluck('id')->all();
        
        $director1 = $movie1->director->pluck('id')->all();
        $director2 = $movie2->director->pluck('id')->all();

        // Calculate Jaccard index for each component
        $genreJaccard = $this->jaccardIndex($genres1, $genres2);
        $actorJaccard = $this->jaccardIndex($actors1, $actors2);
        $directorJaccard = $this->jaccardIndex($director1, $director2);

        // Weighted combination
        $similarity = (0.3 * $genreJaccard) + (0.4 * $directorJaccard) + (0.3 * $actorJaccard); 
        
        return $similarity;
    }

    function jaccardIndex($set1, $set2) {
        if (empty($set1) && empty($set2)) {
            return 0;
        }
        
        // Calculate intersection (items in both sets)
        $intersection = count(array_intersect($set1, $set2));
        
        // Calculate union (all unique items from both sets)
        $union = count(array_unique(array_merge($set1, $set2)));
        
        // Jaccard = |A ∩ B| / |A ∪ B|
        return $union > 0 ? $intersection / $union : 0;
    }

    private function collectSimilarMovies(array $ids, float $weight): array
    {
        $result = [];
        foreach ($ids as $id) {
            foreach ($this->findSimilarMovies($id, 5) as $movie) {
                $movie['similarity'] *= $weight;
                $result[] = $movie;
            }
        }
        return $result;
    }

    public function getPersonMovies(array $ids) : array {
        $result = [];

        foreach($ids as $id) {
            $person = Person::find($id);
            if($person->type == 'actor') {

                $movies = $person->moviesAsActor->toArray();
            } else {
                $movies = $person->moviesAsDirector->toArray();
            }
            $result = array_merge($result, $movies);
        }

        $correctResult = [];

        // build correct format
        foreach($movies as $movie) {
            $correctResult[] = [
                'movie' => $movie,
                'similarity' => 0.2,
            ];
        }
        return $correctResult;
    }

    private function checkUserFavorites(array $recs, User $user) {
        // check if recs have user's favorite actors, directors
        // if yes, increase similarity
        $favoriteIds = $user->favoritePeople->pluck('id')->toArray();

        foreach($recs as &$rec) {
            $actors = $rec['movie']->actors->pluck('id')->toArray();

            if(in_array($rec['movie']->director_id, $favoriteIds)) {
                $rec['similarity'] *= 1.2;
            }
            if(array_intersect($actors, $favoriteIds)) {
                $rec['similarity'] *= 1.2;
            }
        }
        return $recs;
    }

    function getRecommendationsForUser($userId, $limit) {
        $user = User::find($userId);
        if(!$user) {
            return;
        }
        // retrieve user's favorite genres
        $favoriteGenres = $user->favoriteGenres;

        //movies that user shouldn't get as recommendations
        $watchedIds = $user->seenMovies()->pluck('markable_id')->toArray();
        $watchlistIds = $user->wantToWatch()->pluck('markable_id')->toArray();
        $favoriteIds = $user->favorites()->pluck('markable_id')->toArray();
        $excludeIds = array_merge($watchedIds, $watchlistIds, $favoriteIds);

        // get user's high ratings(4-5 stars)
        $reviews = $user->reviews()->where('rating', '>=', 4)->get();
        $allRecommendations = [];

        $favoriteSimilar = [];
        $reviewSimilar = [];
        $seenList = [];
        $genreList = [];

        $userHasData = false;

        // limit recommendation parameter
        $max = 5;
        if (count($favoriteIds) > 0) {
            $userHasData = true;
            $favoriteSimilar = $this->collectSimilarMovies(array_slice($favoriteIds, 0, $max), 1.4);
        } 

        if ($reviews && $reviews->count() > 0) {
            $userHasData = true;

            $movieIds = $reviews->pluck('movie_id')->toArray();
            $reviewSimilar = $this->collectSimilarMovies(array_slice($movieIds, 0, $max), 1.3);
        } 

        if (count($watchedIds) > 0) {   
            $userHasData = true;

            $seenList = $this->collectSimilarMovies(array_slice($watchedIds, 0, $max), 1.05);
        }

        if ($favoriteGenres->count() > 0) {
            $userHasData = true;
            
            $count = $favoriteGenres->count();
            $perGenre = floor($limit / $count);
            
            foreach($favoriteGenres as $genre) {
                $genreList = array_merge($genreList, $this->getGenreMovies($genre, $perGenre));
            }

            foreach($genreList as &$movieData) {
                $movieData['similarity'] *= 1.2;
            }
        } 
        
        if(!$userHasData) {
            return $this->getRecommendationsForNewUser($user, $limit);
        }

        $allRecommendations = array_merge($favoriteSimilar, $reviewSimilar, $seenList, $genreList);

        if ($excludeIds) {
            $allRecommendations = array_filter($allRecommendations, function ($rec) use ($excludeIds) {
                return !in_array($rec['movie']->id, $excludeIds);
            });
        }

        $unique = [];
        foreach ($allRecommendations as $rec) {
            $movieId = $rec['movie']->id;

            if (!isset($unique[$movieId])) {
                $unique[$movieId] = $rec;
            }
        }

        $result = array_values($unique);

        usort($result, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $result = array_slice($result, 0, $limit);
        if (count($result) < $limit) {
            $missing = $limit - count($result);

            $existingIds = array_map(fn ($rec) => $rec['movie']->id, $result);
            $extraExclude = array_merge($excludeIds, $existingIds);
            $extra = $this->getPopularMovies($missing, $extraExclude);
            $result = array_merge($extra, $result);

            $unique = [];
            foreach ($result as $rec) {
                $movieId = $rec['movie']->id;

                if (!isset($unique[$movieId])) {
                    $unique[$movieId] = $rec;
                }
            }

            $result = array_values($unique);

            usort($result, function($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });

            $result = array_slice($result, 0, $limit);
        }

        // make sure maximum similarity is 1
        foreach ($result as &$rec) {
            if($rec['similarity'] > 1) {
                $rec['similarity'] = 1;
            }
        }

        $result = $this->checkUserFavorites($result, $user);
        $result = array_slice($result, 0, $limit);
        return $result;
    }

    private function getRecommendationsForNewUser(User $user, $limit) {
        if (count($user->favoriteGenres) == 0) {
            return $this->getPopularMovies($limit);
        }
        $favoriteGenres = $user->favoriteGenres;
        $count = $favoriteGenres->count();
        $perGenre = floor($limit / $count);
        $recs = [];

        foreach($user->favoriteGenres as $genre) {
            $recs = array_merge($recs, $this->getGenreMovies($genre, $perGenre));
        }

        foreach($recs as &$movieData) {
            $movieData['similarity'] *= 1.2;
        }

        return $recs;
    }

    private function getGenreMovies(Genre $genre, $count) {

        $movies = $genre->movies()->limit($count)->get();

        // If not enough movies, fill from global pool
        if ($movies->count() < $count) {
            $missing = $count - $movies->count();

            $extra = $this->getPopularMovies($missing);

            $movies = $movies->merge($extra);
        }

        // Build result format
        return $movies->map(fn($movie) => [
            'movie' => $movie,
            'similarity' => 0.2,
        ])->toArray();
    }

    function getPopularMovies($limit, $excludeIds = []) {
        $popularMovies = Movie::where('tmdb_rating', '>', 8)->whereNotIn('id', $excludeIds)->limit($limit)->get();
        //exclude seen, favorites
      
        $popularMovies = $popularMovies->map(fn($movie) => [
            'movie' => $movie,
            'similarity' => 0.2,
        ])->toArray();

        return $popularMovies;
    }
}
