@extends('layouts.app')

@section('title', $userName . ' — ' . ucfirst($type))

@section('content')

@php
    $meta = match($type) {
        'favorites' => ['label' => 'Favorites', 'icon' => 'heroicon-o-heart', 'empty' => 'No favorites yet.'],
        'seen movies' => ['label' => 'Films', 'icon' => 'heroicon-o-check-circle', 'empty' => 'No watched films yet.'],
        'watchlist' => ['label' => 'Watchlist', 'icon' => 'heroicon-o-bookmark', 'empty' => 'No titles on the watchlist yet.'],
        default => ['label' => ucfirst($type), 'icon' => 'heroicon-o-film', 'empty' => 'Nothing here yet.'],
    };
@endphp

<div class="mx-auto max-w-6xl px-4 pb-20">

    <a href="{{ route('profile.show', $user) }}" class="inline-flex items-center gap-2 pt-6 text-sm text-base-content/55 transition-colors hover:text-base-content">
        @svg('heroicon-o-arrow-left', 'h-4 w-4')
        Back to profile
    </a>

    <div class="flex items-center gap-3 py-8">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-field bg-primary/10">
            @svg($meta['icon'], 'h-5 w-5 text-primary')
        </div>
        <div>
            <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">{{ $userName }}'s {{ $meta['label'] }}</h1>
            <p class="mt-1 text-sm text-base-content/55">{{ $movies->count() }} {{ Str::plural('title', $movies->count()) }}</p>
        </div>
    </div>

    @if($movies->isEmpty())
        <div class="rounded-box border border-white/6 bg-base-200 p-14 text-center">
            @svg($meta['icon'], 'mx-auto h-12 w-12 text-base-content/25')
            <h3 class="mt-4 font-display text-lg uppercase tracking-[0.08em] text-base-content">{{ $meta['empty'] }}</h3>
        </div>
    @elseif($type === 'favorites')
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach($movies as $entry)
                <x-movie-card :movie="$entry->markable" />
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach($movies as $entry)
                <x-movie-browse-card :movie="$entry->movie" :saved="$type === 'watchlist'" />
            @endforeach
        </div>
    @endif
</div>
@endsection
