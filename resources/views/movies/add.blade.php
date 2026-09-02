@extends('layouts.app')

@section('title', 'Add Movie')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-md shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-film', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Add movie
                </h1>
                <p class="text-sm text-base-content/55">Import a title straight from TMDB.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="mt-6 rounded-field border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('movies.store') }}" class="mt-6 space-y-5">
            @csrf

            {{-- TMDB ID --}}
            <div>
                <label for="movie_id" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    TMDB ID
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-hashtag', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input type="number" id="movie_id" name="movie_id" min="1"
                        required
                        placeholder="e.g. 100"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                </div>
                @error('movie_id')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                Add movie
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
