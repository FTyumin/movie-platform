@extends('layouts.app')

@section('title', 'dashboard')

@section('content')

@php
    $initials = collect(preg_split('/\s+/', trim($user->name)))
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->join('');

    $stats = [
        ['label' => 'Watchlist', 'value' => $user->wantToWatch->count(), 'icon' => 'heroicon-o-bookmark'],
        ['label' => 'Watched', 'value' => $user->seenMovies->count(), 'icon' => 'heroicon-o-check-circle'],
        ['label' => 'Reviews', 'value' => $review_count, 'icon' => 'heroicon-o-pencil-square'],
        ['label' => 'Avg Rating', 'value' => $average_review, 'icon' => 'heroicon-s-star'],
    ];

    $collectionTabs = [
        'profile' => 'Profile',
        'watchlist' => 'Watchlist',
        'lists' => 'Your Lists',
    ];
@endphp

<div class="mx-auto max-w-6xl px-2 pb-20" x-data="{ tab: 'profile' }">

    {{-- Welcome hero --}}
    <div class="flex items-center gap-5 pt-10 pb-2">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-primary bg-base-200">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
            @else
                <span class="font-display text-xl font-semibold tracking-[0.02em] text-primary">{{ $initials }}</span>
            @endif
        </div>

        <div class="min-w-0">
            <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content sm:text-[1.75rem]">
                Welcome back, <span class="text-primary">{{ $user->name }}</span>
            </h1>

            <div class="mt-1.5 flex items-center gap-3">
                <x-user-list-modal title="Followers" :users="$user->followers->map->follower->filter()" empty-message="No followers yet.">
                    <x-slot name="trigger">
                        <span class="flex items-center gap-1.5 text-sm">
                            <span class="font-display font-semibold text-base-content">{{ $user->followers->count() }}</span>
                            <span class="text-base-content/50">Followers</span>
                        </span>
                    </x-slot>
                </x-user-list-modal>

                <span class="h-0.75 w-0.75 rounded-full bg-base-content/25"></span>

                <x-user-list-modal title="Following" :users="$user->followees->map->followee->filter()" empty-message="Not following anyone yet.">
                    <x-slot name="trigger">
                        <span class="flex items-center gap-1.5 text-sm">
                            <span class="font-display font-semibold text-base-content">{{ $user->followees->count() }}</span>
                            <span class="text-base-content/50">Following</span>
                        </span>
                    </x-slot>
                </x-user-list-modal>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 py-8 sm:grid-cols-4">
        @foreach($stats as $stat)
            <div class="flex items-center justify-between rounded-box border border-white/6 bg-base-200 p-5 transition-colors hover:border-primary/30">
                <div>
                    <p class="font-display text-[0.68rem] font-medium uppercase tracking-[0.14em] text-base-content/50">{{ $stat['label'] }}</p>
                    <p class="mt-1.5 font-display text-2xl font-semibold text-base-content">{{ $stat['value'] }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-field bg-primary/10">
                    @svg($stat['icon'], 'h-5 w-5 text-primary')
                </div>
            </div>
        @endforeach
    </div>

    {{-- Collection tabs --}}
    <div class="flex items-center gap-1 overflow-x-auto border-b border-white/[0.07]">
        @foreach($collectionTabs as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                class="shrink-0 border-b-2 px-4 py-3 font-display text-[0.75rem] font-medium uppercase tracking-widest transition-colors"
                :class="tab === '{{ $key }}' ? 'border-primary text-base-content' : 'border-transparent text-base-content/50 hover:text-base-content'">
                {{ $label }}
            </button>
        @endforeach
        <div class="flex-1"></div>
    </div>

    {{-- Watchlist tab --}}
    <div x-show="tab === 'watchlist'" x-cloak class="pt-8">
        <x-section-head title="Watchlist" :href="route('profile.watchlist', $user)" />

        @if($watchList->isEmpty())
            <p class="text-sm text-base-content/55">No titles on the watchlist yet.</p>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($watchList as $entry)
                    <x-movie-browse-card :movie="$entry->movie" :saved="true" />
                @endforeach
            </div>
        @endif
    </div>

    {{-- Your Lists tab --}}
    <div x-show="tab === 'lists'" x-cloak class="pt-8">
        <x-section-head title="Your Lists" :href="route('lists.index')" />

        @if($lists->isEmpty())
            <p class="text-sm text-base-content/55">You haven't created any lists yet.</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach($lists as $list)
                    <a href="{{ route('lists.show', $list) }}"
                       class="rounded-box border border-white/6 bg-base-200 p-5 transition-colors hover:border-primary/30">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <p class="font-display text-sm font-medium uppercase tracking-[0.04em] text-base-content">{{ $list->name }}</p>
                            <span class="shrink-0 text-xs text-base-content/45">{{ $list->movies_count }} {{ Str::plural('film', $list->movies_count) }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            @forelse($list->movies as $movie)
                                <div class="h-13.5 w-9 overflow-hidden rounded-field border border-white/8 bg-base-300">
                                    <img src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}" alt="{{ $movie->name }}" class="h-full w-full object-cover">
                                </div>
                            @empty
                                <p class="text-xs text-base-content/40">No films yet.</p>
                            @endforelse
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Profile tab --}}
    <div x-show="tab === 'profile'" x-cloak class="grid grid-cols-1 gap-6 pt-8 lg:grid-cols-[1fr_320px] lg:items-start">
        <div class="flex flex-col gap-10">

            {{-- Recent Reviews --}}
            <div>
                <x-section-head title="Recent Reviews" :href="route('profile.reviews', $user)" />

                @if($reviews->isEmpty())
                    <p class="text-sm text-base-content/55">No reviews yet.</p>
                @else
                    <div class="flex flex-col gap-3">
                        @foreach($reviews as $review)
                            <a href="{{ route('reviews.show', $review) }}"
                               class="group grid grid-cols-[44px_1fr] gap-3 rounded-box border border-white/6 bg-base-200 p-4 transition-colors hover:border-primary/30">
                                <div class="aspect-2/3 overflow-hidden rounded-field bg-base-300">
                                    <img src="https://image.tmdb.org/t/p/w500/{{ $review->movie->poster_url }}"
                                         alt="{{ $review->movie->name }}"
                                         class="h-full w-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="truncate font-display text-[0.8rem] font-medium uppercase tracking-[0.03em] text-base-content group-hover:text-primary">
                                        {{ $review->movie->name }}
                                    </h4>
                                    <div class="mt-1 flex items-center gap-0.5">
                                        @for($j = 0; $j < 5; $j++)
                                            @svg('heroicon-s-star', 'h-2.5 w-2.5 ' . ($j < round($review->rating) ? 'text-primary' : 'text-base-content/20'))
                                        @endfor
                                    </div>
                                    <p class="mt-1.5 line-clamp-2 text-[0.8rem] leading-relaxed text-base-content/55">{{ $review->description }}</p>
                                    <p class="mt-1.5 text-[0.7rem] text-base-content/35">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recently Watched --}}
            <div>
                <x-section-head title="Recently Watched" :href="route('profile.seen', $user)" />

                @if($seen->isEmpty())
                    <p class="text-sm text-base-content/55">No watched films yet.</p>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach($seen as $entry)
                            <x-movie-browse-card :movie="$entry->movie" />
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Favorites --}}
            <div>
                <x-section-head title="Favorites" :href="route('profile.favorites', $user)" />

                @if($favorites->isEmpty())
                    <p class="text-sm text-base-content/55">No favorites yet.</p>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach($favorites as $fav)
                            <x-movie-card :movie="$fav->markable" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="flex flex-col gap-5 lg:sticky lg:top-24">

            {{-- Quick Actions --}}
            <div class="rounded-box border border-white/6 bg-base-200 p-5">
                <h3 class="font-display text-sm font-medium uppercase tracking-[0.14em] text-base-content">Quick Actions</h3>
                <div class="mt-3 flex flex-col gap-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-field px-3 py-2.5 transition-colors hover:bg-base-300">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-field bg-primary/10">
                            @svg('heroicon-o-pencil-square', 'h-3.5 w-3.5 text-primary')
                        </div>
                        <span class="text-sm text-base-content/80">Edit profile</span>
                    </a>

                    <a href="/suggestion" class="flex items-center gap-3 rounded-field px-3 py-2.5 transition-colors hover:bg-base-300">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-field bg-primary/10">
                            @svg('heroicon-o-paper-airplane', 'h-3.5 w-3.5 text-primary')
                        </div>
                        <span class="text-sm text-base-content/80">Send movie suggestion</span>
                    </a>

                    @if($user->is_admin)
                        <a href="/admin" class="flex items-center gap-3 rounded-field px-3 py-2.5 transition-colors hover:bg-base-300">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-field bg-primary/10">
                                @svg('heroicon-o-shield-check', 'h-3.5 w-3.5 text-primary')
                            </div>
                            <span class="text-sm text-base-content/80">Go to admin dashboard</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-field px-3 py-2.5 text-left transition-colors hover:bg-base-300">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-field bg-primary/10">
                                @svg('heroicon-o-arrow-right-on-rectangle', 'h-3.5 w-3.5 text-primary')
                            </div>
                            <span class="text-sm text-base-content/80">Log out</span>
                        </button>
                    </form>

                    <x-confirm-modal title="Delete account?" message="Your account and all of its data will be deleted. This action cannot be undone."
                        :action="route('profile.destroy')" method="DELETE">
                        <x-slot name="trigger" class="block w-full">
                            <button type="button" class="flex w-full items-center gap-3 rounded-field px-3 py-2.5 text-left transition-colors hover:bg-base-300">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-field bg-error/10">
                                    @svg('monoicon-delete', 'h-3.5 w-3.5 text-error')
                                </div>
                                <span class="text-sm text-error/80">Delete account</span>
                            </button>
                        </x-slot>
                    </x-confirm-modal>
                </div>
            </div>

            {{-- This Month --}}
            <div class="rounded-box border border-white/6 bg-base-200 p-5">
                <h3 class="font-display text-sm font-medium uppercase tracking-[0.14em] text-base-content">This Month</h3>
                <div class="mt-3.5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-base-content/55">Films watched</span>
                        <span class="font-display text-sm font-semibold text-base-content">{{ $thisMonth['watched'] }}</span>
                    </div>
                    <div class="h-px bg-white/6"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-base-content/55">Reviews written</span>
                        <span class="font-display text-sm font-semibold text-base-content">{{ $thisMonth['reviews'] }}</span>
                    </div>
                    <div class="h-px bg-white/6"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-base-content/55">New followers</span>
                        <span class="font-display text-sm font-semibold text-base-content">+{{ $thisMonth['followers'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            @if($user->notifications->isNotEmpty())
                <div class="rounded-box border border-white/6 bg-base-200 p-5">
                    <h3 class="font-display text-sm font-medium uppercase tracking-[0.14em] text-base-content">Notifications</h3>
                    <div class="mt-3.5 flex flex-col gap-3">
                        @foreach($user->notifications as $notification)
                            <div class="rounded-field border border-white/6 bg-base-300 p-3.5">
                                <p class="text-sm text-base-content/80">{{ $notification->data['message'] }}</p>
                                <span class="mt-1 block text-[0.7rem] text-base-content/40">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
