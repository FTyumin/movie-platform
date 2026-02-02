@extends('layouts.app')

@section('title', $movie->name)

@section('content')

<a href="{{ url()->previous() }}"
    class=" left-4 top-4 inline-flex items-center gap-2 text-gray-300 text-white hover:text-white
        transition text-sm">
    ← Back
</a>
<div class="max-w-6xl mx-auto mt-8 px-4 py-8 ">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Poster --}}
        <div class="md:col-span-1 space-y-4">
            <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}" 
                 alt="{{ $movie->name }} poster" 
                 class="rounded-xl shadow-md w-full top-4">
            @if($movie->rating)
                <div class="rounded-xl border border-gray-700 bg-gray-800/60 p-4 shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-yellow-500">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-400">Platform Score</div>
                            <div class="text-2xl font-semibold text-white">{{ $movie->rating }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{-- Details --}}
        <div class="md:col-span-2 space-y-6">
            <h1 class="text-3xl text-white font-bold">{{ $movie->name }}</h1>
            
            <div class="flex flex-wrap items-center gap-4 text-gray-400">
                <span class="text-sm">{{ $movie->year }}</span>
                <span>•</span>
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-white font-medium">{{ $movie->tmdb_rating }}</span>
                </div>
                <span>•</span>
                <span class="text-sm uppercase">{{ $movie->language }}</span>
                <span>•</span>
                <span class="text-sm">{{ $movie->duration }} mins</span>
            </div>

            {{-- Genres --}}
            <div class="flex flex-wrap gap-2">
                @foreach($movie->genres as $genre)
                 <a href="{{ route('genres.show', $genre->id ) }}" 
                    class="px-3 py-1 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white text-sm rounded-full transition-colors">
                    {{ $genre->name }}
                 </a>
                @endforeach
            </div>

            {{-- Description --}}
            <p class="text-gray-300 leading-relaxed">
                {{ $movie->description }}
            </p>
            {{-- Director & Cast --}}
            <div class="space-y-4 py-4 border-t border-gray-700">
                @if(isset($movie->director) && count($movie->director) > 0)

                    <div class="flex gap-3">
                        <span class="text-sm font-semibold text-gray-400 min-w-[80px]">Director</span>
                        @foreach($movie->director as $person)
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('people.show', $person) }}" 
                                class="text-sm text-blue-400 hover:text-blue-300 hover:underline transition-colors">
                                    {{ $person->first_name }} {{ $person->last_name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif


                @if(isset($movie->actors) && count($movie->actors) > 0)
                    <div class="flex gap-3">
                        <span class="text-sm font-semibold text-gray-400 min-w-[80px]">Cast</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($movie->actors->take(5) as $actor)
                                <a href="{{ route('people.show', $actor->slug) }}" 
                                   class="text-sm text-blue-400 hover:text-blue-300 hover:underline transition-colors">
                                    {{ $actor->first_name }} {{ $actor->last_name }}<span class="text-gray-500">{{ !$loop->last ? ',' : '' }}</span>
                                </a>
                            @endforeach

                            @foreach($additionalActors as $actor)
                                <a href="" 
                                   class="text-sm text-blue-400 hover:text-blue-300 hover:underline transition-colors">
                                    {{ $actor['first_name'] }} {{ $actor['last_name'] }}<span class="text-gray-500">{{ !$loop->last ? ',' : '' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Trailer --}}
            @if ($movie->trailer_url)
                <div class="h-[500px] rounded-xl overflow-hidden bg-gray-900" >
                    <iframe 
                        src="https://www.youtube-nocookie.com/embed/{{ $movie->trailer_url }}" 
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="w-full h-[400px]"
                        style="height:500px;"
                    ></iframe>
                </div>
            @endif

            {{-- Actions --}}
            @if(Auth::check())
                @php
                    $isSeen = Auth::user()->seenMovies->pluck('markable_id')->contains($movie->id);
                    $isWatchList = Auth::user()->wantToWatch->pluck('markable_id')->contains($movie->id);
                    $isFavorite = Auth::user()->favorites->pluck('markable_id')->contains($movie->id);
                @endphp
                <div class="flex gap-3 mt-6">
                    <form action="{{ route('seen.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="group flex flex-col items-center gap-2 w-24 py-3 {{ $isSeen ? 'bg-green-600/20' : 'bg-gray-700/50' }} rounded-lg hover:bg-gray-700 transition-colors">
                            <x-heroicon-o-eye class="w-7 h-7 {{ $isSeen ? 'text-green-500' : 'text-gray-400' }} group-hover:text-green-500 transition-colors" />
                            <span class="text-xs {{ $isSeen ? 'text-green-500' : 'text-gray-400' }} group-hover:text-white transition-colors">Mark as Seen</span>
                        </button>
                    </form>
                    <form action="{{ route('favorite.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="group flex flex-col items-center gap-2 w-24 py-3 {{ $isFavorite ? 'bg-red-600/20' : 'bg-gray-700/50' }} rounded-lg hover:bg-gray-700 transition-colors">
                            <x-heroicon-o-heart class="w-7 h-7 {{ $isFavorite ? 'text-red-500' : 'text-gray-400' }} group-hover:text-red-500 transition-colors" />
                            <span class="text-xs {{ $isFavorite ? 'text-red-500' : 'text-gray-400' }} group-hover:text-white transition-colors">Add to Favorites</span>
                        </button>
                    </form>
                    <form action="{{ route('watchlist.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="group flex flex-col items-center gap-2 w-24 py-3 {{ $isWatchList ? 'bg-blue-600/20' : 'bg-gray-700/50' }} rounded-lg hover:bg-gray-700 transition-colors relative">
                            <x-heroicon-o-clock class="w-7 h-7 {{ $isWatchList ? 'text-blue-500' : 'text-gray-400' }} group-hover:text-blue-500 transition-colors" />
                            @if(!$isWatchList)
                            <div class="absolute top-2 right-2 w-4 h-4 bg-gray-600 rounded-full flex items-center justify-center">
                                <x-heroicon-o-plus class="w-3 h-3 text-gray-300" />
                            </div>
                            @endif
                            <span class="text-xs {{ $isWatchList ? 'text-blue-500' : 'text-gray-400' }} group-hover:text-white transition-colors">Add to Watchlist</span>
                        </button>
                    </form>

                    @if(!Auth::user()->lists->isEmpty())
                        <div class="relative w-24">
                            <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')"
                                    class="group flex flex-col items-center gap-2 w-full py-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                    stroke="currentColor" class="w-7 h-7 text-gray-400 group-hover:text-purple-500 transition-colors">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                        d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Add List</span>
                            </button>
                            
                            <div class="hidden absolute top-full mt-2 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-10">
                                @foreach(Auth::user()->lists as $option)
                                <form action="{{ route('lists.add', $movie->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="listId" value="{{ $option->id }}">
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 first:rounded-t-lg last:rounded-b-lg transition-colors">
                                        {{ $option->name }}
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- admin options for edit, delete -->
            @if(Auth::check() && Auth::user()->is_admin)
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('movies.edit', $movie) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600/20 text-blue-300 hover:bg-blue-600/30 transition">
                        Edit
                    </a>

                    <form action="{{ route('movies.destroy', $movie) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600/20 text-red-300 hover:bg-red-600/30 transition"
                            onclick="return confirm('Delete this movie?')"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
    
    {{-- Similar Movies Section --}}
    @if(count($similarMovies) > 0)
        <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent mt-8"></div>
        <div class="mt-12 pt-8">
            <h2 class="text-2xl font-bold text-white mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($similarMovies as $recommendation)
                    <a href="{{ route('movies.show', $recommendation['movie']->slug) }}" 
                    class="group">
                        <div class="relative overflow-hidden rounded-lg shadow-lg aspect-[2/3]">
                            <img src="https://image.tmdb.org/t/p/w500/{{ $recommendation['movie']->poster_url }}" 
                                alt="{{ $recommendation['movie']->name }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                    <div class="flex items-center gap-1 mb-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-white group-hover:text-yellow-400 transition-colors line-clamp-2">
                            {{ $recommendation['movie']->name }}
                        </h3>
                        <p class="text-xs text-gray-400"></p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- Write Review Section --}}
    <div class="mt-10 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    <div class="mt-12 pt-8">
        <x-create-review :movie="$movie" :reviews="$reviews" :user-review="$userReview" />
    </div>

</div>
@endsection
