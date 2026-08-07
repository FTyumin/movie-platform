@extends('layouts.app')

@section('title', $movie->name)

{{-- The hero runs edge-to-edge, so this page manages its own gutters. --}}
@section('main-class', '')

@section('content')

@php
    $posterImage = $movie->poster_url
        ? 'https://image.tmdb.org/t/p/w780/' . $movie->poster_url
        : asset('images/cinema.webp');

    $durationLabel = $movie->duration
        ? intdiv($movie->duration, 60) . 'h ' . ($movie->duration % 60) . 'm'
        : null;

    $isSeen = auth()->check() && auth()->user()->seenMovies->pluck('markable_id')->contains($movie->id);
    $isWatchList = auth()->check() && auth()->user()->wantToWatch->pluck('markable_id')->contains($movie->id);
    $isFavorite = auth()->check() && auth()->user()->favorites->pluck('markable_id')->contains($movie->id);
@endphp

{{-- ============================================================
     HERO — the poster thrown out of focus behind the title, with
     a crisp copy of it floating in front as the one sharp thing
     in the frame.
     ============================================================ --}}
<section class="cb-grain relative isolate overflow-hidden">

    <img src="{{ $posterImage }}" alt="" aria-hidden="true"
         class="pointer-events-none absolute inset-0 h-full w-full scale-125 object-cover opacity-30 blur-3xl">

    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-base-100 via-base-100/70 to-base-100/20"></div>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-base-100/60 via-transparent to-transparent"></div>

    <a href="{{ url()->previous() }}"
       class="absolute left-5 top-5 z-10 inline-flex items-center gap-1.5 rounded-selector bg-base-100/60 px-3.5 py-2 font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/80 backdrop-blur-md transition hover:bg-base-100/90 hover:text-base-content sm:left-8 sm:top-8">
        @svg('heroicon-o-arrow-left', 'h-3.5 w-3.5')
        Back
    </a>

    <div class="relative mx-auto flex w-full max-w-[110rem] flex-col gap-8 px-6 pb-12 pt-28 sm:px-8 sm:pb-16 lg:flex-row lg:items-end">

        <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
             alt="{{ $movie->name }} poster"
             class="hidden w-44 shrink-0 rounded-box object-cover shadow-2xl ring-1 ring-white/10 sm:block lg:w-56">

        <div class="min-w-0">
            @if($movie->tmdb_rating)
                <div class="inline-flex items-center gap-1.5 rounded-selector bg-base-200/80 px-3 py-1 font-display text-[0.75rem] font-medium text-primary backdrop-blur-sm">
                    @svg('heroicon-s-star', 'h-3.5 w-3.5')
                    {{ $movie->tmdb_rating }}
                </div>
            @endif

            <h1 class="mt-4 font-display text-4xl font-medium uppercase leading-[1.05] tracking-[0.02em] text-base-content sm:text-5xl lg:text-6xl">
                {{ $movie->name }}
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1 font-display text-[0.8rem] uppercase tracking-[0.1em] text-base-content/60">
                <span>{{ $movie->year }}</span>
                @if($durationLabel)
                    <span class="text-base-content/30">&middot;</span>
                    <span>{{ $durationLabel }}</span>
                @endif
                @if($movie->genres->isNotEmpty())
                    <span class="text-base-content/30">&middot;</span>
                    <span>{{ $movie->genres->take(2)->pluck('name')->join(' / ') }}</span>
                @endif
                @if($movie->language)
                    <span class="text-base-content/30">&middot;</span>
                    <span>{{ $movie->language }}</span>
                @endif
            </div>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                @if($movie->trailer_url)
                    <a href="#trailer"
                       class="inline-flex items-center gap-2.5 rounded-selector bg-primary px-6 py-3 font-display text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        @svg('heroicon-o-play', 'h-4 w-4')
                        Watch trailer
                    </a>
                @endif

                @auth
                    <form action="{{ route('watchlist.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                @class([
                                    'inline-flex items-center gap-2 rounded-selector border px-5 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] transition',
                                    'border-primary bg-primary/10 text-primary' => $isWatchList,
                                    'border-white/15 text-base-content/70 hover:border-primary/40 hover:text-base-content' => ! $isWatchList,
                                ])>
                            @svg($isWatchList ? 'heroicon-s-clock' : 'heroicon-o-clock', 'h-4 w-4')
                            {{ $isWatchList ? 'On watchlist' : 'Watchlist' }}
                        </button>
                    </form>

                    <form action="{{ route('seen.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                @class([
                                    'inline-flex items-center gap-2 rounded-selector border px-5 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] transition',
                                    'border-primary bg-primary/10 text-primary' => $isSeen,
                                    'border-white/15 text-base-content/70 hover:border-primary/40 hover:text-base-content' => ! $isSeen,
                                ])>
                            @svg($isSeen ? 'heroicon-s-eye' : 'heroicon-o-eye', 'h-4 w-4')
                            Seen
                        </button>
                    </form>

                    <form action="{{ route('favorite.toggle', $movie->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                @class([
                                    'inline-flex items-center gap-2 rounded-selector border px-5 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] transition',
                                    'border-primary bg-primary/10 text-primary' => $isFavorite,
                                    'border-white/15 text-base-content/70 hover:border-primary/40 hover:text-base-content' => ! $isFavorite,
                                ])>
                            @svg($isFavorite ? 'heroicon-s-heart' : 'heroicon-o-heart', 'h-4 w-4')
                            Favorite
                        </button>
                    </form>

                    @if(!auth()->user()->lists->isEmpty())
                        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">
                            <button type="button" @click="open = ! open"
                                    class="inline-flex items-center gap-2 rounded-selector border border-white/15 px-5 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] text-base-content/70 transition hover:border-primary/40 hover:text-base-content">
                                @svg('heroicon-o-rectangle-stack', 'h-4 w-4')
                                Add to list
                            </button>

                            <div x-cloak x-show="open" x-transition
                                 class="absolute left-0 top-full z-10 mt-2 w-52 overflow-hidden rounded-box border border-white/[0.08] bg-base-200 py-1.5 shadow-xl">
                                @foreach(auth()->user()->lists as $option)
                                    <form action="{{ route('lists.add', $movie->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="listId" value="{{ $option->id }}">
                                        <button type="submit"
                                                class="w-full px-4 py-2 text-left font-display text-[0.75rem] uppercase tracking-[0.08em] text-base-content/70 transition hover:bg-base-300 hover:text-base-content">
                                            {{ $option->name }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth
            </div>

            @if(auth()->check() && auth()->user()->is_admin)
                <div class="mt-6 flex items-center gap-5">
                    <a href="{{ route('movies.edit', $movie) }}"
                       class="font-display text-[0.68rem] uppercase tracking-[0.16em] text-base-content/45 transition hover:text-primary">
                        Edit movie
                    </a>
                    <form action="{{ route('movies.destroy', $movie) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this movie?')"
                                class="font-display text-[0.68rem] uppercase tracking-[0.16em] text-base-content/45 transition hover:text-error">
                            Delete movie
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============================================================
     PLOT + FACTS
     ============================================================ --}}
<section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8">
    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-section-head title="Plot" />

            <p class="max-w-3xl text-base leading-relaxed text-base-content/75">
                {{ $movie->description }}
            </p>

            @if($movie->genres->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach($movie->genres as $genre)
                        <a href="{{ route('genres.show', $genre->id) }}"
                           class="rounded-selector border border-white/[0.08] bg-base-200 px-4 py-1.5 font-display text-[0.7rem] uppercase tracking-[0.1em] text-base-content/60 transition hover:border-primary/40 hover:text-base-content">
                            {{ $genre->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if($movie->trailer_url)
                <div id="trailer" class="mt-12 scroll-mt-24">
                    <p class="mb-4 font-display text-[0.72rem] uppercase tracking-[0.22em] text-base-content/50">Trailer</p>
                    <div class="overflow-hidden rounded-box border border-white/[0.06] bg-base-200">
                        <iframe
                            src="https://www.youtube-nocookie.com/embed/{{ $movie->trailer_url }}"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            class="aspect-video w-full"
                        ></iframe>
                    </div>
                </div>
            @endif
        </div>

        <div class="h-max space-y-6 rounded-box border border-white/[0.06] bg-base-200 p-6">
            @if(isset($movie->director) && count($movie->director) > 0)
                <div>
                    <p class="font-display text-[0.68rem] uppercase tracking-[0.18em] text-base-content/45">Director</p>
                    <div class="mt-1.5 flex flex-wrap gap-x-2 gap-y-1">
                        @foreach($movie->director as $person)
                            <a href="{{ route('people.show', $person) }}"
                               class="font-display text-sm text-primary transition hover:brightness-110">
                                {{ $person->first_name }} {{ $person->last_name }}{{ !$loop->last ? ',' : '' }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($durationLabel)
                <div>
                    <p class="font-display text-[0.68rem] uppercase tracking-[0.18em] text-base-content/45">Runtime</p>
                    <p class="mt-1.5 text-sm text-base-content/80">{{ $durationLabel }}</p>
                </div>
            @endif

            @if($movie->language)
                <div>
                    <p class="font-display text-[0.68rem] uppercase tracking-[0.18em] text-base-content/45">Language</p>
                    <p class="mt-1.5 text-sm uppercase text-base-content/80">{{ $movie->language }}</p>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============================================================
     MAIN CAST
     ============================================================ --}}
@if((isset($movie->actors) && $movie->actors->isNotEmpty()) || !empty($additionalActors))
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8">
        <x-section-head title="Main cast" />

        @if($movie->actors->isNotEmpty())
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                @foreach($movie->actors->take(5) as $actor)
                    <a href="{{ route('people.show', $actor) }}" class="group relative block overflow-hidden rounded-box border border-white/[0.06] bg-base-200">
                        @if($actor->profile_path)
                            <img src="https://image.tmdb.org/t/p/w300/{{ $actor->profile_path }}"
                                 alt="{{ $actor->first_name }} {{ $actor->last_name }}"
                                 class="aspect-[2/3] w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex aspect-[2/3] w-full items-center justify-center bg-base-300">
                                @svg('heroicon-o-user', 'h-10 w-10 text-base-content/25')
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                        <p class="absolute inset-x-0 bottom-0 p-3 font-display text-[0.75rem] font-medium uppercase leading-tight tracking-[0.08em] text-base-content">
                            {{ $actor->first_name }} {{ $actor->last_name }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif

        @if(!empty($additionalActors))
            <p class="mt-6 font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/45">
                Also starring
                {{ collect($additionalActors)->map(fn($a) => trim($a['first_name'] . ' ' . $a['last_name']))->join(', ') }}
            </p>
        @endif
    </section>
@endif

{{-- ============================================================
     AUDIENCE + LATEST REVIEWS
     ============================================================ --}}
<section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8">
    <div class="grid gap-8 lg:grid-cols-3">

        <div class="h-max rounded-box border border-white/[0.06] bg-base-200 p-8 text-center">
            <p class="font-display text-[0.72rem] uppercase tracking-[0.22em] text-base-content/50">Audience</p>

            <p class="mt-4 font-display text-5xl font-semibold text-primary">
                {{ $movie->rating ? number_format($movie->rating, 1) : 'No reviews yet' }}
            </p>

            @if($movie->rating)
                @php $audienceStars = (int) round($movie->rating); @endphp
                <div class="mt-3 flex justify-center gap-1" role="img" aria-label="Rated {{ $movie->rating }} out of 5">
                    @for($i = 1; $i <= 5; $i++)
                        @svg('heroicon-s-star', 'h-4 w-4 ' . ($i <= $audienceStars ? 'text-primary' : 'text-base-content/20'))
                    @endfor
                </div>
            @endif

            <p class="mt-2 font-display text-[0.65rem] uppercase tracking-[0.14em] text-base-content/40">
                Average user rating &middot; {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
            </p>

            @auth
                <a href="#write-review"
                   class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-selector bg-primary px-6 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110">
                    @svg('heroicon-o-pencil-square', 'h-4 w-4')
                    {{ $userReview ? 'Edit your review' : 'Add review' }}
                </a>
            @endauth
        </div>

        <div class="lg:col-span-2">
            <x-section-head title="Latest reviews" :href="$reviews->isNotEmpty() ? '#write-review' : null" link-label="All reviews" />

            @forelse($reviews->take(3) as $review)
                @php $reviewStars = (int) round($review->rating); @endphp
                <article class="mb-4 rounded-box border border-white/[0.06] bg-base-200 p-5 last:mb-0">
                    <div class="flex items-start justify-between gap-4">
                        <a href="{{ route('profile.show', $review->user) }}" class="flex items-center gap-3">
                            <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                                 alt="" class="h-9 w-9 rounded-full object-cover">
                            <div>
                                <p class="font-display text-[0.75rem] font-medium uppercase tracking-[0.08em] text-base-content">{{ $review->user->name }}</p>
                                <p class="font-display text-[0.65rem] uppercase tracking-[0.1em] text-base-content/40">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                        <div class="flex shrink-0 gap-0.5" role="img" aria-label="Rated {{ $review->rating }} out of 5">
                            @for($i = 1; $i <= 5; $i++)
                                @svg('heroicon-s-star', 'h-3.5 w-3.5 ' . ($i <= $reviewStars ? 'text-primary' : 'text-base-content/20'))
                            @endfor
                        </div>
                    </div>

                    <a href="{{ route('reviews.show', $review) }}" class="mt-4 block font-display text-[0.9rem] italic leading-relaxed text-base-content/65 transition hover:text-base-content/85">
                        &ldquo;{{ \Illuminate\Support\Str::limit(strip_tags($review->description), 160) }}&rdquo;
                    </a>
                </article>
            @empty
                <div class="rounded-box border border-white/[0.06] bg-base-200 p-8 text-center">
                    <p class="text-sm text-base-content/50">No reviews yet &mdash; be the first to share your thoughts.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================
     YOU MAY ALSO LIKE
     ============================================================ --}}
@if(count($similarMovies) > 0)
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8">
        <x-section-head title="You may also like" />

        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($similarMovies as $recommendation)
                <x-movie-card :movie="$recommendation['movie']" />
            @endforeach
        </div>
    </section>
@endif

{{-- ============================================================
     WRITE A REVIEW + ALL REVIEWS
     ============================================================ --}}
<section id="write-review" class="mx-auto w-full max-w-[110rem] scroll-mt-24 px-6 py-16 sm:px-8">
    <x-create-review :movie="$movie" :reviews="$reviews" :user-review="$userReview" />
</section>

@endsection
