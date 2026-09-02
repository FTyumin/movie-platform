@extends('layouts.app')

@section('title', $user->name)

@section('content')

@php
    $isFollowing = auth()->check() && auth()->user()->followees()->where('followee_id', $user->id)->exists();
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

    $tabs = [
        'reviews' => 'Reviews',
        'watchlist' => 'Watchlist',
        'films' => 'Films',
        'favorites' => 'Favorites',
    ];
@endphp

<div class="mx-auto max-w-5xl px-2 pb-20" x-data="{ tab: 'reviews' }">

    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 pt-6 text-sm text-base-content/55 transition-colors hover:text-base-content">
        @svg('heroicon-o-arrow-left', 'h-4 w-4')
        Back
    </a>

    {{-- Profile hero --}}
    <div class="flex flex-col gap-6 border-b border-white/8 py-8 sm:flex-row sm:items-end">
        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-primary bg-base-200 shadow-[0_0_0_5px_rgba(255,201,28,0.08)]">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
            @else
                <span class="font-display text-3xl font-semibold tracking-[0.02em] text-primary">{{ $initials }}</span>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <h1 class="font-display text-3xl font-semibold uppercase tracking-[0.02em] text-base-content">{{ $user->name }}</h1>
            <p class="mt-1.5 text-sm text-base-content/55">Joined {{ $user->created_at->format('F Y') }}</p>

            <div class="mt-3.5 flex items-center gap-4">
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

        <div class="flex shrink-0 items-center gap-2">
            @auth
                @if(auth()->id() !== $user->id)
                    <div x-data="{
                            following: {{ $isFollowing ? 'true' : 'false' }},
                            loading: false,
                            async toggle() {
                                this.loading = true;
                                const method = this.following ? 'DELETE' : 'POST';
                                const endpoint = `/api/users/{{ $user->id }}/${this.following ? 'unfollow' : 'follow'}`;
                                try {
                                    const res = await fetch(endpoint, {
                                        method,
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await res.json();
                                    if (res.ok) this.following = data.following;
                                } catch (e) {
                                    console.error(e);
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }">
                        <button @click="toggle()" :disabled="loading"
                            class="rounded-selector bg-primary px-6 py-2.5 font-display text-[0.75rem] font-semibold uppercase tracking-widest text-primary-content transition hover:brightness-110 disabled:opacity-60"
                            x-text="loading ? '…' : (following ? 'Unfollow' : 'Follow')">
                        </button>
                    </div>
                @endif
            @endauth

            <div class="relative" x-data="{ open: false }">
                <button type="button" aria-label="More options" @click="open = !open" @click.outside="open = false"
                    class="rounded-selector border border-white/9 bg-base-200 p-2.5 text-base-content/60 transition hover:border-white/20 hover:text-base-content">
                    @svg('heroicon-o-ellipsis-horizontal', 'h-5 w-5')
                </button>

                <div x-cloak x-show="open" x-transition
                    class="absolute right-0 top-[calc(100%+6px)] z-10 w-52 overflow-hidden rounded-box border border-white/9 bg-base-200 shadow-xl">
                    <button type="button"
                        @click="navigator.clipboard.writeText(window.location.href).catch(() => {}); open = false"
                        class="flex w-full items-center gap-2.5 px-4 py-3 text-left text-sm text-base-content/80 transition hover:bg-base-300">
                        @svg('heroicon-o-link', 'h-4 w-4 text-base-content/45')
                        Copy profile link
                    </button>
                </div>
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

    {{-- Tabs --}}
    <div class="flex items-center gap-1 overflow-x-auto border-b border-white/[0.07]">
        @foreach($tabs as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                class="shrink-0 border-b-2 px-4 py-3 font-display text-[0.75rem] font-medium uppercase tracking-widest transition-colors"
                :class="tab === '{{ $key }}' ? 'border-primary text-base-content' : 'border-transparent text-base-content/50 hover:text-base-content'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Reviews --}}
    <div x-show="tab === 'reviews'" x-cloak class="flex flex-col gap-4 pt-8">
        <x-section-head title="Reviews" :href="route('profile.reviews', $user)" />

        @forelse($reviews as $review)
            <x-review :review="$review" />
        @empty
            <p class="text-sm text-base-content/55">No reviews yet.</p>
        @endforelse
    </div>

    {{-- Watchlist --}}
    <div x-show="tab === 'watchlist'" x-cloak class="flex flex-col gap-4 pt-8">
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

    {{-- Films --}}
    <div x-show="tab === 'films'" x-cloak class="flex flex-col gap-4 pt-8">
        <x-section-head title="Films" :href="route('profile.seen', $user)" />

        @if($seen->isEmpty())
            <p class="text-sm text-base-content/55">No watched films yet.</p>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($seen as $entry)
                    <x-movie-browse-card :movie="$entry->movie" />
                @endforeach
            </div>
        @endif
    </div>

    {{-- Favorites --}}
    <div x-show="tab === 'favorites'" x-cloak class="flex flex-col gap-4 pt-8">
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
@endsection
