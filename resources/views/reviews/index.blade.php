@extends('layouts.app')

@section('title', 'Reviews')

@section('content')
<div class="mx-auto w-full max-w-[110rem] px-6 sm:px-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-6 border-b border-white/[0.07] pb-8 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-display text-4xl font-medium uppercase leading-[1.05] tracking-wider text-base-content sm:text-5xl">
                Community Reviews
            </h1>
            <p class="mt-3 max-w-md text-base-content/60">
                The boldest takes on the latest cinema.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('reviews') }}">
                @if($sort === 'top') <input type="hidden" name="sort" value="top"> @endif
                @if($genreId) <input type="hidden" name="genre" value="{{ $genreId }}"> @endif
                <label for="review-search" class="sr-only">Search reviews by movie or reviewer</label>
                <div class="flex w-full items-center gap-2 rounded-selector border border-white/8 bg-base-200 px-4 py-2.5 transition focus-within:border-primary/40 sm:w-80">
                    @svg('heroicon-o-magnifying-glass', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input id="review-search" type="search" name="search" value="{{ $search }}"
                           placeholder="Search reviews by movie or reviewer…" autocomplete="off"
                           class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/50 focus:outline-none">
                </div>
            </form>

            @auth
                <a href="{{ route('movies.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-selector bg-primary px-5 py-2.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                    @svg('heroicon-o-pencil-square', 'h-4 w-4')
                    Write a review
                </a>
            @endauth
        </div>
    </div>

    @php
        $qSearch = array_filter(['search' => $search]);
        $qSort = $sort === 'top' ? ['sort' => 'top'] : [];
        $qGenre = $genreId ? ['genre' => $genreId] : [];
        $selectedGenre = $genreId ? $genres->firstWhere('id', (int) $genreId) : null;
    @endphp

    {{-- Filters --}}
    <div class="mt-8 flex flex-wrap items-center gap-3">
        <a href="{{ route('reviews', array_merge($qSearch, $qGenre)) }}"
           @class([
               'rounded-selector border px-4 py-2 font-display text-[0.72rem] font-medium uppercase tracking-[0.14em] transition-colors',
               'border-primary/50 bg-primary/10 text-primary' => $sort !== 'top',
               'border-white/[0.08] text-base-content/55 hover:border-white/20 hover:text-base-content' => $sort === 'top',
           ])>
            Latest
        </a>

        <a href="{{ route('reviews', array_merge($qSearch, $qGenre, ['sort' => 'top'])) }}"
           @class([
               'rounded-selector border px-4 py-2 font-display text-[0.72rem] font-medium uppercase tracking-[0.14em] transition-colors',
               'border-primary/50 bg-primary/10 text-primary' => $sort === 'top',
               'border-white/[0.08] text-base-content/55 hover:border-white/20 hover:text-base-content' => $sort !== 'top',
           ])>
            Highest Rated
        </a>

        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button type="button" @click="open = !open"
                @class([
                    'flex items-center gap-2 rounded-selector border px-4 py-2 font-display text-[0.72rem] font-medium uppercase tracking-[0.14em] transition-colors',
                    'border-primary/50 bg-primary/10 text-primary' => $selectedGenre,
                    'border-white/[0.08] text-base-content/55 hover:border-white/20 hover:text-base-content' => ! $selectedGenre,
                ])>
                {{ $selectedGenre->name ?? 'Genre' }}
                @svg('heroicon-o-chevron-down', 'h-3.5 w-3.5')
            </button>

            <div x-show="open" x-transition x-cloak
                 class="absolute left-0 z-20 mt-2 max-h-72 w-48 overflow-y-auto rounded-field border border-white/8 bg-base-200 p-1.5 shadow-xl">
                <a href="{{ route('reviews', array_merge($qSearch, $qSort)) }}"
                   class="block rounded-field px-3 py-2 text-sm text-base-content/70 transition hover:bg-base-300 hover:text-base-content">
                    All genres
                </a>
                @foreach($genres as $genre)
                    <a href="{{ route('reviews', array_merge($qSearch, $qSort, ['genre' => $genre->id])) }}"
                       @class([
                           'block rounded-field px-3 py-2 text-sm transition hover:bg-base-300',
                           'text-primary' => $selectedGenre?->id === $genre->id,
                           'text-base-content/70 hover:text-base-content' => $selectedGenre?->id !== $genre->id,
                       ])>
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>
        </div>

        @if($search !== '' || $selectedGenre || $sort === 'top')
            <a href="{{ route('reviews') }}" class="font-display text-[0.7rem] uppercase tracking-[0.14em] text-base-content/40 transition hover:text-base-content">
                Clear filters
            </a>
        @endif
    </div>

    {{-- Reviews + sidebar --}}
    <div class="mt-10 grid grid-cols-1 gap-10 pb-20 lg:grid-cols-[1fr_320px]">

        <div class="flex flex-col gap-6">
            @forelse($reviews as $review)
                <x-review :review="$review" />
            @empty
                <div class="rounded-box border border-white/6 bg-base-200 p-14 text-center">
                    @svg('heroicon-o-chat-bubble-left-right', 'mx-auto h-12 w-12 text-base-content/25')
                    <h3 class="mt-4 font-display text-lg uppercase tracking-[0.08em] text-base-content">No reviews yet</h3>
                    <p class="mt-2 text-base-content/55">Be the first to share your thoughts on a movie.</p>
                </div>
            @endforelse

            @if($reviews->hasPages())
                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>

        {{-- Top reviewers --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-box border border-white/6 bg-base-200 p-6">
                <div class="mb-5 flex items-baseline justify-between">
                    <h2 class="font-display text-sm font-medium uppercase tracking-[0.2em] text-base-content">Top Reviewers</h2>
                    <span class="font-display text-[0.65rem] uppercase tracking-[0.16em] text-primary">This week</span>
                </div>

                @forelse($topReviewers as $reviewer)
                    <a href="{{ route('profile.show', $reviewer) }}"
                       class="flex items-center gap-3 py-3 transition hover:opacity-80 {{ ! $loop->last ? 'border-b border-white/6' : '' }}">
                        <div class="relative shrink-0">
                            <div class="h-10 w-10 overflow-hidden rounded-full ring-1 ring-white/10">
                                <img src="{{ $reviewer->image ? asset('storage/' . $reviewer->image) : asset('images/person-placeholder.png') }}"
                                     class="h-full w-full object-cover" alt="">
                            </div>
                            <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary font-display text-[0.6rem] font-bold text-primary-content">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                        <span class="flex-1 truncate text-sm font-medium text-base-content">{{ $reviewer->name }}</span>
                        <span class="text-right">
                            <span class="block font-display text-base font-semibold text-base-content">{{ $reviewer->reviews_count }}</span>
                            <span class="block font-display text-[0.6rem] uppercase tracking-[0.14em] text-base-content/45">Reviews</span>
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-base-content/50">No reviews yet this week.</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
