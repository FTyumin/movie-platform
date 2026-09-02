<x-guest-layout>

@php
    // First three top-rated posters headline the hero stack, the rest
    // fill "Trending now" below so nothing repeats back-to-back.
    $heroMovies = $movies->take(3);
    $trendingMovies = $movies->slice(3, 6)->values();

    $freshReviews = $topReviews->take(3);
@endphp

{{-- HERO — mirrors home.blade.php's authenticated hero (same headline
     treatment, same staggered poster stack), swapping the personal
     "pick up where you left off" copy/CTAs for guest-facing ones. --}}
<section class="cb-grain relative isolate flex min-h-144 items-center overflow-hidden lg:min-h-176">

    <div class="relative mx-auto w-full max-w-[110rem] px-6 py-24 sm:px-8">
        <div class="max-w-xl">
            <h1 class="cb-animate-rise cb-delay-1 font-display text-4xl font-medium uppercase leading-[1.05] tracking-[0.06em] text-base-content sm:text-5xl lg:text-6xl">
                The theater<br>is yours.
            </h1>

            <p class="cb-animate-rise cb-delay-2 mt-6 max-w-md text-base leading-relaxed text-base-content/60">
                Rate what you've seen, write what you actually thought, and get
                recommendations built from your own taste instead of a chart.
            </p>

            <div class="cb-animate-rise cb-delay-3 mt-10 flex flex-wrap items-center gap-4">
                <a href="{{ url('/register') }}"
                   class="inline-flex items-center gap-2.5 rounded-selector bg-primary px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                    @svg('heroicon-o-pencil-square', 'h-4 w-4')
                    Start reviewing
                </a>
                <a href="{{ route('movies.index') }}"
                   class="inline-flex items-center gap-2 rounded-selector border border-primary/50 px-7 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary transition hover:bg-primary/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                    Browse films
                </a>
            </div>

            <p class="cb-animate-rise cb-delay-3 mt-8 text-sm text-base-content/55">
                Already have an account?
                <a href="{{ url('/login') }}" class="font-medium text-primary transition-colors hover:brightness-110">
                    Sign in
                </a>
            </p>
        </div>
    </div>

    {{-- staggered poster stack --}}
    @if($heroMovies->isNotEmpty())
        <div class="absolute inset-y-0 right-6 z-10 hidden items-center gap-4 sm:right-10 lg:flex xl:right-16">
            @php
                $stackSizes = ['w-36 lg:w-40', 'w-40 lg:w-48', 'w-36 lg:w-40'];
                $stackOffsets = ['translate-y-4', '-translate-y-4', 'translate-y-9'];
            @endphp
            @foreach($heroMovies as $i => $movie)
                <a href="{{ route('movies.show', $movie) }}"
                   class="group/poster relative aspect-2/3 {{ $stackSizes[$i] ?? 'w-36' }} {{ $stackOffsets[$i] ?? '' }} shrink-0 overflow-hidden rounded-box border-2 border-transparent bg-base-200 shadow-[0_24px_80px_rgba(0,0,0,0.55)] transition-colors duration-300 hover:border-primary">
                    <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}" alt="{{ $movie->name }}"
                         class="h-full w-full object-cover transition-transform duration-500 group-hover/poster:scale-105">
                    <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-black via-black/10 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-3">
                        <p class="truncate font-display text-[0.75rem] font-medium uppercase tracking-[0.06em] text-base-content">{{ $movie->name }}</p>
                        @if($movie->tmdb_rating)
                            <div class="mt-1 flex items-center gap-1">
                                @svg('heroicon-s-star', 'h-2.5 w-2.5 text-primary')
                                <span class="font-mono text-[0.68rem] text-base-content/70">{{ number_format($movie->tmdb_rating, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

{{-- ============================================================
     TRENDING NOW
     ============================================================ --}}
@if($trendingMovies->isNotEmpty())
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-24 sm:px-8">
        <div class="mb-8 flex items-end justify-between gap-6">
            <div>
                <div class="flex items-baseline gap-4">
                    <h2 class="font-display text-sm font-medium uppercase tracking-[0.22em] text-base-content">Trending now</h2>
                    <span class="font-mono text-[0.65rem] uppercase tracking-[0.14em] text-base-content/30">This week</span>
                </div>
                <span class="mt-3 block h-0.75 w-14 bg-primary"></span>
            </div>
            <a href="{{ route('movies.index') }}"
               class="group inline-flex shrink-0 items-center gap-2 font-display text-[0.72rem] uppercase tracking-[0.18em] text-base-content/50 transition-colors hover:text-primary">
                View all
                @svg('heroicon-o-arrow-right', 'h-3.5 w-3.5 transition-transform group-hover:translate-x-1')
            </a>
        </div>

        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
            @foreach($trendingMovies as $movie)
                <x-movie-browse-card :movie="$movie" />
            @endforeach
        </div>
    </section>
@endif

{{-- ============================================================
     FRESH REVIEWS
     ============================================================ --}}
@if($freshReviews->isNotEmpty())
    <section class="mx-auto w-full max-w-[110rem] px-6 py-24 sm:px-8">
        <x-section-head title="Fresh reviews" :href="route('reviews')" link-label="Read all" />

        <div class="flex flex-col gap-4">
            @foreach($freshReviews as $review)
                <a href="{{ route('reviews.show', $review) }}"
                   class="group grid grid-cols-[5.5rem_1fr] gap-5 rounded-box border border-white/6 bg-base-200 p-5 transition-colors hover:border-primary/30 sm:grid-cols-[6.5rem_1fr] sm:p-6">
                    <div class="aspect-2/3 overflow-hidden rounded-field bg-base-300">
                        <img src="https://image.tmdb.org/t/p/w342/{{ $review->movie->poster_url }}" alt="{{ $review->movie->name }}"
                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>

                    <div class="flex min-w-0 flex-col">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-display text-base font-medium uppercase tracking-wider text-base-content transition-colors group-hover:text-primary sm:text-lg">
                                    {{ $review->movie->name }}
                                </p>
                                <div class="mt-1.5 flex items-center gap-2">
                                    <span class="h-5 w-5 shrink-0 overflow-hidden rounded-full ring-1 ring-white/10">
                                        <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                                             class="h-full w-full object-cover" alt="">
                                    </span>
                                    <span class="text-sm text-base-content/45">Reviewed by {{ $review->user->name }}</span>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-1.5 rounded-selector border border-primary/25 bg-primary/10 px-3 py-1.5">
                                @svg('heroicon-s-star', 'h-3.5 w-3.5 text-primary')
                                <span class="font-display text-sm font-semibold text-primary">{{ number_format($review->rating, 1) }}</span>
                                <span class="font-display text-[0.65rem] text-base-content/40">/5</span>
                            </div>
                        </div>

                        <p class="mt-3 font-medium text-base-content/85">{{ $review->title }}</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-base-content/50 line-clamp-2">
                            {{ strip_tags($review->description) }}
                        </p>

                        <div class="mt-4 flex items-center gap-5 border-t border-white/6 pt-3">
                            <span class="flex items-center gap-1.5 text-xs text-base-content/40">
                                @svg('heroicon-o-hand-thumb-up', 'h-3.5 w-3.5')
                                {{ $review->liked_by_count }} {{ Str::plural('Like', $review->liked_by_count) }}
                            </span>
                            <span class="flex items-center gap-1.5 text-xs text-base-content/40">
                                @svg('heroicon-o-chat-bubble-left-right', 'h-3.5 w-3.5')
                                {{ $review->comments_count }} {{ Str::plural('Comment', $review->comments_count) }}
                            </span>
                            <span class="ml-auto font-display text-[0.68rem] uppercase tracking-[0.14em] text-primary/70 transition-colors group-hover:text-primary">
                                Read more
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

</x-guest-layout>
