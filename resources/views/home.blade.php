@extends('layouts.app')
@section('content')
<section class="relative min-h-[60vh] md:min-h-[55vh] lg:min-h-[50vh] overflow-hidden isolate">

    <!-- Dark cinematic overlay -->
    <div class="absolute inset-0 opacity-10">
        <img src="{{ asset('images/bg-hero.jpg') }}" alt="Cinema background" 
             class="w-full h-full object-cover rounded-md">
    </div>
    
    <!-- Content -->
    <div class="relative mx-auto max-w-6xl px-6 py-32 md:py-40 flex flex-col items-start">
      @auth
        <span class="text-yellow-300 font-semibold tracking-wide text-sm md:text-base uppercase mb-4">
            Welcome Back, {{  auth()->user()->name  ?? '' }}
        </span>
      @endauth
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-tight max-w-3xl drop-shadow-xl">
            Discover. Track. Share.
        </h1>

        <p class="mt-5 max-w-2xl text-white/80 md:text-lg leading-relaxed">
            Browse movies, follow your friends, create lists, and see what everyone is watching.
        </p>

        <!-- Buttons -->
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="/reviews" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-semibold bg-white/10 backdrop-blur border border-white/20 hover:bg-white/20 hover:border-white/40 transition focus:outline-none focus:ring-2 focus:ring-yellow-500/60">
               @svg('heroicon-o-star', 'h-5 w-5 text-yellow-300') 
               Browse Latest Reviews
            </a>

            <a href="#search" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-yellow-500 to-amber-600 text-gray-900 hover:from-yellow-400 hover:to-amber-500 transition shadow-lg shadow-yellow-500/30 focus:outline-none focus:ring-2 focus:ring-yellow-500/60">
               @svg('heroicon-o-magnifying-glass-circle', 'h-5 w-5') 
               Find a Movie
            </a>
        </div>
    </div>
</section>

<!-- Trending Movies -->
<div class="my-20 mt-[10rem] mx-6 sm:mx-8 lg:mx-28 p-8">
  <div class="mb-12">
    <h1 class="mb-3 text-4xl font-bold leading-tight tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
      Featured <span class="text-yellow-600">Movies</span>
    </h1>
    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl">
      Discover the most popular and trending movies that everyone is talking about
    </p>
  </div>

  <!-- Responsive Grid -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
    @foreach($movies as $movie)
        <x-movie-card :movie="$movie" />
    @endforeach
  </div>

  <!-- View All Button -->
  <div class="mt-12 text-center">
    <a href="/movies" class="inline-flex items-center px-6 py-3 text-base font-medium text-yellow-300  rounded-lg 0 focus:outline-none focus:ring-4 focus:ring-blue-300 bg-black transition-colors">
      View All Movies
      @svg('heroicon-o-arrow-right', 'w-4 h-4 ml-2')
    </a>
  </div>
    @auth
        <h1 class="mb-3 mt-10 text-4xl font-bold leading-tight tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
            Our picks <span class="text-yellow-600">For You</span>
        </h1>
    @endauth

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
    @foreach($userRecommendations as $movie)
        <x-movie-card :movie="$movie['movie']" />
    @endforeach
  </div>
</div>

<!-- Genres -->
<div class="my-16 mx-10 sm:px-8 lg:px-28">
    <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-white md:text-5xl lg:text-6xl">Genres</h1>
    <h1 class="mb-2 text-2xl leading-none tracking-tight text-white md:text-2xl lg:text-3xl">Browse movies by genre</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-16 gap-y-10">
        @foreach($genres as $genre)
            <div class="bg-solid-800 border-gray-700 border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:border-white/20">
                <div class="p-5">
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-white ">
                        {{ $genre->name }} ({{ $genre->movies_count }})
                        </h5>
                    </a>
                    <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-6"></div>

                    <a href="{{ route('movies.index', ['genres' => [$genre->id]] ) }}" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gradient-to-r from-yellow-500 to-yellow-600
                      hover:from-yellow-400 hover:to-yellow-500
                      shadow-lg shadow-yellow-500/20 ">
                        View Movies
                        @svg('heroicon-o-arrow-right', 'rtl:rotate-180 w-3.5 h-3.5 ms-2')
                    </a>
                </div>
            </div>   
        @endforeach
    </div>
</div>

<!-- Popular Lists -->
 @if($lists->isNotEmpty())
  <div class="my-16 mx-10 sm:px-8 lg:px-28">
      <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-white md:text-5xl lg:text-6xl">Lists</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-16 gap-y-10">
      @foreach($lists as $list)
    <a href="{{ route('lists.show', $list) }}" class="group">
      <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6 hover:bg-gray-800/70 hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 h-full flex flex-col">
          {{-- List Header --}}
          <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  @svg('heroicon-o-list-bullet', 'w-6 h-6 text-white')
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-400 bg-gray-700/50 px-3 py-1 rounded-full">
                  @svg('heroicon-o-film', 'w-3 h-3')
                  {{ $list->movies->count() ?? 0 }} movies
              </div>
          </div>

          {{-- Movie Posters Preview --}}
          @if($list->movies->count() > 0)
            <div class="mb-4 flex gap-2 overflow-hidden">
                @foreach($list->movies->take(3) as $movie)
                    <div class="flex-1 aspect-[2/3] rounded-lg overflow-hidden bg-gray-700 relative group/poster">
                        <img 
                            src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
                            alt="{{ $movie->title }}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover/poster:scale-110"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover/poster:opacity-100 transition-opacity duration-300"></div>
                    </div>
                @endforeach
                
                @if($list->movies->count() > 3)
                    <div class="flex-1 aspect-[2/3] rounded-lg bg-gray-700/50 border-2 border-dashed border-gray-600 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-400">+{{ $list->movies->count() - 3 }}</p>
                            <p class="text-xs text-gray-500 mt-1">more</p>
                        </div>
                    </div>
                @endif
            </div>
          @endif

          {{-- List Title --}}
          <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors line-clamp-2">
              {{ $list->name }}
          </h3>

          {{-- List Description --}}
          <p class="text-gray-400 text-sm mb-4 flex-grow line-clamp-3 leading-relaxed">
              {{ $list->description ?? 'No description provided' }}
          </p>

          {{-- List Footer --}}
          <div class="flex items-center justify-between pt-4 border-t border-gray-700">
              <div class="flex items-center gap-2">
                  <div class="w-10 h-10 rounded-full overflow-hidden">
                      <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}" alt="" class="h-full w-full object-cover">
                  </div>
                  <div>
                      <p class="text-sm font-medium text-gray-300">{{ $list->user->name }}</p>
                      <p class="text-xs text-gray-500">{{ $list->created_at->diffForHumans() }}</p>
                  </div>
              </div>
              @svg('heroicon-o-arrow-right', 'w-5 h-5 text-gray-400 group-hover:text-blue-400 group-hover:translate-x-1 transition-all')
          </div>
      </div>
  </a>
  @endforeach
  </div>
  </div>

@endif
@endsection
