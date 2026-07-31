@extends('layouts.app')

{{-- The hero runs edge-to-edge, so this page manages its own gutters. --}}
@section('main-class', '')

@section('content')

@php
    // The hero is lit by whatever film is currently headlining.
    $heroMovie = $featuredReview?->movie ?? $movies->first();
    $heroImage = $heroMovie?->poster_url
        ? 'https://image.tmdb.org/t/p/w780/' . $heroMovie->poster_url
        : asset('images/cinema.webp');
@endphp

{{-- ============================================================
     THE APERTURE — a single lens iris opening on load, with the
     headlining film burning through behind it. One light source,
     everything else in the dark.
     ============================================================ --}}
<section class="cb-grain relative isolate flex min-h-[36rem] items-center overflow-hidden lg:min-h-[42rem]">

    {{-- headlining film, thrown out of focus so it reads as light, not poster --}}
    <img src="{{ $heroImage }}" alt="" aria-hidden="true"
         class="pointer-events-none absolute inset-0 h-full w-full scale-125 object-cover opacity-20 blur-3xl">

    {{-- the aperture itself --}}
    <div class="cb-animate-iris pointer-events-none absolute left-1/2 top-1/2 aspect-square w-[min(115vw,44rem)] -translate-x-1/2 -translate-y-1/2 lg:left-[62%] lg:w-[min(78vw,50rem)]"
         aria-hidden="true">
        <div class="cb-bloom absolute inset-0"></div>
        <div class="cb-iris-outer absolute inset-0"></div>
        <div class="cb-iris absolute inset-[14%]"></div>
        <div class="cb-iris-sheen absolute inset-[9%]"></div>
    </div>

    {{-- keep the copy readable over the glow --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-base-100 via-base-100/70 to-transparent"></div>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-base-100 via-transparent to-base-100/60"></div>

    <div class="relative mx-auto w-full max-w-[110rem] px-6 py-24 sm:px-8">
        <div class="max-w-xl">
            <h1 class="cb-animate-rise cb-delay-1 mt-5 font-display text-4xl font-medium uppercase leading-[1.05] tracking-[0.06em] text-base-content sm:text-5xl lg:text-6xl">
                The theater<br>is yours.
            </h1>

            <p class="cb-animate-rise cb-delay-2 mt-6 max-w-md text-base leading-relaxed text-base-content/60">
                @auth
                    Pick up where you left off, {{ auth()->user()->name }}. Everything below is
                    drawn from what you've rated, watched and saved.
                @else
                    Rate what you've seen, write what you actually thought, and get
                    recommendations built from your own taste instead of a chart.
                @endauth
            </p>

            <div class="cb-animate-rise cb-delay-3 mt-10 flex flex-wrap items-center gap-4">
                @guest
                    <a href="{{ url('/register') }}"
                       class="inline-flex items-center gap-2.5 rounded-selector bg-primary px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        @svg('heroicon-o-pencil-square', 'h-4 w-4')
                        Start reviewing
                    </a>
                    <a href="{{ route('movies.index') }}"
                       class="inline-flex items-center gap-2 rounded-selector border border-primary/50 px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary transition hover:bg-primary/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        Browse films
                    </a>
                @else
                    <a href="{{ route('movies.index') }}"
                       class="inline-flex items-center gap-2.5 rounded-selector bg-primary px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        @svg('heroicon-o-film', 'h-4 w-4')
                        Browse films
                    </a>
                    <a href="{{ url('/feed') }}"
                       class="inline-flex items-center gap-2 rounded-selector border border-primary/50 px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary transition hover:bg-primary/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        Your feed
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     GENRE RAIL — the filter shortcut, pinned "All films" first
     ============================================================ --}}
@if($genres->isNotEmpty())
    <section class="mx-auto w-full max-w-[110rem] px-6 sm:px-8" aria-label="Browse by genre">
        <div class="relative flex items-center gap-3">
            <a href="{{ route('movies.index') }}"
               class="shrink-0 rounded-selector bg-primary px-6 py-3 font-display text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110">
                All films
            </a>

            {{-- min-w-0 so the rail scrolls instead of widening the page --}}
            <div class="cb-rail flex min-w-0 gap-3 overflow-x-auto py-1">
                @foreach($genres as $genre)
                    <a href="{{ route('movies.index', ['genres' => [$genre->id]]) }}"
                       class="shrink-0 rounded-selector border border-white/[0.07] bg-base-200 px-6 py-3 font-display text-[0.75rem] uppercase tracking-[0.14em] text-base-content/70 transition hover:border-primary/40 hover:bg-base-300 hover:text-base-content">
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>

            {{-- edge fade so the rail reads as scrollable --}}
            <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-base-100 to-transparent"></div>
        </div>
    </section>
@endif

{{-- ============================================================
     TOP REVIEWS
     ============================================================ --}}
@if($topReviews->isNotEmpty())
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-24 sm:px-8">
        <x-section-head title="Top reviews" :href="route('reviews')" link-label="Read all" />

        @php
            // Don't stretch a thin set of reviews across five columns —
            // narrow the grid to whatever there actually is to show.
            $reviewCols = match(true) {
                $topReviews->count() >= 5 => 'sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5',
                $topReviews->count() >= 3 => 'sm:grid-cols-2 lg:grid-cols-3',
                default                   => 'sm:grid-cols-2',
            };
        @endphp

        <div class="grid grid-cols-1 gap-5 {{ $reviewCols }}">
            @foreach($topReviews as $review)
                @php $stars = (int) round($review->rating / 2); @endphp

                <article class="flex flex-col rounded-box border border-white/[0.06] bg-base-200 p-5 transition-colors hover:border-primary/30">
                    <div class="flex items-start gap-4">
                        <a href="{{ route('movies.show', $review->movie) }}" class="shrink-0">
                            <img src="https://image.tmdb.org/t/p/w185/{{ $review->movie->poster_url }}"
                                 alt="{{ $review->movie->name }}"
                                 class="aspect-[2/3] w-14 rounded-field object-cover">
                        </a>

                        <div class="min-w-0">
                            <a href="{{ route('reviews.show', $review) }}"
                               class="font-display text-[0.8rem] font-medium uppercase leading-tight tracking-[0.1em] text-base-content transition-colors hover:text-primary">
                                {{ $review->movie->name }}
                            </a>

                            <div class="mt-2 flex gap-0.5" role="img"
                                 aria-label="Rated {{ $review->rating }} out of 10">
                                @for($i = 1; $i <= 5; $i++)
                                    @svg('heroicon-s-star', 'h-3 w-3 ' . ($i <= $stars ? 'text-primary' : 'text-base-content/20'))
                                @endfor
                            </div>
                        </div>
                    </div>

                    <p class="mt-5 grow font-display text-[0.9rem] italic leading-relaxed text-base-content/65">
                        &ldquo;{{ \Illuminate\Support\Str::limit(strip_tags($review->description), 110) }}&rdquo;
                    </p>

                    <a href="{{ route('profile.show', $review->user) }}"
                       class="mt-5 font-display text-[0.68rem] uppercase tracking-[0.18em] text-primary/80 transition-colors hover:text-primary">
                        &mdash; {{ $review->user->name }}
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

{{-- ============================================================
     TOP RATED
     ============================================================ --}}
@if($movies->isNotEmpty())
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-24 sm:px-8">
        <x-section-head title="Top rated" :href="route('movies.index')" link-label="View all" />

        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($movies as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>
    </section>
@endif

{{-- ============================================================
     PERSONAL RECOMMENDATIONS
     ============================================================ --}}
@auth
    @if(!empty($userRecommendations))
        <section class="mx-auto w-full max-w-[110rem] px-6 pt-24 sm:px-8">
            <x-section-head title="Picked for you" :href="route('movies.index')" link-label="View all" />

            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                @foreach(collect($userRecommendations)->take(5) as $recommendation)
                    <x-movie-card :movie="$recommendation['movie']" />
                @endforeach
            </div>
        </section>
    @endif
@endauth

{{-- ============================================================
     HOW IT WORKS — the wide panel explains the recommender,
     the narrow one hands off to reader-built lists
     ============================================================ --}}
<section class="mx-auto w-full max-w-[110rem] px-6 py-24 sm:px-8">
    <div class="grid gap-5 lg:grid-cols-3">

        <div class="relative overflow-hidden rounded-box border border-white/[0.06] bg-base-200 p-8 lg:col-span-2 lg:p-10">
            <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-[radial-gradient(circle,rgba(255,178,34,0.22),transparent_70%)] blur-2xl" aria-hidden="true"></div>

            <div class="relative">
                <p class="font-display text-[0.72rem] uppercase tracking-[0.22em] text-primary">How we pick</p>
                <p class="mt-5 max-w-lg text-lg leading-relaxed text-base-content/75">
                    Recommendations come from the films you've rated, favourited and
                    marked as seen &mdash; matched on shared directors, cast and genres.
                    Rate a few more and the list moves with you.
                </p>

                <div class="mt-10 flex items-center justify-between gap-6">
                    @svg('heroicon-o-sparkles', 'h-10 w-10 text-primary')
                    @auth
                        <a href="{{ route('movies.index') }}"
                           class="group inline-flex items-center gap-2 font-display text-[0.72rem] uppercase tracking-[0.18em] text-base-content/50 transition-colors hover:text-primary">
                            Rate a film
                            @svg('heroicon-o-arrow-right', 'h-3.5 w-3.5 transition-transform group-hover:translate-x-1')
                        </a>
                    @else
                        <a href="{{ url('/register') }}"
                           class="group inline-flex items-center gap-2 font-display text-[0.72rem] uppercase tracking-[0.18em] text-base-content/50 transition-colors hover:text-primary">
                            Create an account
                            @svg('heroicon-o-arrow-right', 'h-3.5 w-3.5 transition-transform group-hover:translate-x-1')
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <a href="{{ route('lists.index') }}"
           class="group flex flex-col justify-between rounded-box border border-white/[0.06] bg-base-200 p-8 transition-colors hover:border-primary/30">
            <div>
                <p class="font-display text-[0.72rem] uppercase tracking-[0.22em] text-base-content">Community lists</p>
                <p class="mt-4 text-sm leading-relaxed text-base-content/60">
                    @if($lists->isNotEmpty())
                        {{ $lists->count() }} {{ \Illuminate\Support\Str::plural('collection', $lists->count()) }}
                        put together by people using the site &mdash; double features, deep
                        cuts and everything one director ever made.
                    @else
                        Collections put together by people using the site. Build the first
                        one and it shows up here.
                    @endif
                </p>
            </div>

            <div class="mt-10 flex items-center justify-between gap-4">
                @if($lists->isNotEmpty())
                    @php $curators = $lists->pluck('user')->filter()->unique('id'); @endphp
                    <div class="flex items-center">
                        @foreach($curators->take(3) as $curator)
                            <img src="{{ $curator->image ? asset('storage/' . $curator->image) : asset('images/person-placeholder.png') }}"
                                 alt="" class="-ml-2 h-9 w-9 rounded-full object-cover ring-2 ring-base-200 first:ml-0">
                        @endforeach

                        @if($curators->count() > 3)
                            <span class="-ml-2 flex h-9 w-9 items-center justify-center rounded-full bg-primary font-display text-[0.7rem] font-semibold text-primary-content ring-2 ring-base-200">
                                +{{ $curators->count() - 3 }}
                            </span>
                        @endif
                    </div>
                @else
                    <span></span>
                @endif

                @svg('heroicon-o-arrow-right', 'h-5 w-5 shrink-0 text-base-content/40 transition-all group-hover:translate-x-1 group-hover:text-primary')
            </div>
        </a>
    </div>
</section>

@endsection
