@extends('layouts.app')

@section('title', $search !== '' ? 'Search: '.$search : 'Search')

@section('content')

@php
    $totalCount = $movies->count() + $people->count() + $reviews->count() + $lists->count();

    $tabs = [
        'all'     => ['label' => 'All',     'count' => $totalCount],
        'films'   => ['label' => 'Films',   'count' => $movies->count()],
        'people'  => ['label' => 'People',  'count' => $people->count()],
        'reviews' => ['label' => 'Reviews', 'count' => $reviews->count()],
        'lists'   => ['label' => 'Lists',   'count' => $lists->count()],
    ];

    $showFilms   = in_array($tab, ['all', 'films'])   && $movies->isNotEmpty();
    $showPeople  = in_array($tab, ['all', 'people'])  && $people->isNotEmpty();
    $showReviews = in_array($tab, ['all', 'reviews']) && $reviews->isNotEmpty();
    $showLists   = in_array($tab, ['all', 'lists'])   && $lists->isNotEmpty();
    $showEmpty   = ! $showFilms && ! $showPeople && ! $showReviews && ! $showLists;
@endphp

<div class="mx-auto max-w-5xl px-2 pb-20">

    {{-- Summary --}}
    <h1 class="font-display text-3xl font-semibold uppercase tracking-[0.02em] text-base-content">
        {{ $search !== '' ? 'Results for "'.$search.'"' : 'All results' }}
    </h1>
    <p class="mt-1.5 text-sm text-base-content/55">
        {{ $totalCount }} {{ Str::plural('result', $totalCount) }} across films, people, reviews and lists
    </p>

    {{-- Category tabs --}}
    <div class="cb-rail mt-7 flex items-center gap-1 overflow-x-auto border-b border-white/[0.07]">
        @foreach($tabs as $key => $meta)
            <a href="{{ route('movies.search', ['search' => $search, 'tab' => $key]) }}"
               @class([
                   'flex shrink-0 items-center gap-2 border-b-2 px-4 py-3 font-display text-[0.75rem] font-medium uppercase tracking-[0.1em] transition-colors',
                   'border-primary text-base-content' => $tab === $key,
                   'border-transparent text-base-content/50 hover:text-base-content' => $tab !== $key,
               ])>
                {{ $meta['label'] }}
                <span @class([
                    'rounded-full px-2 py-0.5 text-[11px] font-semibold leading-none',
                    'bg-primary/20 text-primary' => $tab === $key,
                    'bg-base-200 text-base-content/45' => $tab !== $key,
                ])>{{ $meta['count'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-8 flex flex-col gap-12">

        {{-- Films --}}
        @if($showFilms)
            <div class="flex flex-col gap-4">
                @if($tab === 'all')
                    <x-section-head title="Films" />
                @endif

                @foreach($movies as $movie)
                    @php
                        $durationLabel = $movie->duration
                            ? intdiv($movie->duration, 60) . 'h ' . ($movie->duration % 60) . 'm'
                            : null;
                        $director = $movie->director->first();
                        $isSaved = $watchlistIds->contains($movie->id);
                    @endphp
                    <a href="{{ route('movies.show', $movie) }}"
                       class="group grid grid-cols-[80px_1fr] gap-4 rounded-box border border-white/[0.06] bg-base-200 p-4 transition-colors hover:border-primary/40 sm:grid-cols-[96px_1fr] sm:gap-5">

                        <div class="aspect-[2/3] overflow-hidden rounded-field bg-base-300">
                            <img src="https://image.tmdb.org/t/p/w300/{{ $movie->poster_url }}"
                                 alt="{{ $movie->name }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>

                        <div class="flex min-w-0 flex-col justify-center gap-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h3 class="font-display text-lg font-medium uppercase tracking-[0.02em] text-base-content">
                                    {{ $movie->name }}
                                </h3>
                                @if($movie->tmdb_rating)
                                    <span class="flex items-center gap-1">
                                        @svg('heroicon-s-star', 'h-3 w-3 text-primary')
                                        <span class="font-sans text-[13px] font-semibold text-primary">{{ number_format($movie->tmdb_rating, 1) }}</span>
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-base-content/50">
                                <span>{{ $movie->year }}</span>
                                @if($movie->genres->isNotEmpty())
                                    <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                                    <span>{{ $movie->genres->pluck('name')->take(2)->join(', ') }}</span>
                                @endif
                                @if($durationLabel)
                                    <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                                    <span>{{ $durationLabel }}</span>
                                @endif
                            </div>

                            @if($movie->description)
                                <p class="text-sm leading-relaxed text-base-content/60 line-clamp-2">
                                    {{ $movie->description }}
                                </p>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 pt-0.5">
                                @if($director)
                                    <span class="font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45">
                                        Dir. {{ $director->first_name }} {{ $director->last_name }}
                                    </span>
                                @endif
                                @if($isSaved)
                                    <span class="flex items-center gap-1 font-display text-[0.68rem] font-medium uppercase tracking-[0.08em] text-primary">
                                        @svg('heroicon-s-bookmark', 'h-3 w-3')
                                        In watchlist
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- People --}}
        @if($showPeople)
            <div class="flex flex-col gap-4">
                @if($tab === 'all')
                    <x-section-head title="People" />
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($people as $person)
                        @php
                            $filmCount = $person->movies_as_actor_count + $person->movies_as_director_count;
                            $role = $person->movies_as_director_count > 0 && $person->movies_as_actor_count > 0
                                ? 'Actor, Director'
                                : ($person->movies_as_director_count > 0 ? 'Director' : 'Actor');
                            $initials = strtoupper(mb_substr($person->first_name, 0, 1) . mb_substr($person->last_name, 0, 1));
                        @endphp
                        <a href="{{ route('people.show', $person) }}"
                           class="group flex flex-col items-center gap-3 rounded-box border border-white/[0.06] bg-base-200 p-6 text-center transition-colors hover:border-primary/40">
                            <div class="h-16 w-16 overflow-hidden rounded-full bg-base-300 ring-1 ring-white/10">
                                @if($person->profile_path)
                                    <img src="https://image.tmdb.org/t/p/w185/{{ $person->profile_path }}" alt=""
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center font-display text-lg font-semibold text-base-content/60">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-display text-base font-medium uppercase tracking-[0.02em] text-base-content transition-colors group-hover:text-primary">
                                    {{ $person->first_name }} {{ $person->last_name }}
                                </h3>
                                <p class="mt-0.5 text-xs text-base-content/50">{{ $role }}</p>
                            </div>
                            <span class="text-xs text-base-content/40">{{ $filmCount }} {{ Str::plural('film', $filmCount) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Reviews --}}
        @if($showReviews)
            <div class="flex flex-col gap-4">
                @if($tab === 'all')
                    <x-section-head title="Reviews" />
                @endif

                @foreach($reviews as $review)
                    <x-review :review="$review" />
                @endforeach
            </div>
        @endif

        {{-- Lists --}}
        @if($showLists)
            <div class="flex flex-col gap-4">
                @if($tab === 'all')
                    <x-section-head title="Lists" />
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($lists as $list)
                        <a href="{{ route('lists.show', $list) }}"
                           class="group flex flex-col rounded-box border border-white/[0.06] bg-base-200 p-5 transition-colors hover:border-primary/40">
                            <div class="flex items-center gap-2.5">
                                @svg('heroicon-o-list-bullet', 'h-4 w-4 text-primary')
                                <h3 class="font-display text-[0.95rem] font-medium uppercase tracking-[0.02em] text-base-content transition-colors group-hover:text-primary">
                                    {{ $list->name }}
                                </h3>
                            </div>
                            @if($list->description)
                                <p class="mt-2.5 text-sm leading-relaxed text-base-content/55 line-clamp-2">
                                    {{ $list->description }}
                                </p>
                            @endif
                            <div class="mt-4 flex items-center gap-2 text-xs text-base-content/45">
                                <span>{{ $list->movies_count }} {{ Str::plural('movie', $list->movies_count) }}</span>
                                <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                                <span>by {{ $list->user->name }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Empty state --}}
        @if($showEmpty)
            <div class="rounded-box border border-dashed border-white/[0.12] px-6 py-20 text-center">
                @svg('heroicon-o-magnifying-glass', 'mx-auto h-10 w-10 text-base-content/25')
                <p class="mt-4 font-display text-lg font-medium uppercase tracking-[0.02em] text-base-content/85">
                    {{ $totalCount === 0 ? 'No results found' : 'No '.$tabs[$tab]['label'].' found' }}
                </p>
                <p class="mx-auto mt-1.5 max-w-sm text-sm text-base-content/55">
                    @if($totalCount === 0)
                        Try a different search term or browse our catalog.
                    @else
                        Nothing matched in this category — try another tab.
                    @endif
                </p>
                @if($totalCount === 0)
                    <a href="{{ route('movies.index') }}"
                       class="mt-6 inline-flex h-10 items-center rounded-selector bg-primary px-5 font-display text-[0.75rem] font-bold uppercase tracking-[0.08em] text-primary-content transition hover:brightness-110">
                        Browse all movies
                    </a>
                @else
                    <a href="{{ route('movies.search', ['search' => $search, 'tab' => 'all']) }}"
                       class="mt-6 inline-flex h-10 items-center rounded-selector bg-primary px-5 font-display text-[0.75rem] font-bold uppercase tracking-[0.08em] text-primary-content transition hover:brightness-110">
                        View all results
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
