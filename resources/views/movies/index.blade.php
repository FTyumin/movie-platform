@extends('layouts.app')

@section('title', 'Browse films')

@section('content')
@php
    $statusLabels = ['favorite' => 'Favorites', 'want_to_watch' => 'Watchlist', 'seen' => 'Seen'];
@endphp

<div class="mx-auto max-w-[100rem]" x-data="{ filtersOpen: false }" @keydown.escape.window="filtersOpen = false">

    <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-[1fr_280px]">

        <main class="min-w-0">

            {{-- Title + count, search, sort --}}
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="font-display text-3xl font-semibold uppercase tracking-[0.02em] text-base-content">
                            Browse films
                        </h1>
                        <p class="mt-1.5 text-sm text-base-content/55">
                            {{ $movies->total() }} of {{ $totalMovies }} titles
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="filtersOpen = true"
                                class="flex items-center gap-2 rounded-selector border border-white/[0.08] bg-base-200 px-4 py-2.5 font-display text-[0.75rem] font-medium uppercase tracking-[0.1em] text-base-content/80 lg:hidden">
                            @svg('heroicon-o-adjustments-horizontal', 'h-4 w-4')
                            Filters
                            @if(count($chips))
                                <span class="rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-bold leading-none text-primary-content">{{ count($chips) }}</span>
                            @endif
                        </button>

                        <label class="flex items-center gap-2.5">
                            <span class="hidden font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-base-content/50 sm:inline">Sort</span>
                            <select name="sort" form="browse-filters" onchange="this.form.requestSubmit()"
                                    class="rounded-field border border-white/[0.08] bg-base-200 px-3 py-2 text-sm text-base-content focus:border-primary/40 focus:outline-none">
                                <option value="rating" @selected($sort === 'rating')>Highest rated</option>
                                <option value="year" @selected($sort === 'year')>Newest first</option>
                                <option value="title" @selected($sort === 'title')>A–Z</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div x-data class="flex w-full max-w-md items-center gap-2.5 rounded-selector border border-white/[0.08] bg-base-200 px-4 py-2.5 transition focus-within:border-primary/40">
                    @svg('heroicon-o-magnifying-glass', 'h-4 w-4 flex-none text-base-content/40')
                    <input type="search" name="q" form="browse-filters" value="{{ request('q') }}"
                           placeholder="Search titles, directors, people" autocomplete="off"
                           @input.debounce.500ms="$el.form.requestSubmit()"
                           class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/45 focus:outline-none">
                </div>
            </div>

            {{-- Active filter chips --}}
            @if(count($chips))
                <div class="mb-6 flex flex-wrap items-center gap-2">
                    @foreach($chips as $chip)
                        <a href="{{ $chip['url'] }}"
                           class="flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary/10 px-3 py-1.5 font-sans text-xs font-medium text-primary transition hover:border-primary/50">
                            {{ $chip['label'] }}
                            <span class="text-sm leading-none opacity-70">&times;</span>
                        </a>
                    @endforeach
                    <a href="{{ route('movies.index') }}" class="px-1 py-1.5 text-xs font-medium text-base-content/45 underline-offset-2 hover:text-base-content/70 hover:underline">
                        Clear all
                    </a>
                </div>
            @endif

            @if($movies->isEmpty())
                <div class="rounded-box border border-dashed border-white/[0.12] px-6 py-16 text-center">
                    <p class="font-display text-lg font-medium uppercase tracking-[0.02em] text-base-content/85">No films match</p>
                    <p class="mx-auto mt-1.5 max-w-sm text-sm text-base-content/55">Try widening the year range or clearing a genre.</p>
                    <a href="{{ route('movies.index') }}"
                       class="mt-5 inline-flex h-10 items-center rounded-selector bg-primary px-5 font-display text-[0.75rem] font-bold uppercase tracking-[0.08em] text-primary-content transition hover:brightness-110">
                        Reset filters
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 xl:grid-cols-4">
                    @foreach($movies as $movie)
                        <x-movie-browse-card :movie="$movie" :saved="$watchlistIds->contains($movie->id)" />
                    @endforeach
                </div>

                @if($movies->lastPage() > 1)
                    <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-white/[0.07] pt-6">
                        <span class="text-xs text-base-content/50">
                            {{ $movies->firstItem() }}–{{ $movies->lastItem() }} of {{ $movies->total() }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            @if($movies->onFirstPage())
                                <span class="flex h-8 w-8 items-center justify-center rounded-field border border-white/[0.06] text-base-content/25">
                                    @svg('heroicon-o-chevron-left', 'h-3.5 w-3.5')
                                </span>
                            @else
                                <a href="{{ $movies->previousPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-field border border-white/[0.08] text-base-content/70 transition hover:border-primary/40">
                                    @svg('heroicon-o-chevron-left', 'h-3.5 w-3.5')
                                </a>
                            @endif

                            @foreach ($movies->getUrlRange(max(1, $movies->currentPage() - 2), min($movies->lastPage(), $movies->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}"
                                   @class([
                                       'flex h-8 min-w-[2rem] items-center justify-center rounded-field px-2 font-display text-[0.75rem] font-semibold transition',
                                       'bg-primary text-primary-content' => $page === $movies->currentPage(),
                                       'border border-white/[0.08] text-base-content/70 hover:border-primary/40' => $page !== $movies->currentPage(),
                                   ])>{{ $page }}</a>
                            @endforeach

                            @if($movies->hasMorePages())
                                <a href="{{ $movies->nextPageUrl() }}" class="flex h-8 w-8 items-center justify-center rounded-field border border-white/[0.08] text-base-content/70 transition hover:border-primary/40">
                                    @svg('heroicon-o-chevron-right', 'h-3.5 w-3.5')
                                </a>
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-field border border-white/[0.06] text-base-content/25">
                                    @svg('heroicon-o-chevron-right', 'h-3.5 w-3.5')
                                </span>
                            @endif
                        </div>

                        <label class="flex items-center gap-2.5">
                            <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-base-content/50">Per page</span>
                            <select name="per_page" form="browse-filters" onchange="this.form.requestSubmit()"
                                    class="rounded-field border border-white/[0.08] bg-base-200 px-3 py-2 text-sm text-base-content focus:border-primary/40 focus:outline-none">
                                <option value="8" @selected($perPage === 8)>8</option>
                                <option value="12" @selected($perPage === 12)>12</option>
                                <option value="16" @selected($perPage === 16)>16</option>
                            </select>
                        </label>
                    </div>
                @endif
            @endif
        </main>

        {{-- Mobile filter backdrop --}}
        <div x-show="filtersOpen" x-transition.opacity @click="filtersOpen = false" x-cloak
             class="fixed inset-0 z-40 bg-black/70 lg:hidden"></div>

        <aside
            :class="filtersOpen ? 'translate-x-0' : 'translate-x-full'"
            class="fixed inset-y-0 right-0 z-50 w-[86%] max-w-xs transform overflow-y-auto border-l border-white/[0.08] bg-base-200 p-6 transition-transform duration-300 ease-out lg:sticky lg:inset-auto lg:top-24 lg:z-auto lg:max-h-[calc(100vh-7rem)] lg:w-auto lg:max-w-none lg:translate-x-0 lg:rounded-box lg:border lg:p-6 lg:transition-none">

            <form id="browse-filters" method="GET" action="{{ route('movies.index') }}"
                  x-data="{ minYear: {{ $minYear }}, minRating: {{ $minRating }} }" class="flex flex-col gap-6">

                <div class="flex items-center justify-between gap-3 lg:hidden">
                    <span class="font-display text-sm font-semibold uppercase tracking-[0.1em] text-base-content">Filters</span>
                    <button type="button" @click="filtersOpen = false" class="text-2xl leading-none text-base-content/50 hover:text-base-content">&times;</button>
                </div>

                <div class="hidden items-center justify-between lg:flex">
                    <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-base-content">Filters</span>
                    <span class="font-display text-[0.7rem] font-semibold text-primary">{{ count($chips) ? count($chips) . ' active' : 'None' }}</span>
                </div>

                <div class="flex flex-col gap-2.5">
                    <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.1em] text-base-content/55">Genre</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($genres as $genre)
                            @php $checked = in_array($genre->id, request('genres', [])); @endphp
                            <label @class([
                                'cursor-pointer select-none rounded-full border px-3 py-1.5 font-sans text-xs font-medium transition-colors',
                                'border-primary/50 bg-primary/15 text-primary' => $checked,
                                'border-white/[0.08] bg-base-100 text-base-content/65 hover:border-white/20' => ! $checked,
                            ])>
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" onchange="this.form.requestSubmit()"
                                       class="sr-only" {{ $checked ? 'checked' : '' }}>
                                {{ $genre->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="h-px bg-white/[0.07]"></div>

                <div class="flex flex-col gap-2.5">
                    <div class="flex items-baseline justify-between gap-2">
                        <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.1em] text-base-content/55">Released after</span>
                        <span class="font-display text-sm font-semibold text-base-content" x-text="minYear"></span>
                    </div>
                    <input type="range" name="min_year" min="1950" max="2025" step="1"
                           x-model.number="minYear" @change="$el.form.requestSubmit()" class="w-full cursor-pointer accent-primary">
                    <div class="flex justify-between text-[11px] text-base-content/40">
                        <span>1950</span><span>2025</span>
                    </div>
                </div>

                <div class="h-px bg-white/[0.07]"></div>

                <div class="flex flex-col gap-2.5">
                    <div class="flex items-baseline justify-between gap-2">
                        <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.1em] text-base-content/55">Min rating</span>
                        <span class="font-display text-sm font-semibold text-base-content" x-text="minRating > 0 ? minRating.toFixed(1) : 'Any'"></span>
                    </div>
                    <input type="range" name="min_rating" min="0" max="9" step="0.5"
                           x-model.number="minRating" @change="$el.form.requestSubmit()" class="w-full cursor-pointer accent-primary">
                </div>

                @auth
                    <div class="h-px bg-white/[0.07]"></div>

                    <div class="flex flex-col gap-2.5">
                        <span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.1em] text-base-content/55">My lists</span>
                        @foreach($statusLabels as $key => $label)
                            <label class="flex cursor-pointer select-none items-center gap-2.5">
                                <input type="checkbox" name="status[]" value="{{ $key }}" onchange="this.form.requestSubmit()"
                                       class="h-4 w-4 flex-none cursor-pointer accent-primary" {{ in_array($key, $statuses) ? 'checked' : '' }}>
                                <span class="flex-1 text-[13.5px] text-base-content/75">{{ $label }}</span>
                                <span class="flex-none text-xs text-base-content/40">{{ $statusCounts[$key] }}</span>
                            </label>
                        @endforeach
                    </div>
                @endauth
            </form>
        </aside>
    </div>
</div>
@endsection
