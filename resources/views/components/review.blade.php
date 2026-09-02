@props(['review'])

@php
    $likesCount = $review->liked_by_count ?? $review->likedBy->count();
    $commentsCount = $review->comments_count ?? $review->comments->count();
@endphp

<article class="group rounded-box border border-white/[0.06] bg-base-200 transition-colors hover:border-primary/25">
    <div class="flex flex-col gap-5 p-5 sm:flex-row sm:p-6">

        {{-- Still --}}
        <a href="{{ route('movies.show', $review->movie) }}" class="block h-40 w-full shrink-0 overflow-hidden rounded-field bg-base-300 sm:h-auto sm:w-32">
            <img src="https://image.tmdb.org/t/p/w500/{{ $review->movie->poster_url }}"
                 alt="{{ $review->movie->name }}"
                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        </a>

        {{-- Body --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <a href="{{ route('movies.show', $review->movie) }}"
                       class="font-display text-lg font-medium uppercase tracking-[0.04em] text-base-content transition-colors hover:text-primary sm:text-xl">
                        {{ $review->movie->name }}
                    </a>
                    <a href="{{ route('profile.show', $review->user) }}" class="mt-1.5 flex items-center gap-2 w-max">
                        <span class="h-5 w-5 shrink-0 overflow-hidden rounded-full ring-1 ring-white/10">
                            <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                                 class="h-full w-full object-cover" alt="">
                        </span>
                        <span class="text-sm text-base-content/55 transition-colors hover:text-base-content">
                            Reviewed by {{ $review->user->name }}
                        </span>
                    </a>
                </div>

                <div class="flex shrink-0 items-center gap-1.5 rounded-selector border border-primary/25 bg-primary/10 px-3 py-1.5">
                    @svg('heroicon-s-star', 'h-3.5 w-3.5 text-primary')
                    <span class="font-display text-sm font-semibold text-primary">{{ number_format($review->rating, 1) }}</span>
                    <span class="font-display text-[0.65rem] text-base-content/40">/5</span>
                </div>
            </div>

            <h3 class="mt-3 font-medium text-base-content">{{ $review->title }}</h3>

            @if($review->spoilers)
                <div class="mt-3 rounded-field border border-primary/25 bg-primary/10 p-3" x-data="{ open: false }">
                    <div class="flex items-center gap-2">
                        @svg('heroicon-o-exclamation-triangle', 'h-4 w-4 shrink-0 text-primary')
                        <span class="text-sm font-medium text-primary">This review contains spoilers</span>
                        <button type="button"
                            class="ml-auto rounded-selector bg-primary px-3 py-1 font-display text-[0.65rem] font-semibold uppercase tracking-[0.1em] text-primary-content transition hover:brightness-110"
                            @click="open = !open">
                            <span x-text="open ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>

                    <p x-show="open" x-transition class="mt-3 border-t border-primary/15 pt-3 text-sm leading-relaxed text-base-content/65">
                        {{ $review->description }}
                    </p>
                </div>
            @else
                <p class="mt-2 text-sm leading-relaxed text-base-content/60 line-clamp-2">
                    {{ $review->description }}
                </p>
            @endif

            <div class="mt-4 flex items-center gap-5 border-t border-white/[0.06] pt-4">
                @auth
                    <form action="{{ route('reviews.like', $review) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 text-sm text-base-content/55 transition-colors hover:text-primary">
                            @svg($review->likedBy->contains(auth()->id()) ? 'heroicon-s-hand-thumb-up' : 'heroicon-o-hand-thumb-up', 'h-4 w-4')
                            {{ $likesCount }} {{ Str::plural('Like', $likesCount) }}
                        </button>
                    </form>
                @else
                    <span class="flex items-center gap-1.5 text-sm text-base-content/55">
                        @svg('heroicon-o-hand-thumb-up', 'h-4 w-4')
                        {{ $likesCount }} {{ Str::plural('Like', $likesCount) }}
                    </span>
                @endauth

                <a href="{{ route('reviews.show', $review) }}" class="flex items-center gap-1.5 text-sm text-base-content/55 transition-colors hover:text-primary">
                    @svg('heroicon-o-chat-bubble-left-right', 'h-4 w-4')
                    {{ $commentsCount }} {{ Str::plural('Comment', $commentsCount) }}
                </a>

                <a href="{{ route('reviews.show', $review) }}"
                   class="ml-auto font-display text-[0.7rem] uppercase tracking-[0.14em] text-primary/80 transition hover:text-primary">
                    Read more
                </a>

                @if(auth()->check() && auth()->user()->is_admin)
                    <x-confirm-modal title="Delete review?"
                        message="Delete this review and all its comments?"
                        :action="route('reviews.destroy', $review)"
                        method="DELETE">
                        <x-slot name="trigger" class="w-max">
                            <button class="font-display text-[0.65rem] uppercase tracking-[0.1em] text-error/70 hover:text-error">Delete</button>
                        </x-slot>
                    </x-confirm-modal>
                @endif
            </div>
        </div>
    </div>
</article>
