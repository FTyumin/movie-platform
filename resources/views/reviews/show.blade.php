@extends('layouts.app')

@section('title', $review->movie->name . ' — Review by ' . $review->user->name)

@section('content')

@php
    $movie = $review->movie;
    $posterImage = $movie->poster_url
        ? 'https://image.tmdb.org/t/p/w500/' . $movie->poster_url
        : asset('images/cinema.webp');
    $durationLabel = $movie->duration
        ? intdiv($movie->duration, 60) . 'h ' . ($movie->duration % 60) . 'm'
        : null;
    $director = $movie->director->first();
    $genre = $movie->genres->first();
    $stars = (int) round($review->rating);
    $isLiked = auth()->check() && $review->likedBy->contains(auth()->id());
    $likesCount = $review->likedBy->count();
@endphp

<div class="mx-auto w-full max-w-4xl px-6 pb-20 sm:px-8">

    <a href="{{ url()->previous() }}"
       class="mt-6 inline-flex items-center gap-1.5 font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/55 transition hover:text-primary">
        @svg('heroicon-o-arrow-left', 'h-3.5 w-3.5')
        Back to browse
    </a>

    {{-- ============================================================
         FILM HEADER
         ============================================================ --}}
    <div class="mt-6 grid grid-cols-[8rem_1fr] gap-6 sm:grid-cols-[11rem_1fr] sm:gap-8">
        <a href="{{ route('movies.show', $movie) }}" class="block">
            <img src="{{ $posterImage }}" alt="{{ $movie->name }} poster"
                 class="aspect-2/3 w-full rounded-box object-cover shadow-2xl ring-1 ring-white/10">
        </a>

        <div class="flex flex-col justify-end gap-5 pb-1">
            <div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                    @if($genre)
                        <span class="rounded-selector border border-primary/25 bg-primary/10 px-2.5 py-1 font-display text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-primary">
                            {{ $genre->name }}
                        </span>
                    @endif
                    <span class="font-display text-[0.8rem] text-base-content/50">{{ $movie->year }}</span>
                    @if($durationLabel)
                        <span class="text-base-content/25">&middot;</span>
                        <span class="font-display text-[0.8rem] text-base-content/50">{{ $durationLabel }}</span>
                    @endif
                </div>

                <a href="{{ route('movies.show', $movie) }}"
                   class="mt-2 block font-display text-3xl font-medium uppercase leading-[1.1] tracking-wider text-base-content transition hover:text-primary sm:text-4xl">
                    {{ $movie->name }}
                </a>

                @if($director)
                    <a href="{{ route('people.show', $director) }}" class="mt-1.5 block text-sm text-base-content/55 transition hover:text-primary">
                        Directed by {{ $director->first_name }} {{ $director->last_name }}
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="flex gap-0.5" role="img" aria-label="Rated {{ $review->rating }} out of 5">
                    @for($i = 1; $i <= 5; $i++)
                        @svg('heroicon-s-star', 'h-5 w-5 ' . ($i <= $stars ? 'text-primary' : 'text-base-content/20'))
                    @endfor
                </div>
                <span class="font-display text-xl font-semibold text-primary">{{ number_format($review->rating, 1) }}</span>
                <span class="font-display text-[0.75rem] text-base-content/40">/ 5</span>
            </div>
        </div>
    </div>

    {{-- ============================================================
         REVIEW CARD
         ============================================================ --}}
    <article class="mt-9 rounded-box border border-white/6 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <a href="{{ route('profile.show', $review->user) }}" class="shrink-0">
                <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                     alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-white/10">
            </a>
            <div>
                <a href="{{ route('profile.show', $review->user) }}" class="font-medium text-base-content transition hover:text-primary">
                    {{ $review->user->name }}
                </a>
                <div class="flex items-center gap-2 text-[0.8rem] text-base-content/45">
                    <time>{{ $review->created_at->format('M d, Y') }}</time>
                    <span class="h-0.5 w-0.5 rounded-full bg-base-content/25"></span>
                    <span>{{ $reviewerReviewCount }} {{ Str::plural('review', $reviewerReviewCount) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-5 border-t border-white/6 pt-5">
            <h1 class="font-medium text-base-content">{{ $review->title }}</h1>

            @if($review->spoilers)
                <div x-data="{ open: false }" class="mt-3">
                    <div x-show="!open" class="rounded-field border border-primary/25 bg-primary/10 p-4">
                        <div class="flex items-start gap-3">
                            @svg('heroicon-o-exclamation-triangle', 'h-5 w-5 shrink-0 text-primary')
                            <div class="flex-1">
                                <p class="font-medium text-primary">This review contains spoilers</p>
                                <p class="mt-1 text-sm text-base-content/60">Reveals plot points you might want to discover for yourself.</p>
                                <button type="button" @click="open = true"
                                    class="mt-3 rounded-selector bg-primary px-4 py-1.5 font-display text-[0.7rem] font-semibold uppercase tracking-[0.1em] text-primary-content transition hover:brightness-110">
                                    Show review
                                </button>
                            </div>
                        </div>
                    </div>
                    <div x-show="open" x-transition x-cloak>
                        <p class="text-[0.95rem] leading-relaxed text-base-content/80 whitespace-pre-line">{{ $review->description }}</p>
                        <button type="button" @click="open = false"
                            class="mt-4 font-display text-[0.7rem] uppercase tracking-widest text-base-content/45 hover:text-base-content">
                            Hide review
                        </button>
                    </div>
                </div>
            @else
                <p class="mt-3 text-[0.95rem] leading-relaxed text-base-content/80 whitespace-pre-line">{{ $review->description }}</p>
            @endif
        </div>

        <div class="mt-6 flex items-center gap-3">
            @auth
                <form action="{{ route('reviews.like', $review) }}" method="POST">
                    @csrf
                    <button type="submit"
                        @class([
                            'flex items-center gap-1.5 rounded-selector border px-3.5 py-2 font-display text-[0.75rem] font-medium transition',
                            'border-primary/50 bg-primary/10 text-primary' => $isLiked,
                            'border-white/10 text-base-content/60 hover:border-white/25 hover:text-base-content' => ! $isLiked,
                        ])>
                        @svg($isLiked ? 'heroicon-s-hand-thumb-up' : 'heroicon-o-hand-thumb-up', 'h-3.5 w-3.5')
                        {{ $likesCount > 0 ? $likesCount : 'Like' }}
                    </button>
                </form>
            @else
                <span class="flex items-center gap-1.5 rounded-selector border border-white/10 px-3.5 py-2 font-display text-[0.75rem] font-medium text-base-content/60">
                    @svg('heroicon-o-hand-thumb-up', 'h-3.5 w-3.5')
                    {{ $likesCount > 0 ? $likesCount : 'Like' }}
                </span>
            @endauth

            <button type="button"
                x-data="{ copied: false }"
                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                class="flex items-center gap-1.5 rounded-selector border border-white/10 px-3.5 py-2 font-display text-[0.75rem] font-medium text-base-content/60 transition hover:border-white/25 hover:text-base-content">
                @svg('heroicon-o-share', 'h-3.5 w-3.5')
                <span x-text="copied ? 'Copied!' : 'Share'"></span>
            </button>
        </div>
    </article>

    {{-- ============================================================
         COMMENTS
         ============================================================ --}}
    <div class="mt-7 overflow-hidden rounded-box border border-white/6 bg-base-200">

        <div class="flex items-center justify-between gap-4 border-b border-white/6 px-6 py-4 sm:px-8">
            <div class="flex items-center gap-2.5">
                @svg('heroicon-o-chat-bubble-left-right', 'h-5 w-5 text-primary')
                <h2 class="font-display text-[0.95rem] font-medium uppercase tracking-[0.06em] text-base-content">Comments</h2>
                <span class="rounded-selector bg-base-300 px-2.5 py-0.5 font-display text-[0.7rem] text-base-content/50">{{ $review->comments->count() }}</span>
            </div>
            <span class="font-display text-[0.65rem] uppercase tracking-[0.14em] text-base-content/40">Newest first</span>
        </div>

        @auth
            <div class="border-b border-white/6 px-6 py-6 sm:px-8">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                    <div class="flex gap-3.5">
                        <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('images/person-placeholder.png') }}"
                             alt="" class="h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-white/10">
                        <div class="flex-1">
                            <textarea name="comment" rows="3" placeholder="Share your thoughts on this review…"
                                class="w-full rounded-field border border-white/10 bg-base-100 px-3.5 py-2.5 text-sm text-base-content placeholder:text-base-content/40 transition focus:border-primary/50 focus:outline-none"
                            >{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex justify-end">
                                <button type="submit"
                                    class="rounded-selector bg-primary px-5 py-2 font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                                    Post
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="border-b border-white/6 px-6 py-6 text-center sm:px-8">
                <p class="mb-3.5 text-sm text-base-content/55">Sign in to join the conversation</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center rounded-selector bg-primary px-6 py-2.5 font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
                    Log in
                </a>
            </div>
        @endauth

        @forelse($review->comments as $comment)
            <div class="border-b border-white/5 px-6 py-5 last:border-b-0 sm:px-8">
                <div class="flex gap-3.5">
                    <img src="{{ $comment->user->image ? asset('storage/' . $comment->user->image) : asset('images/person-placeholder.png') }}"
                         alt="" class="h-8 w-8 shrink-0 rounded-full object-cover ring-1 ring-white/10">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('profile.show', $comment->user) }}" class="text-sm font-medium text-base-content transition hover:text-primary">
                                {{ $comment->user->name }}
                            </a>
                            <span class="text-[0.75rem] text-base-content/40">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1.5 text-sm leading-relaxed text-base-content/75">{{ $comment->description }}</p>

                        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin))
                            <div class="mt-2.5 flex items-center gap-4">
                                <button type="button" class="font-display text-[0.68rem] uppercase tracking-widest text-primary/80 hover:text-primary"
                                        onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')">
                                    Edit
                                </button>
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-display text-[0.68rem] uppercase tracking-widest text-error/70 hover:text-error">Delete</button>
                                </form>
                            </div>

                            <form id="edit-comment-{{ $comment->id }}" action="{{ route('comments.update', $comment) }}"
                                  method="POST" class="mt-3 hidden">
                                @csrf
                                @method('PATCH')
                                <textarea name="comment" rows="3"
                                    class="w-full rounded-field border border-white/10 bg-base-100 px-3.5 py-2.5 text-sm text-base-content focus:border-primary/50 focus:outline-none">{{ $comment->description }}</textarea>
                                <div class="mt-2 flex gap-2">
                                    <button class="rounded-selector bg-primary px-4 py-1.5 font-display text-[0.68rem] font-semibold uppercase tracking-widest text-primary-content transition hover:brightness-110">Save</button>
                                    <button type="button"
                                            class="rounded-selector border border-white/10 px-4 py-1.5 font-display text-[0.68rem] uppercase tracking-widest text-base-content/60"
                                            onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.add('hidden')">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-14 text-center sm:px-8">
                @svg('heroicon-o-chat-bubble-left-right', 'mx-auto h-9 w-9 text-base-content/20')
                <p class="mt-3 text-sm text-base-content/45">No comments yet. Be the first to share your thoughts.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
