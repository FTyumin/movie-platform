<article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden mt-12 border border-gray-700">
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-0">
        {{-- Poster --}}
        <div class="sm:col-span-3 bg-gray-900/50">
            <a href="{{ route('movies.show', $review->movie) }}" class="block h-full">
                <img src="https://image.tmdb.org/t/p/w500/{{ $review->movie->poster_url }}"
                    alt="{{ $review->name }}"
                    class="w-full h-full object-cover aspect-[2/3] sm:aspect-auto">
            </a>
        </div>

        {{-- Content --}}
        <div class="sm:col-span-9 flex flex-col">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show', $review->user) }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                                <img src="{{ $review->user->image ? asset('storage/' . $review->user->image) : asset('images/person-placeholder.png') }}"
                                    class="w-full h-full object-cover" alt="">
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-lg">{{ $review->title }}</h3>
                                <a href="{{ route('movies.show', $review->movie) }}" class="text-sm text-yellow-400 hover:text-yellow-300 transition-colors">
                                    {{ $review->movie->name }}
                                </a>
                                <p class="text-sm text-gray-300">{{ $review->user->name }}</p>
                                <time class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</time>
                            </div>
                        </a>
                    </div>

                    <div class="flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/30 rounded-lg px-3 py-1">
                        @svg('heroicon-s-star', 'w-4 h-4 text-yellow-500')
                        <span class="text-lg font-bold text-yellow-500">{{ $review->rating }}</span>
                        <span class="text-xs text-gray-400">/5</span>
                    </div>
                </div>

                <div class="mt-4">
                    @if($review->spoilers)
                        <div class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg" x-data="{ open: false }">
                            <div class="flex items-center gap-2">
                                @svg('heroicon-o-exclamation-triangle', 'w-5 h-5 text-yellow-500 shrink-0')
                                <span class="text-sm text-yellow-500 font-medium">This review contains spoilers</span>
                                <button type="button" class="ml-auto px-3 py-1 bg-yellow-500 text-gray-900 text-xs font-medium rounded hover:bg-yellow-400"
                                    @click="open = !open">
                                    <span x-text="open ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>

                            <div x-show="open" x-transition class="mt-3 pt-3 border-t border-yellow-500/20">
                                <p class="text-gray-300 leading-relaxed">{{ $review->description }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-300 leading-relaxed">{{ $review->description }}</p>
                    @endif
                </div>

                <div class="mt-4 flex items-center justify-between">
                    @if(auth()->check() && auth()->user()->is_admin)
                        <x-confirm-modal title="Delete review?"
                            message="Delete this review and all its comments?"
                            :action="route('reviews.destroy', $review)"
                            method="DELETE">
                            <x-slot name="trigger" class="w-max">
                                <button class="text-xs text-red-400 hover:text-red-300">Delete review</button>
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
            </div>

            <div class="mt-auto bg-gray-900/50 px-6 py-3 border-t border-gray-700 flex items-center justify-between">
                <a href="{{ route('reviews.show', $review) }}" class="text-sm text-yellow-400 hover:text-yellow-300 font-medium transition">
                    View full review and comments
                </a>
                <span class="text-xs text-gray-400">
                    {{ $review->comments->count() }} {{ Str::plural('comment', $review->comments->count()) }}
                </span>
            </div>
        </div>
    </div>
</article>
