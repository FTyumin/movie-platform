@extends('layouts.app')

@section('title', 'Movie Lists')

@section('content')
<div class="mx-auto w-full max-w-[110rem] px-6 sm:px-8 pb-20">

    {{-- Page header --}}
    <div class="border-b border-white/[0.07] pb-8 pt-2">
        <h1 class="font-display text-4xl font-medium uppercase leading-[1.05] tracking-wider text-base-content sm:text-5xl">
            Curated Lists
        </h1>
        <p class="mt-3 max-w-xl text-base-content/60">
            Discover hand-picked collections from the community. Find your next cinematic obsession through focused thematic journeys.
        </p>
    </div>

    {{-- Featured collections --}}
    @if($featured->isNotEmpty())
        <div class="mt-10" x-data="{ active: 0, total: {{ $featured->count() }} }">
            <div class="relative aspect-video w-full overflow-hidden rounded-box border border-white/6 bg-base-200 sm:aspect-3/1">
                @foreach($featured as $i => $list)
                    <div x-show="active === {{ $i }}" x-transition.opacity.duration.500ms
                         class="absolute inset-0">
                        @if(optional($list->movies->first())->poster_url)
                            <img src="https://image.tmdb.org/t/p/w1280/{{ $list->movies->first()->poster_url }}"
                                 class="absolute inset-0 h-full w-full object-cover" alt="">
                        @endif
                        <div class="absolute inset-0 bg-linear-to-t from-base-100 via-base-100/60 to-base-100/10"></div>

                        <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
                            <span class="inline-block rounded-selector bg-primary px-3 py-1 font-display text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-primary-content">
                                Featured Collection
                            </span>
                            <h2 class="mt-4 font-display text-3xl font-medium uppercase tracking-[0.03em] text-base-content sm:text-4xl">
                                {{ $list->name }}
                            </h2>
                            @if($list->description)
                                <p class="mt-2 max-w-xl text-sm text-base-content/70 line-clamp-2">
                                    {{ $list->description }}
                                </p>
                            @endif
                            <div class="mt-5 flex flex-wrap items-center gap-4">
                                <span class="flex items-center gap-1.5 text-sm text-base-content/60">
                                    @svg('heroicon-o-film', 'h-4 w-4 text-primary')
                                    {{ $list->movies_count }} {{ Str::plural('movie', $list->movies_count) }}
                                </span>
                                <a href="{{ route('lists.show', $list) }}"
                                   class="inline-flex items-center gap-2 rounded-selector bg-base-content px-5 py-2 font-display text-[0.75rem] font-semibold uppercase tracking-[0.12em] text-base-100 transition hover:brightness-90">
                                    View List
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($featured->count() > 1)
                    <button type="button" @click="active = (active - 1 + total) % total"
                            class="absolute left-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-base-100/60 text-base-content backdrop-blur transition hover:bg-base-100/90">
                        @svg('heroicon-o-chevron-left', 'h-5 w-5')
                    </button>
                    <button type="button" @click="active = (active + 1) % total"
                            class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-base-100/60 text-base-content backdrop-blur transition hover:bg-base-100/90">
                        @svg('heroicon-o-chevron-right', 'h-5 w-5')
                    </button>

                    <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                        @foreach($featured as $i => $list)
                            <button type="button" @click="active = {{ $i }}"
                                    :class="active === {{ $i }} ? 'w-6 bg-primary' : 'w-2 bg-white/30'"
                                    class="h-2 rounded-full transition-all"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-[280px_1fr]">

        {{-- Sidebar --}}
        <aside class="flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
            <form method="GET" action="{{ route('lists.index') }}" class="flex flex-col gap-6">
                <label for="list-search" class="sr-only">Search within lists</label>
                <div class="flex items-center gap-2 rounded-selector border border-white/8 bg-base-200 px-4 py-2.5 transition focus-within:border-primary/40">
                    @svg('heroicon-o-magnifying-glass', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input id="list-search" type="search" name="search" value="{{ $search }}"
                           placeholder="Search within lists…" autocomplete="off"
                           class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/50 focus:outline-none">
                </div>

                <div class="rounded-box border border-white/6 bg-base-200 p-5">
                    <h2 class="font-display text-[0.7rem] font-medium uppercase tracking-[0.2em] text-base-content/50">
                        Sort by
                    </h2>
                    <div class="mt-4 flex flex-col gap-3">
                        @foreach(['recent' => 'Recent Activity', 'movies' => 'Movie Count', 'name' => 'Name'] as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 text-sm text-base-content/70 transition hover:text-base-content">
                                <input type="radio" name="sort" value="{{ $value }}" {{ $sort === $value ? 'checked' : '' }}
                                       onchange="this.form.submit()" class="radio radio-sm radio-primary">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>

            @auth
                <a href="{{ route('lists.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-selector bg-primary px-5 py-3 font-display text-[0.8rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                    @svg('heroicon-o-plus', 'h-4 w-4')
                    Create New List
                </a>
            @endauth

            <div class="rounded-box border border-white/6 bg-base-200 p-5">
                <h3 class="font-display text-[0.7rem] font-medium uppercase tracking-[0.2em] text-primary">Pro Tip</h3>
                <p class="mt-2 text-sm text-base-content/60">
                    Follow your favorite curators to get notified when they add new movies to their collections.
                </p>
            </div>
        </aside>

        {{-- Lists grid --}}
        <div>
            @if($search !== '')
                <p class="mb-6 text-sm text-base-content/50">
                    {{ $lists->total() }} {{ Str::plural('result', $lists->total()) }} for &ldquo;{{ $search }}&rdquo;
                </p>
            @endif

            @if($lists->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-box border border-white/6 bg-base-200 py-20">
                    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-box border border-white/8 bg-base-300">
                        @svg('heroicon-o-list-bullet', 'h-12 w-12 text-base-content/30')
                    </div>
                    <h3 class="font-display text-xl font-medium uppercase tracking-wider text-base-content">
                        {{ $search !== '' ? 'No lists found' : 'No Lists Yet' }}
                    </h3>
                    <p class="mt-2 max-w-md text-center text-base-content/55">
                        @if($search !== '')
                            Try a different search term or clear your search.
                        @else
                            Be the first to create a movie list and share your favorite films with the community!
                        @endif
                    </p>

                    @auth
                        <a href="{{ route('lists.create') }}"
                           class="mt-6 inline-flex items-center gap-2 rounded-selector bg-primary px-5 py-2.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                            @svg('heroicon-o-plus', 'h-4 w-4')
                            Create Your First List
                        </a>
                    @endauth
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach($lists as $list)
                        <a href="{{ route('lists.show', $list) }}"
                           class="group flex h-full flex-col rounded-box border border-white/6 bg-base-200 p-6 transition-colors hover:border-primary/40">

                            {{-- Fanned poster preview --}}
                            <div class="mb-5 flex items-center justify-center py-2">
                                @forelse($list->movies as $i => $movie)
                                    <div @class([
                                            'relative aspect-[2/3] w-20 sm:w-24 overflow-hidden rounded-lg border border-white/10 shadow-xl ring-4 ring-base-200 transition-transform duration-300 group-hover:-translate-y-1',
                                            '-ml-8' => $i > 0,
                                            '-rotate-6' => $i === 0,
                                            'z-10 scale-105' => $i === 1,
                                            'rotate-6' => $i === 2,
                                        ])>
                                        <img src="https://image.tmdb.org/t/p/w342/{{ $movie->poster_url }}"
                                             alt="{{ $movie->name }}" class="h-full w-full object-cover">
                                    </div>
                                @empty
                                    <div class="flex aspect-2/3 w-20 items-center justify-center rounded-lg border border-dashed border-white/10 text-base-content/25">
                                        @svg('heroicon-o-film', 'h-6 w-6')
                                    </div>
                                @endforelse
                            </div>

                            <h3 class="font-display text-lg font-medium uppercase tracking-[0.04em] text-base-content transition-colors group-hover:text-primary line-clamp-1">
                                {{ $list->name }}
                            </h3>

                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}"
                                     alt="" class="h-5 w-5 rounded-full object-cover">
                                <span class="text-xs text-base-content/50">by {{ $list->user->name }}</span>
                            </div>

                            <p class="mt-3 grow text-sm leading-relaxed text-base-content/55 line-clamp-3">
                                {{ $list->description ?: 'No description provided' }}
                            </p>

                            <div class="mt-5 flex items-center justify-between border-t border-white/6 pt-4">
                                <span class="flex items-center gap-1.5 text-xs text-base-content/45">
                                    @svg('heroicon-o-film', 'h-3.5 w-3.5')
                                    {{ $list->movies_count }} {{ Str::plural('movie', $list->movies_count) }}
                                </span>
                                @svg('heroicon-o-arrow-right', 'h-4 w-4 text-base-content/40 transition group-hover:translate-x-1 group-hover:text-primary')
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($lists->hasPages())
                    <div class="mt-10">
                        {{ $lists->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
