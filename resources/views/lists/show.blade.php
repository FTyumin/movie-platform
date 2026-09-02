@extends('layouts.app')

@section('title', $list->name)

@section('content')
<div class="mx-auto w-full max-w-[110rem] px-6 sm:px-8 pb-20">

    {{-- Back link --}}
    <div class="pt-2 pb-6">
        <a href="{{ route('lists.index') }}" class="inline-flex items-center gap-2 text-sm text-base-content/50 transition hover:text-primary">
            @svg('heroicon-o-arrow-left', 'h-4 w-4')
            Back to Lists
        </a>
    </div>

    @if (session('message'))
        <div class="mb-6 flex items-center gap-2 rounded-field border border-primary/30 bg-primary/10 px-4 py-3 text-sm text-primary">
            @svg('heroicon-o-check-circle', 'h-4 w-4 shrink-0')
            {{ session('message') }}
        </div>
    @endif

    {{-- List hero --}}
    <div class="rounded-box border border-white/6 bg-base-200 p-6 sm:p-8">
        <div class="flex flex-col gap-8 md:flex-row md:items-start">

            {{-- Fanned poster preview --}}
            <div class="flex shrink-0 items-center justify-center py-2 md:w-48">
                @forelse($list->movies->take(3) as $i => $movie)
                    <div @class([
                            'relative aspect-[2/3] w-20 sm:w-24 overflow-hidden rounded-lg border border-white/10 shadow-xl ring-4 ring-base-200',
                            '-ml-8' => $i > 0,
                            '-rotate-6' => $i === 0,
                            'z-10 scale-105' => $i === 1,
                            'rotate-6' => $i === 2,
                        ])>
                        <img src="https://image.tmdb.org/t/p/w342/{{ $movie->poster_url }}"
                             alt="{{ $movie->name }}" class="h-full w-full object-cover">
                    </div>
                @empty
                    <div class="flex aspect-2/3 w-24 items-center justify-center rounded-lg border border-dashed border-white/10 text-base-content/25">
                        @svg('heroicon-o-film', 'h-8 w-8')
                    </div>
                @endforelse
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <h1 class="font-display text-3xl font-medium uppercase leading-[1.05] tracking-wide text-base-content sm:text-4xl">
                        {{ $list->name }}
                    </h1>

                    @auth
                        @if(Auth::id() === $list->user_id)
                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('lists.edit', $list) }}"
                                   class="inline-flex items-center gap-2 rounded-selector border border-primary/40 bg-primary/10 px-4 py-2 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] text-primary transition hover:bg-primary/20">
                                    @svg('heroicon-o-pencil-square', 'h-3.5 w-3.5')
                                    Edit
                                </a>

                                <x-confirm-modal
                                    title="Delete list?"
                                    message="This will permanently delete the list and remove all movies from it. This action cannot be undone."
                                    :action="route('lists.destroy', $list)"
                                    method="DELETE"
                                >
                                    <x-slot name="trigger">
                                        <button
                                            class="inline-flex items-center gap-2 rounded-selector border border-error/30 bg-error/10 px-4 py-2 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] text-error transition hover:bg-error/20">
                                            @svg('heroicon-o-trash', 'h-3.5 w-3.5')
                                            Delete
                                        </button>
                                    </x-slot>
                                </x-confirm-modal>
                            </div>
                        @endif
                    @endauth
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-base-content/55">
                    <div class="flex items-center gap-2">
                        <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}"
                             alt="" class="h-6 w-6 rounded-full object-cover">
                        <a href="{{ route('profile.show', $list->user) }}" class="font-medium text-base-content transition hover:text-primary">
                            {{ $list->user->name }}
                        </a>
                    </div>
                    <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                    <span>{{ $list->created_at->format('M d, Y') }}</span>
                    <span class="h-[3px] w-[3px] flex-none rounded-full bg-base-content/25"></span>
                    <span class="flex items-center gap-1.5">
                        @svg('heroicon-o-film', 'h-3.5 w-3.5 text-primary')
                        {{ $list->movies->count() }} {{ Str::plural('movie', $list->movies->count()) }}
                    </span>
                </div>

                @if($list->description)
                    <p class="mt-4 max-w-2xl leading-relaxed text-base-content/70">
                        {{ $list->description }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Movies grid --}}
    @if($list->movies->count() > 0)
        <div class="mt-10">
            <h2 class="font-display text-[0.7rem] font-medium uppercase tracking-[0.2em] text-base-content/50">
                Movies in this list
            </h2>

            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($list->movies as $movie)
                    <div class="group relative">
                        <x-movie-browse-card :movie="$movie" />

                        @auth
                            @if(Auth::id() === $list->user_id)
                                <div class="absolute left-2 top-2 opacity-0 transition-opacity group-hover:opacity-100">
                                    <x-confirm-modal
                                        title="Remove {{ $movie->name }}?"
                                        message="This movie will be removed from your list. This action cannot be undone."
                                        :action="route('lists.remove', [$list->id, $movie->id])"
                                        method="DELETE"
                                    >
                                        <x-slot name="trigger">
                                            <button class="flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-base-content/80 transition hover:bg-error hover:text-white">
                                                @svg('heroicon-o-x-mark', 'h-3.5 w-3.5')
                                            </button>
                                        </x-slot>
                                    </x-confirm-modal>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Empty state --}}
        <div class="mt-10 flex flex-col items-center justify-center rounded-box border border-white/6 bg-base-200 py-20">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-box border border-white/8 bg-base-300">
                @svg('heroicon-o-film', 'h-12 w-12 text-base-content/30')
            </div>
            <h3 class="font-display text-xl font-medium uppercase tracking-wider text-base-content">No Movies Yet</h3>
            <p class="mt-2 max-w-md text-center text-base-content/55">
                This list is empty. Start adding your favorite movies to build your collection!
            </p>
            @auth
                @if(Auth::id() === $list->user_id)
                    <a href="{{ route('movies.index') }}"
                       class="mt-6 inline-flex items-center gap-2 rounded-selector bg-primary px-5 py-2.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                        @svg('heroicon-o-plus', 'h-4 w-4')
                        Add Movies
                    </a>
                @endif
            @endauth
        </div>
    @endif
</div>
@endsection
