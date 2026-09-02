@extends('layouts.app')

@section('title', $user->name . ' — Reviews')

@section('content')
<div class="mx-auto max-w-3xl px-4 pb-20">

    <a href="{{ route('profile.show', $user) }}" class="inline-flex items-center gap-2 pt-6 text-sm text-base-content/55 transition-colors hover:text-base-content">
        @svg('heroicon-o-arrow-left', 'h-4 w-4')
        Back to profile
    </a>

    <div class="flex items-center gap-3 py-8">
        <span class="h-11 w-11 shrink-0 overflow-hidden rounded-full ring-1 ring-white/10">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/person-placeholder.png') }}"
                 class="h-full w-full object-cover" alt="">
        </span>
        <div>
            <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">{{ $user->name }}'s reviews</h1>
            <p class="mt-1 text-sm text-base-content/55">{{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}</p>
        </div>
    </div>

    <div class="flex flex-col gap-4">
        @forelse($reviews as $review)
            <x-review :review="$review" />
        @empty
            <div class="rounded-box border border-white/6 bg-base-200 p-14 text-center">
                @svg('heroicon-o-chat-bubble-left-right', 'mx-auto h-12 w-12 text-base-content/25')
                <h3 class="mt-4 font-display text-lg uppercase tracking-[0.08em] text-base-content">No reviews yet</h3>
                <p class="mt-2 text-base-content/55">Be the first to share your thoughts on a movie.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection