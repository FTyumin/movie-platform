@extends('layouts.app')

@section('title', $person->first_name . ' ' . $person->last_name)

{{-- The hero runs edge-to-edge, so this page manages its own gutters. --}}
@section('main-class', '')

@section('content')

@php
    $photoUrl = $person->profile_path
        ? 'https://image.tmdb.org/t/p/w500/' . $person->profile_path
        : null;

    $directedCount = $person->moviesAsDirector->count();
    $actedCount = $person->moviesAsActor->count();

    $role = '';

    if($directedCount && $actedCount) {
        $role = 'Actor/ Director';
    } else if($directedCount) {
        $role = 'Director';
    } else if($actedCount) {
        $role = 'Actor';
    } else {
        $role = null;
    }

    $isFavorite = auth()->check() && Auth::user()->favoritePeople->pluck('id')->contains($person->id);
@endphp

{{-- ============================================================
     HERO — the profile photo thrown out of focus behind the name,
     with a crisp copy of it floating in front, mirroring the
     movie-show hero treatment.
     ============================================================ --}}
<section class="cb-grain relative isolate overflow-hidden">

    @if($photoUrl)
        <img src="{{ $photoUrl }}" alt="" aria-hidden="true"
             class="pointer-events-none absolute inset-0 h-full w-full scale-125 object-cover opacity-30 blur-3xl">
    @endif

    <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-base-100 via-base-100/70 to-base-100/20"></div>
    <div class="pointer-events-none absolute inset-0 bg-linear-to-r from-base-100/60 via-transparent to-transparent"></div>

    <a href="{{ url()->previous() }}"
       class="absolute left-5 top-5 z-10 inline-flex items-center gap-1.5 rounded-selector bg-base-100/60 px-3.5 py-2 font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/80 backdrop-blur-md transition hover:bg-base-100/90 hover:text-base-content sm:left-8 sm:top-8">
        @svg('heroicon-o-arrow-left', 'h-3.5 w-3.5')
        Back
    </a>

    <div class="relative mx-auto flex w-full max-w-[110rem] flex-col gap-8 px-6 pb-12 pt-28 sm:px-8 sm:pb-16 lg:flex-row lg:items-end">

        <div class="hidden w-44 shrink-0 overflow-hidden rounded-box shadow-2xl ring-1 ring-white/10 sm:block lg:w-56">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ $person->first_name }} {{ $person->last_name }}"
                     class="aspect-2/3 w-full object-cover">
            @else
                <div class="flex aspect-2/3 w-full items-center justify-center bg-base-200">
                    @svg('heroicon-o-user', 'h-14 w-14 text-base-content/25')
                </div>
            @endif
        </div>

        <div class="min-w-0">
            @if($role)
                <div class="inline-flex items-center gap-1.5 rounded-selector bg-base-200/80 px-3 py-1 font-display text-[0.75rem] font-medium text-primary backdrop-blur-sm">
                    {{ $role }}
                </div>
            @endif

            <h1 class="mt-4 font-display text-4xl font-medium uppercase leading-[1.05] tracking-[0.02em] text-base-content sm:text-5xl lg:text-6xl">
                {{ $person->first_name }} {{ $person->last_name }}
            </h1>

            @auth
                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <form action="{{ route('person.favorite', $person->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                @class([
                                    'inline-flex items-center gap-2 rounded-selector border px-5 py-3 font-display text-[0.72rem] font-semibold uppercase tracking-[0.12em] transition',
                                    'border-primary bg-primary/10 text-primary' => $isFavorite,
                                    'border-white/15 text-base-content/70 hover:border-primary/40 hover:text-base-content' => ! $isFavorite,
                                ])>
                            @svg($isFavorite ? 'heroicon-s-heart' : 'heroicon-o-heart', 'h-4 w-4')
                            {{ $isFavorite ? 'Favorited' : 'Favorite' }}
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</section>

{{-- ============================================================
     BIOGRAPHY
     ============================================================ --}}
@if($person->biography)
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8 {{ ! $directedCount && ! $actedCount ? 'pb-16' : '' }}">
        <x-section-head title="Biography" />
        <p class="max-w-3xl text-base leading-relaxed text-base-content/75">
            {{ $person->biography }}
        </p>
    </section>
@endif

{{-- ============================================================
     DIRECTING
     ============================================================ --}}
@if($directedCount)
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-16 sm:px-8 {{ ! $actedCount ? 'pb-16' : '' }}">
        <x-section-head title="Directing" />
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach($person->moviesAsDirector as $movie)
                <x-movie-browse-card :movie="$movie" />
            @endforeach
        </div>
    </section>
@endif

{{-- ============================================================
     FILMOGRAPHY
     ============================================================ --}}
@if($actedCount)
    <section class="mx-auto w-full max-w-[110rem] px-6 pt-16 pb-16 sm:px-8">
        <x-section-head title="Filmography" />
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach($person->moviesAsActor as $movie)
                <x-movie-browse-card :movie="$movie" />
            @endforeach
        </div>
    </section>
@endif

@endsection
