{{-- Poster card. The poster is the whole card — rating and title sit in the
     shadow at the bottom rather than in a strip below it, so a grid of these
     reads as a shelf of posters instead of a table. --}}
<a href="{{ route('movies.show', $movie) }}"
   class="group relative block overflow-hidden rounded-box border border-white/[0.06] bg-base-200 transition-colors hover:border-primary/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">

    <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
         alt="{{ $movie->name }}"
         class="aspect-[2/3] w-full object-cover transition-transform duration-500 group-hover:scale-105"/>

    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/25 to-transparent"></div>

    <div class="absolute inset-x-0 bottom-0 p-4">
        @if($movie->tmdb_rating)
            <div class="flex items-center gap-1.5 font-display text-[0.72rem] font-medium tracking-wide text-primary">
                @svg('heroicon-s-star', 'h-3 w-3')
                {{ $movie->tmdb_rating }}
            </div>
        @endif

        <h3 class="mt-1.5 font-display text-[0.8rem] font-medium uppercase leading-tight tracking-[0.1em] text-base-content line-clamp-2">
            {{ $movie->name }}
        </h3>
    </div>
</a>
