@extends('layouts.app')

@section('title', $person->first_name)

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-300 hover:text-white transition-colors mb-4">
        Back
    </a>

    {{-- Header: photo + bio --}}
    <div class="flex flex-col md:flex-row gap-8">
        {{-- Director photo --}}
        <div class="shrink-0">
            <img src="https://image.tmdb.org/t/p/w500/{{ $person->profile_path }}"
                 alt="{{ $person->name }}"
                 class="w-64 h-80 object-cover rounded-xl shadow">
        </div>

        {{-- Details --}}
        <div class="flex-1 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold text-white">{{ $person->first_name }} {{ $person->last_name }}</h1>
                @auth
                    @php
                        $isFavorite = Auth::user()->favoritePeople->pluck('id')->contains($person->id);
                    @endphp
                    <form action="{{ route('person.favorite', $person->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="group inline-flex items-center gap-2 px-3 py-2 {{ $isFavorite ? 'bg-red-600/20' : 'bg-gray-700/50' }} rounded-lg hover:bg-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 text-red-500 group-hover:text-red-500 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <span class="text-xs {{ $isFavorite ? 'text-red-500' : 'text-gray-400' }} group-hover:text-white transition-colors">Add to Favorites</span>
                        </button>
                    </form>
                @endauth
            </div>

            @if($person->biography)
                <p class="text-white leading-relaxed">
                    {{ $person->biography }}
                </p>
            @endif

        </div>
    </div>

    {{-- Directed Movies --}}
    <section class="mt-12">
        
        @if($person->moviesAsDirector && $person->moviesAsDirector->count())
            <h2 class="text-2xl text-white font-semibold mb-4">Directed Movies</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($person->moviesAsDirector as $movie)
                    <a href="{{ route('movies.show', $movie->slug) }}"
                    class="group rounded-xl shadow hover:shadow-md transition overflow-hidden">
                        <div class="group relative">
                                <div class="aspect-[2/3] bg-gray-700 rounded-lg overflow-hidden relative">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"  
                                alt="Movie poster" />
                        </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            
        @endif
    </section>

    <section class="mt-12">
        @if($person->moviesAsActor && $person->moviesAsActor->count())
            <h2 class="text-2xl text-white font-semibold mb-4">Movies</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($person->moviesAsActor as $movie)
                    <a href="{{ route('movies.show', $movie->slug) }}"
                        class="group rounded-xl shadow hover:shadow-md transition overflow-hidden">
                        <div class="group relative">
                            <div class="aspect-[2/3] bg-gray-700 rounded-lg overflow-hidden relative">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                    src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"  
                                    alt="Movie poster" />
                            
                            
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>


@endsection
