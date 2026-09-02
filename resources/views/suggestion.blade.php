@extends('layouts.app')

@section('title', 'Suggestion')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-md shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-light-bulb', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Suggest a movie
                </h1>
                <p class="text-sm text-base-content/55">Tell us what's missing from FilmStack.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('suggestions.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="title" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Movie title
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-film', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input type="text" id="title" name="title" minlength="3" maxlength="30"
                        value="{{ old('title') }}"
                        required
                        placeholder="e.g. The Departed"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                </div>
                @error('title')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                Submit suggestion
            </button>
        </form>
    </div>
</div>

@endsection
