
<div class="group relative rounded-box overflow-hidden bg-neutral-900
            border border-white/5 hover:border-primary/30
            transition-all duration-300">

    {{-- Poster --}}
    <a href="{{ route('movies.show', $movie->slug) }}" class="block relative">
        <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
            alt="{{ $movie->name }}"
            class="aspect-[2/3] w-full object-cover transition-transform duration-500 group-hover:scale-105"/>

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>

        {{-- Rating stamp --}}
        <div class="absolute top-3 right-3 flex items-center justify-center w-9 h-9 rounded-full bg-black/70 backdrop-blur border border-accent/40
                    font-condensed text-sm font-semibold text-accent">
                {{ $movie->tmdb_rating }}
        </div>

        {{-- Genres --}}
        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap gap-1">
            @foreach($movie->genres->take(2) as $genre)
                <span class="font-mono text-[0.65rem] uppercase tracking-wide px-2 py-0.5 rounded-field
                            bg-black/60 text-primary border border-primary/30 backdrop-blur">
                    {{ $genre->name }}
                </span>
            @endforeach
        </div>
    </a>
    {{-- Content --}}
    <div class="p-4">
        <h3 class="text-sm font-semibold text-white leading-tight line-clamp-2
                    decoration-primary underline-offset-4 group-hover:underline transition">
            {{ $movie->name }}
        </h3>

        <div class="mt-1.5 flex items-center gap-2 font-mono text-xs text-gray-500">
            <span>{{ $movie->year }}</span>
            <span class="text-primary/50">&middot;</span>
            <span>{{ $movie->duration }} min</span>
        </div>
    </div>
</div>