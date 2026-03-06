@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-neutral-900 to-black p-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-300 hover:text-white transition-colors mb-6">
        ← Back
    </a>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
        <p class="text-gray-400 mt-1">Overview of platform activity</p>
    </div>

    {{-- Top stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        {{-- Most followed user --}}
        <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-5">
            <p class="text-sm text-gray-400">Most Followed User</p>
            <a href="{{ route('profile.show',$userWithMostFollowers ) }}">
                <p class="text-xl font-semibold text-white mt-1">
                    {{ $userWithMostFollowers?->name ?? '—' }}
                </p>
            </a>
            <p class="text-gray-400 text-sm">
                {{ $userWithMostFollowers?->followers_count ?? 0 }} followers
            </p>
        </div>

        {{-- Top review --}}
        @if($topReview)
            <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-5">
                <p class="text-sm text-gray-400">Top Review</p>
                <a href="{{ route('reviews.show',$topReview ) }}">
                    <p class="text-white font-medium mt-1 line-clamp-2">
                        {{ $topReview?->title ?? '—' }}
                    </p>
                </a>
                <p class="text-gray-400 text-sm">
                    {{ $topReview?->liked_by_count ?? 0 }} likes
                </p>
            </div>
        @endif
        {{-- Suggestions --}}
        <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-5">
            <p class="text-sm text-gray-400">Pending Suggestions</p>
            <p class="text-3xl font-bold text-white mt-1">
                {{ $suggestions->count() }}
            </p>
        </div>

        {{-- Placeholder for future stat --}}
        <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-5">
            <p class="text-sm text-gray-400">System Status</p>
            <p class="text-green-400 font-semibold mt-1">Operational</p>
        </div>
    </div>

    {{-- Admin Actions --}}
    <div class="rounded-2xl bg-neutral-900/80 border border-white/5 p-6 mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Admin Actions</h2>
            <span class="text-xs text-gray-500">Manage core platform tools</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="/admin" class="group flex items-center gap-3 rounded-xl border border-white/5 bg-neutral-900/60 p-4 hover:border-yellow-500/40 hover:bg-neutral-900 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                    @svg('heroicon-o-shield-check', 'w-5 h-5 text-yellow-400')
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Admin dashboard</p>
                    <p class="text-xs text-gray-400">Overview & health</p>
                </div>
            </a>

            <a href="{{ route('movies.create') }}" class="group flex items-center gap-3 rounded-xl border border-white/5 bg-neutral-900/60 p-4 hover:border-yellow-500/40 hover:bg-neutral-900 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                    @svg('heroicon-o-pencil-square', 'w-5 h-5 text-yellow-400')
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Add movie</p>
                    <p class="text-xs text-gray-400">Create manually</p>
                </div>
            </a>

            <a href="{{ route('movies.load') }}" class="group flex items-center gap-3 rounded-xl border border-white/5 bg-neutral-900/60 p-4 hover:border-yellow-500/40 hover:bg-neutral-900 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                    @svg('tabler-movie', 'w-5 h-5 text-yellow-400')
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Load from TMDB</p>
                    <p class="text-xs text-gray-400">Import catalog</p>
                </div>
            </a>

            <a href="{{ route('admin.feed') }}" class="group flex items-center gap-3 rounded-xl border border-white/5 bg-neutral-900/60 p-4 hover:border-yellow-500/40 hover:bg-neutral-900 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                    @svg('tabler-movie', 'w-5 h-5 text-yellow-400')
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Admin feed</p>
                    <p class="text-xs text-gray-400">Moderation stream</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

        {{-- Most Favorited Movies --}}
        <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Most Favorited Movies</h2>

            <ul class="space-y-3">
                @foreach($mostFavorites as $movie)
                    <li class="flex justify-between text-gray-300">
                        <span>{{ $movie->title }}</span>
                        <span class="text-gray-400">{{ $movie->favoriters_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Most Watched Movies --}}
        <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Most Watched Movies</h2>

            <ul class="space-y-3">
                @foreach($mostWatched as $movie)
                    <li class="flex justify-between text-gray-300">
                        <span>{{ $movie->name }}</span>
                        <span class="text-gray-400">{{ $movie->watchers_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        {{-- Suggestions --}}
    <div class="rounded-xl bg-neutral-900/80 border border-white/5 p-6 w-xl">
        <h2 class="text-lg font-semibold text-white mb-4">Pending Suggestions</h2>

        @forelse($suggestions as $sug)
            <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-none">
                <div>
                    <p class="text-white font-medium">{{ $sug->title }}</p>
                    <p class="text-sm text-gray-400">Submitted by user #{{ $sug->user_id }}</p>
                </div>

                <form method="POST" action="{{ route('suggestions.approve', $sug) }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-500 text-white text-sm transition"
                    >
                        Approve
                    </button>
                </form>

                <form method="POST" action="{{ route('suggestions.reject', $sug) }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white text-sm transition"
                    >
                        Reject
                    </button>
                </form>

            </div>
        @empty
            <p class="text-gray-400">No pending suggestions 🎉</p>
        @endforelse
    </div>
    </div>



</div>
@endsection
