@extends('layouts.app')
@section('content')

{{-- ============================================================
     THE MARQUEE — the hero is the cinema's marquee. The featured
     film's title runs huge between two strips of chasing bulbs,
     with a real reader review billed underneath.
     ============================================================ --}}
<section class="grain relative overflow-hidden isolate rounded-box border border-primary/20 bg-base-200">

    {{-- house backdrop, kept faint --}}
    <div class="absolute inset-0 opacity-[0.10]">
        <img src="{{ asset('images/cinema.webp') }}" alt="" class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-base-100 via-base-100/70 to-transparent"></div>
    <div class="spotlight-layer pointer-events-none absolute inset-0"></div>

    <div class="relative mx-auto max-w-5xl px-6 py-16 md:py-24 text-center">

        {{-- eyebrow --}}
        <p class="font-condensed uppercase tracking-[0.4em] text-xs md:text-sm text-primary">
            @auth
                Tonight's programme, {{ auth()->user()->name }}
            @else
                Now Showing
            @endauth
        </p>

        {{-- top run of chase-lights --}}
        <div class="marquee-bulbs mt-8 mx-auto max-w-3xl" aria-hidden="true"></div>

        {{-- the nameplate --}}
        <div class="py-6 md:py-8">
            @if($featuredReview)
                <h1 class="animate-nameplate font-marquee uppercase leading-[0.92] break-words text-base-content text-[2.75rem] sm:text-7xl md:text-8xl"
                    style="text-shadow: 0 0 34px rgba(255,200,90,0.18)">
                    {{ $featuredReview->movie->name }}
                </h1>
            @else
                <h1 class="animate-nameplate font-marquee uppercase leading-[0.92] break-words text-base-content text-6xl sm:text-7xl md:text-8xl"
                    style="text-shadow: 0 0 34px rgba(255,200,90,0.18)">
                    Filmstack
                </h1>
            @endif
        </div>

        {{-- bottom run of chase-lights, running the other way --}}
        <div class="marquee-bulbs reverse mx-auto max-w-3xl" aria-hidden="true"></div>

        {{-- the billing --}}
        @if($featuredReview)
            <div class="animate-billing mt-10 mx-auto max-w-2xl">
                <p class="font-display italic text-xl md:text-2xl leading-snug text-base-content/90">
                    &ldquo;{{ \Illuminate\Support\Str::limit($featuredReview->description, 150) }}&rdquo;
                </p>
                <div class="mt-5 flex items-center justify-center gap-3 font-mono text-xs md:text-sm">
                    <span class="uppercase tracking-widest text-base-content/60">{{ $featuredReview->user->name }}</span>
                    <span class="text-primary">/</span>
                    <a href="{{ route('movies.show', $featuredReview->movie) }}" class="text-base-content/60 hover:text-base-content transition">{{ $featuredReview->movie->name }}</a>
                    <span class="inline-flex items-center gap-1 text-accent">
                        @svg('heroicon-o-star', 'w-4 h-4') {{ $featuredReview->rating }}/10
                    </span>
                </div>
            </div>
        @else
            <p class="animate-billing mt-8 mx-auto max-w-xl text-base-content/70 md:text-lg leading-relaxed">
                Log what you watch, rate it, and see what the whole house is talking about.
            </p>
        @endif

        {{-- admission --}}
        <div class="mt-11 flex flex-wrap items-center justify-center gap-5">
            <a href="/reviews"
               class="ticket inline-flex items-center gap-2 bg-primary px-9 py-3.5 font-condensed uppercase tracking-widest text-sm font-semibold text-primary-content hover:brightness-110 transition focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-base-100">
                @svg('heroicon-o-ticket', 'h-5 w-5')
                Read the reviews
            </a>
            <a href="{{ route('movies.index') }}"
               class="group inline-flex items-center gap-2 font-condensed uppercase tracking-widest text-sm text-base-content/70 hover:text-base-content transition">
                Find a film
                @svg('heroicon-o-arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1')
            </a>
        </div>
    </div>
</section>

@php
    // shared section-heading markup lives inline; keeps the "programme" voice
    // consistent without a partial for four short blocks.
@endphp

{{-- ============================================================
     THIS WEEK'S PROGRAMME — top-rated titles
     ============================================================ --}}
<section class="mx-auto max-w-7xl px-6 lg:px-10 mt-20">
    <div class="mb-10 flex items-end justify-between gap-4 border-b border-base-content/10 pb-4">
        <div>
            <p class="font-condensed uppercase tracking-[0.35em] text-xs text-primary">Now showing &middot; Top rated</p>
            <h2 class="mt-2 font-condensed font-semibold uppercase text-4xl md:text-5xl leading-none tracking-tight text-base-content">
                This Week&rsquo;s Programme
            </h2>
        </div>
        <a href="/movies" class="hidden sm:inline-flex items-center gap-2 font-condensed uppercase tracking-widest text-xs text-base-content/60 hover:text-primary transition whitespace-nowrap">
            Full programme
            @svg('heroicon-o-arrow-right', 'w-4 h-4')
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @foreach($movies as $movie)
            <x-movie-card :movie="$movie" />
        @endforeach
    </div>

    @auth
        {{-- RESERVED FOR YOU — personal recommendations --}}
        @if(!empty($userRecommendations))
            <div class="mt-20 mb-10 flex items-end justify-between gap-4 border-b border-base-content/10 pb-4">
                <div>
                    <p class="font-condensed uppercase tracking-[0.35em] text-xs text-primary">Your private screening</p>
                    <h2 class="mt-2 font-condensed font-semibold uppercase text-4xl md:text-5xl leading-none tracking-tight text-base-content">
                        Reserved For You
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($userRecommendations as $movie)
                    <x-movie-card :movie="$movie['movie']" />
                @endforeach
            </div>
        @endif
    @endauth
</section>

{{-- ============================================================
     ON THE BILL — browse by genre, as a programme listing
     ============================================================ --}}
<section class="mx-auto max-w-7xl px-6 lg:px-10 mt-20">
    <div class="mb-10 border-b border-base-content/10 pb-4">
        <p class="font-condensed uppercase tracking-[0.35em] text-xs text-primary">Choose your showing</p>
        <h2 class="mt-2 font-condensed font-semibold uppercase text-4xl md:text-5xl leading-none tracking-tight text-base-content">
            On The Bill
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($genres as $genre)
            <a href="{{ route('movies.index', ['genres' => [$genre->id]]) }}"
               class="group relative overflow-hidden border border-base-content/10 bg-base-200 hover:bg-base-300 hover:border-primary/40 transition-colors">
                <span class="absolute left-0 top-0 h-full w-1 bg-primary origin-top scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></span>
                <div class="flex items-center justify-between gap-4 px-6 py-6">
                    <span class="font-condensed uppercase tracking-wide text-xl md:text-2xl text-base-content group-hover:text-primary transition-colors">
                        {{ $genre->name }}
                    </span>
                    <span class="font-mono text-xs text-base-content/40 whitespace-nowrap">
                        {{ $genre->movies_count }} films
                        @svg('heroicon-o-arrow-right', 'inline w-3.5 h-3.5 -mt-0.5 transition-transform group-hover:translate-x-1')
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ============================================================
     DOUBLE FEATURES — reader-programmed lists
     ============================================================ --}}
@if($lists->isNotEmpty())
    <section class="mx-auto max-w-7xl px-6 lg:px-10 mt-20 mb-8">
        <div class="mb-10 border-b border-base-content/10 pb-4">
            <p class="font-condensed uppercase tracking-[0.35em] text-xs text-primary">Programmed by readers</p>
            <h2 class="mt-2 font-condensed font-semibold uppercase text-4xl md:text-5xl leading-none tracking-tight text-base-content">
                Double Features
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($lists as $list)
                <a href="{{ route('lists.show', $list) }}" class="group">
                    <div class="bg-base-200 border border-base-content/10 rounded-box p-6 hover:bg-base-300 hover:border-primary/40 transition-all duration-300 h-full flex flex-col">
                        {{-- header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-field flex items-center justify-center shrink-0">
                                @svg('heroicon-o-list-bullet', 'w-6 h-6 text-primary-content')
                            </div>
                            <div class="flex items-center gap-2 font-mono text-xs text-base-content/50 bg-base-300 px-3 py-1 rounded-field">
                                @svg('heroicon-o-film', 'w-3 h-3')
                                {{ $list->movies->count() ?? 0 }} films
                            </div>
                        </div>

                        {{-- poster preview --}}
                        @if($list->movies->count() > 0)
                            <div class="mb-4 flex gap-2 overflow-hidden">
                                @foreach($list->movies->take(3) as $movie)
                                    <div class="flex-1 aspect-[2/3] rounded-field overflow-hidden bg-base-300 relative group/poster">
                                        <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
                                             alt="{{ $movie->name }}"
                                             class="w-full h-full object-cover transition-transform duration-300 group-hover/poster:scale-110">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover/poster:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                @endforeach

                                @if($list->movies->count() > 3)
                                    <div class="flex-1 aspect-[2/3] rounded-field bg-base-300 border-2 border-dashed border-base-content/20 flex items-center justify-center">
                                        <div class="text-center">
                                            <p class="font-condensed text-2xl font-bold text-base-content/50">+{{ $list->movies->count() - 3 }}</p>
                                            <p class="font-mono text-xs text-base-content/40 mt-1">more</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- title --}}
                        <h3 class="font-condensed uppercase tracking-wide text-xl font-semibold text-base-content mb-2 group-hover:text-primary transition-colors line-clamp-2">
                            {{ $list->name }}
                        </h3>

                        {{-- description --}}
                        <p class="text-base-content/60 text-sm mb-4 grow line-clamp-3 leading-relaxed">
                            {{ $list->description ?? 'No description provided' }}
                        </p>

                        {{-- footer --}}
                        <div class="flex items-center justify-between pt-4 border-t border-base-content/10">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}" alt="" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-base-content/80">{{ $list->user->name }}</p>
                                    <p class="font-mono text-xs text-base-content/40">{{ $list->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @svg('heroicon-o-arrow-right', 'w-5 h-5 text-base-content/40 group-hover:text-primary group-hover:translate-x-1 transition-all')
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
