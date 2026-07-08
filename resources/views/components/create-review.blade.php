@props(['movie', 'reviews', 'userReview' => null])

<div class="md:col-span-full">

    @auth
        @php($currentRating = (int) old('rating', $userReview?->rating ?? 0))
        <form
            action="{{ $userReview ? route('reviews.update', $userReview) : route('reviews.store') }}"
            method="POST" class="mt-16 mx-auto space-y-6 mb-12"
        >
            @csrf
            @if ($userReview)
                @method('PATCH')
            @endif

            <div>
                <label for="title" class="block text-sm font-medium text-white mb-1">
                    Title of your review
                </label>
                <input type="text" id="title" name="title" required
                    class="w-full rounded-lg bg-gray-900 border border-gray-700 px-4 py-2 text-white placeholder-gray-500 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500/50 transition"
                    value="{{ old('title', $userReview?->title) }}"
                    autocomplete="off"
                >
            </div>

            <h3 class="text-3xl text-white font-semibold">
                Write a Review for <span class="text-yellow-600">{{ $movie->name }}</span>
            </h3>
            <input type="hidden" name="movie_id" value="{{ $movie->id }}" autocomplete="off">

            <fieldset class="flex items-center space-x-1" x-data="{ rating: {{ $currentRating }} }" autocomplete="off">
                <legend class="sr-only">Rating</legend>
                @for ($i = 1; $i <= 5; $i++)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only" x-model="rating">
                        <svg
                            :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                            class="h-8 w-8 transition-colors hover:text-yellow-300"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.95a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.951c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.54-1.118l1.286-3.951a1 1 0 00-.364-1.118L2.98 9.377c-.783-.57-.38-1.81.588-1.81h4.153a1 1 0 00.95-.69l1.286-3.95z" />
                        </svg>
                    </label>
                @endfor
            </fieldset>
            @error('rating')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror


            <div>
                <label for="comment" class="block text-sm font-medium text-white mb-1">
                    Your Review
                </label>
                <textarea id="comment" name="comment" rows="4" autocomplete="off"
                    class="block w-full rounded-lg bg-gray-900 border border-gray-700 px-4 py-2
                        text-white placeholder-gray-500 focus:border-yellow-500
                        focus:ring-1 focus:ring-yellow-500/50 transition"
                    placeholder="Share your thoughts about this movie"
                >{{ old('comment', $userReview?->description) }}</textarea>
                @error('comment')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-row gap-3 items-center text-white">
                <input type="checkbox" name="spoilers" @checked(old('spoilers', $userReview?->spoilers))>
                <p>Contains spoilers</p>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="rounded-lg bg-yellow-500 px-6 py-2 font-medium text-gray-900 hover:bg-yellow-400 transition"
                >
                    {{ $userReview ? 'Update Review' : 'Submit Review' }}
                </button>
            </div>
        </form>

        @if ($userReview)
            <x-confirm-modal title="Delete review?"
                 message="Are you sure you want to delete this review? This action cannot be undone."
                    :action="route('reviews.destroy', $userReview)"
                    method="DELETE">
                    <x-slot name="trigger" class="w-max">
                        <button  class="rounded-lg border border-red-500/40 bg-red-500/10 px-6 py-2 text-red-400 hover:bg-red-500/20 transition">
                            <span class="text-md font-medium">Delete Review</span>     
                        </button>     
                    </x-slot>
            </x-confirm-modal>
        @endif
    @endauth

    <div class="mt-12 pt-8 border-t border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            Reviews
            <span class="text-base font-normal text-gray-400">({{ $reviews->count() }})</span>
        </h2>

        <div class="space-y-4">
            @forelse($reviews as $review)
                <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('profile.show', $review->user) }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center overflow-hidden">
                                        <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h1 class="font-medium text-xl text-white">{{ $review->title }}</h1>
                                        <p class="font-medium text-gray-200">{{ $review->user->name }}</p>
                                        <time class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</time>
                                    </div>
                                </a>
                            </div>

                            <div class="flex items-center gap-1 bg-yellow-500/10 border border-yellow-500/30 rounded-lg px-3 py-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-lg font-bold text-yellow-500">{{ $review->rating }}</span>
                                <span class="text-xs text-gray-400">/5</span>
                            </div>
                        </div>

                        @if($review->spoilers)
                            <div class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg" x-data="{ open: false }">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-500 shrink-0">
                                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-yellow-500 font-medium">
                                        This review contains spoilers
                                    </span>

                                    <button type="button" class="ml-auto px-3 py-1 bg-yellow-500 text-gray-900 text-xs font-medium rounded hover:bg-yellow-400"
                                        @click="open = !open"
                                    >
                                        <span x-text="open ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>

                                <div x-show="open" x-transition class="mt-3 pt-3 border-t border-yellow-500/20">
                                    <p class="text-gray-300 leading-relaxed">
                                        {{ $review->description }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-300 leading-relaxed">
                                {{ $review->description }}
                            </p>
                        @endif

                        @if(auth()->check() && auth()->user()->is_admin)
                            <x-confirm-modal title="Delete review?"
                                message="Delete this review and all its comments?"
                                    :action="route('reviews.destroy', $review)"
                                    method="DELETE">
                                    <x-slot name="trigger" class="w-max">
                                        <button class="text-xs text-red-400 hover:text-red-300">
                                            Delete review
                                        </button>
                                    </x-slot>
                            </x-confirm-modal>
                        @endif
                        @auth
                            <form action="{{ route('reviews.like', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-1">
                                    @if($review->likedBy->contains(auth()->id()))
                                        â¤ï¸
                                    @else
                                        ðŸ¤
                                    @endif
                                    <span class="text-white">{{ $review->likedBy->count() }}</span>
                                </button>
                            </form>
                        @else
                            <div class="flex items-center gap-1">
                                ðŸ¤
                                <span class="text-white">{{ $review->likedBy->count() }}</span>
                            </div>
                        @endauth
                    </div>

                    <div class="bg-gray-900/50 px-6 py-3 border-t border-gray-700 flex items-center justify-between">
                        <a href="{{ route('reviews.show', $review) }}" class="text-sm text-yellow-400 hover:text-yellow-300 font-medium transition">
                            View full review and comments
                        </a>

                        <span class="text-xs text-gray-400">{{ $review->comments->count() }} {{ Str::plural('comment', $review->comments->count()) }}</span>
                    </div>
                   
                </article>
            @empty
                <div class="bg-gray-800 rounded-xl p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-600 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">No Reviews Yet</h3>
                    <p class="text-gray-400">Be the first to share your thoughts about this movie!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
