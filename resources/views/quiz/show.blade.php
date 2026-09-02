@extends('layouts.app')

@section('title', 'Preference quiz')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-3xl">

        <a href="{{ url()->previous() }}"
           class="mb-6 inline-flex items-center gap-2 font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/55 transition-colors hover:text-primary">
            @svg('heroicon-o-arrow-left', 'h-3.5 w-3.5')
            Back
        </a>

        {{-- Header --}}
        <div class="mb-8 text-center">
            <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-adjustments-horizontal', 'h-7 w-7')
            </span>
            <h1 class="font-display text-3xl font-semibold uppercase tracking-[0.02em] text-base-content md:text-4xl">
                Tell us about your <span class="text-primary">movie preferences</span>
            </h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-base-content/60">
                Help us personalize your experience by selecting your favorite genres. We'll recommend movies you'll love.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-box border border-white/9 bg-base-200 p-6 sm:p-8" x-data="{ selected: {{ json_encode(old('genres', [])) }} }">
            <form method="POST" action="{{ route('quiz.store') }}">
                @csrf

                {{-- Pro tip --}}
                <div class="mb-8 flex items-start gap-3 rounded-box border border-primary/30 bg-primary/10 p-4">
                    @svg('heroicon-o-light-bulb', 'mt-0.5 h-5 w-5 shrink-0 text-primary')
                    <div>
                        <p class="font-display text-[0.72rem] font-semibold uppercase tracking-[0.1em] text-primary">Pro tip</p>
                        <p class="mt-1 text-sm text-base-content/70">Select at least 3 genres to get better recommendations. You can always update your preferences later.</p>
                    </div>
                </div>

                {{-- Genre selection title --}}
                <div class="mb-5">
                    <h3 class="font-display text-lg font-semibold uppercase tracking-[0.02em] text-base-content">
                        Select your favorite genres
                    </h3>
                    <p class="mt-1 text-sm text-base-content/55">Choose all the genres you enjoy watching</p>
                </div>

                {{-- Genre grid --}}
                <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($genres as $genre)
                    <label class="group relative cursor-pointer">
                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                            {{ in_array($genre->id, old('genres', [])) ? 'checked' : '' }}
                            class="peer sr-only"
                            @change="$event.target.checked ? selected.push({{ $genre->id }}) : selected = selected.filter(id => id !== {{ $genre->id }})">

                        <div class="flex h-full items-center justify-center rounded-box border border-white/9 bg-base-100 px-4 py-5 text-center transition-colors peer-checked:border-primary/60 peer-checked:bg-primary/10">
                            <span class="font-display text-xs font-semibold uppercase tracking-[0.06em] text-base-content/70 transition-colors peer-checked:text-primary">
                                {{ $genre->name }}
                            </span>
                        </div>

                        <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-content opacity-0 transition-opacity peer-checked:opacity-100" aria-hidden="true">
                            @svg('heroicon-s-check', 'h-3 w-3')
                        </span>
                    </label>
                    @endforeach
                </div>

                {{-- Selected count --}}
                <div class="mb-6 flex items-center justify-between rounded-box border border-white/9 bg-base-100 p-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-selector bg-primary/15 text-primary">
                            @svg('heroicon-o-sparkles', 'h-5 w-5')
                        </span>
                        <div>
                            <p class="text-sm font-medium text-base-content/80">Selected genres</p>
                            <p class="text-xs text-base-content/45">Minimum 3 recommended</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-display text-2xl font-semibold text-primary" x-text="selected.length"></p>
                        <p class="text-xs text-base-content/45">genres</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-3 border-t border-white/9 pt-6 sm:flex-row">
                    <button type="submit" :disabled="selected.length < 3"
                        :class="selected.length < 3 ? 'opacity-40 cursor-not-allowed' : 'hover:brightness-110'"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-selector bg-primary px-8 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        @svg('heroicon-o-check', 'h-4 w-4')
                        Save my preferences
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-selector border border-white/9 bg-base-100 px-8 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-base-content/75 transition hover:border-white/20 hover:text-base-content">
                        Skip for now
                    </a>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-base-content/45">
            You can update your preferences anytime from your profile settings.
        </p>
    </div>
</div>
@endsection
