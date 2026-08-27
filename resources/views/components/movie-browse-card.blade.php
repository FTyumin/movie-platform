{{-- Browse-grid poster card: rating + saved badge float over the poster,
     title/year/genre sit in a padded block below it rather than overlaid,
     so a dense grid stays readable at small sizes. --}}
@props(['movie', 'saved' => false])

<a href="{{ route('movies.show', $movie) }}"
   class="group flex flex-col gap-3 rounded-box border border-white/[0.06] bg-base-200 p-2.5 transition-colors hover:border-primary/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">

    <div class="relative aspect-[2/3] overflow-hidden rounded-field bg-base-300">
        <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
             alt="{{ $movie->name }}"
             loading="lazy"
             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />

        @if($movie->tmdb_rating)
            <div class="pointer-events-none absolute right-2 top-2 flex items-center gap-1 rounded-full bg-black/70 px-2 py-1">
                @svg('heroicon-s-star', 'h-2.5 w-2.5 text-primary')
                <span class="font-sans text-[11px] font-semibold text-base-content">{{ number_format($movie->tmdb_rating, 1) }}</span>
            </div>
        @endif

        @if($saved)
            <div class="pointer-events-none absolute left-2 top-2 flex h-[22px] w-[22px] items-center justify-center rounded-full bg-primary">
                @svg('heroicon-o-check', 'h-3 w-3 stroke-[3] text-primary-content')
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-1 px-0.5 pb-0.5">
        <h3 class="font-display text-[0.8rem] font-medium uppercase leading-tight tracking-[0.08em] text-base-content line-clamp-2">
            {{ $movie->name }}
        </h3>
        <div class="flex items-center gap-2 text-[12px] text-base-content/55">
            <span class="whitespace-nowrap">{{ $movie->year }}</span>
            @if($movie->genres->isNotEmpty())
                <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                <span class="truncate">{{ $movie->genres->pluck('name')->take(2)->join(', ') }}</span>
            @endif
        </div>
    </div>
</a>
