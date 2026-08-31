@extends('layouts.app')

@section('title', 'Load Movies')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-md shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-cloud-arrow-down', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Load movies
                </h1>
                <p class="text-sm text-base-content/55">Bulk-import titles from TMDB into the catalog.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('movies.load.store') }}" class="mt-6 space-y-5">
            @csrf

            {{-- Movie count --}}
            <div>
                <label for="count" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Number of movies
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-hashtag', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input type="number" id="count" name="count" value="{{ old('count', 50) }}"
                        min="1" max="1000" required
                        placeholder="e.g. 100"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                </div>
                <p class="mt-2 text-xs text-base-content/45">Recommended: 20&ndash;100 per load</p>
                @error('count')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Method --}}
            <div>
                <label for="method" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Source
                </label>
                <div class="relative">
                    <select id="method" name="method"
                        class="w-full appearance-none rounded-selector border border-white/9 bg-base-100 px-4 py-3 text-sm text-base-content transition focus:border-primary/50 focus:outline-none">
                        <option value="discover">Discover (standard)</option>
                        <option value="top-rated">Top rated</option>
                        <option value="popular">Popular</option>
                        <option value="now-playing">Now playing</option>
                    </select>
                    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-base-content/40">
                        @svg('heroicon-o-chevron-down', 'h-4 w-4')
                    </span>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                Start loading
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-base-content/55">
            <a href="{{ url()->previous() }}" class="font-medium text-primary transition-colors hover:brightness-110">
                &larr; Back to admin
            </a>
        </p>
    </div>
</div>

@endsection
