@props(['movie', 'reviews', 'userReview' => null])

<div>
    @auth
        @php
            $currentRating = (int) old('rating', $userReview?->rating ?? 0);
        @endphp
        <x-section-head :title="$userReview ? 'Edit your review' : 'Write a review'" />

        <form
            action="{{ $userReview ? route('reviews.update', $userReview) : route('reviews.store') }}"
            method="POST" class="max-w-2xl space-y-5 rounded-box border border-white/[0.06] bg-base-200 p-6 sm:p-8"
        >
            @csrf
            @if ($userReview)
                @method('PATCH')
            @endif

            <input type="hidden" name="movie_id" value="{{ $movie->id }}" autocomplete="off">

            <div>
                <label for="title" class="mb-1.5 block font-display text-[0.7rem] uppercase tracking-[0.16em] text-base-content/50">
                    Title of your review
                </label>
                <input type="text" id="title" name="title" required autocomplete="off"
                    value="{{ old('title', $userReview?->title) }}"
                    class="w-full rounded-field border border-white/[0.08] bg-base-100 px-4 py-2.5 text-sm text-base-content placeholder:text-base-content/35 focus:border-primary/50 focus:outline-none">
                @error('title')
                    <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p class="mb-1.5 font-display text-[0.7rem] uppercase tracking-[0.16em] text-base-content/50">Rating</p>
                <fieldset class="flex items-center gap-1" x-data="{ rating: {{ $currentRating }}, hover: 0 }" @mouseleave="hover = 0" autocomplete="off">
                    <legend class="sr-only">Rating</legend>
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="relative cursor-pointer" @mouseenter="hover = {{ $i }}">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" x-model="rating">
                            <svg :class="(hover || rating) >= {{ $i }} ? 'text-primary' : 'text-base-content/20'"
                                 class="h-8 w-8 transition-colors"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.95a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.951c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.54-1.118l1.286-3.951a1 1 0 00-.364-1.118L2.98 9.377c-.783-.57-.38-1.81.588-1.81h4.153a1 1 0 00.95-.69l1.286-3.95z" />
                            </svg>
                        </label>
                    @endfor
                </fieldset>
                @error('rating')
                    <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="comment" class="mb-1.5 block font-display text-[0.7rem] uppercase tracking-[0.16em] text-base-content/50">
                    Your review
                </label>
                <textarea id="comment" name="comment" rows="4" autocomplete="off"
                    placeholder="Share your thoughts about this movie"
                    class="w-full rounded-field border border-white/[0.08] bg-base-100 px-4 py-2.5 text-sm text-base-content placeholder:text-base-content/35 focus:border-primary/50 focus:outline-none">{{ old('comment', $userReview?->description) }}</textarea>
                @error('comment')
                    <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2.5 text-sm text-base-content/70">
                <input type="checkbox" name="spoilers" @checked(old('spoilers', $userReview?->spoilers))
                       class="h-4 w-4 rounded border-white/20 bg-base-100 text-primary focus:ring-primary/50">
                Contains spoilers
            </label>

            <div class="flex flex-wrap items-center gap-4 pt-1">
                <button type="submit"
                    class="rounded-selector bg-primary px-6 py-2.5 font-display text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110">
                    {{ $userReview ? 'Update review' : 'Submit review' }}
                </button>

                @if ($userReview)
                    <x-confirm-modal title="Delete review?"
                        message="Are you sure you want to delete this review? This action cannot be undone."
                        :action="route('reviews.destroy', $userReview)"
                        method="DELETE">
                        <x-slot name="trigger">
                            <span class="cursor-pointer font-display text-[0.72rem] uppercase tracking-[0.14em] text-error/80 transition hover:text-error">
                                Delete review
                            </span>
                        </x-slot>
                    </x-confirm-modal>
                @endif
            </div>
        </form>
    @endauth

    <div class="mt-16">
        <x-section-head title="All reviews ({{ $reviews->count() }})" />

        <div class="space-y-4">
            @forelse($reviews as $review)
                @php $stars = (int) round($review->rating); @endphp
                <article class="rounded-box border border-white/[0.06] bg-base-200 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <a href="{{ route('profile.show', $review->user) }}" class="flex items-center gap-3 transition hover:opacity-80">
                            <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                                 alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">
                            <div>
                                <h3 class="font-display text-[0.85rem] font-medium uppercase tracking-[0.06em] text-base-content">{{ $review->title }}</h3>
                                <p class="text-sm text-base-content/60">{{ $review->user->name }} &middot; <time class="text-base-content/40">{{ $review->created_at->diffForHumans() }}</time></p>
                            </div>
                        </a>

                        <div class="flex shrink-0 items-center gap-1 rounded-selector border border-primary/25 bg-primary/10 px-3 py-1">
                            @svg('heroicon-s-star', 'h-3.5 w-3.5 text-primary')
                            <span class="font-display text-sm font-semibold text-primary">{{ $review->rating }}</span>
                            <span class="text-xs text-base-content/40">/5</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if($review->spoilers)
                            <div class="rounded-field border border-primary/25 bg-primary/10 p-3" x-data="{ open: false }">
                                <div class="flex items-center gap-2">
                                    @svg('heroicon-o-exclamation-triangle', 'h-5 w-5 shrink-0 text-primary')
                                    <span class="font-display text-[0.72rem] uppercase tracking-[0.1em] text-primary">This review contains spoilers</span>
                                    <button type="button" @click="open = ! open"
                                            class="ml-auto rounded-selector bg-primary px-3 py-1 font-display text-[0.65rem] font-semibold uppercase tracking-[0.1em] text-primary-content transition hover:brightness-110">
                                        <span x-text="open ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                <div x-show="open" x-transition class="mt-3 border-t border-primary/20 pt-3">
                                    <p class="text-sm leading-relaxed text-base-content/75">{{ $review->description }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm leading-relaxed text-base-content/75">{{ $review->description }}</p>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-between gap-4 border-t border-white/[0.06] pt-4">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('reviews.show', $review) }}"
                               class="font-display text-[0.7rem] uppercase tracking-[0.14em] text-primary/80 transition hover:text-primary">
                                View full review
                            </a>
                            <span class="text-xs text-base-content/40">
                                {{ $review->comments->count() }} {{ Str::plural('comment', $review->comments->count()) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            @auth
                                <form action="{{ route('reviews.like', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-1.5 text-base-content/50 transition hover:text-primary">
                                        @svg($review->likedBy->contains(auth()->id()) ? 'heroicon-s-heart' : 'heroicon-o-heart', 'h-4 w-4 ' . ($review->likedBy->contains(auth()->id()) ? 'text-primary' : ''))
                                        <span class="text-xs">{{ $review->likedBy->count() }}</span>
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-1.5 text-base-content/40">
                                    @svg('heroicon-o-heart', 'h-4 w-4')
                                    <span class="text-xs">{{ $review->likedBy->count() }}</span>
                                </div>
                            @endauth

                            @if(auth()->check() && auth()->user()->is_admin)
                                <x-confirm-modal title="Delete review?"
                                    message="Delete this review and all its comments?"
                                    :action="route('reviews.destroy', $review)"
                                    method="DELETE">
                                    <x-slot name="trigger">
                                        <span class="cursor-pointer text-xs text-error/70 transition hover:text-error">Delete</span>
                                    </x-slot>
                                </x-confirm-modal>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-box border border-white/[0.06] bg-base-200 p-12 text-center">
                    @svg('heroicon-o-chat-bubble-left-right', 'mx-auto h-12 w-12 text-base-content/20')
                    <h3 class="mt-4 font-display text-sm uppercase tracking-[0.1em] text-base-content">No reviews yet</h3>
                    <p class="mt-1.5 text-sm text-base-content/50">Be the first to share your thoughts about this movie.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
