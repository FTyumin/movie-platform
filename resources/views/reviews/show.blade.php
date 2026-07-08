@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-300 hover:text-white transition-colors mb-4">
        â† Back
    </a>
    {{-- Review Card --}}
    <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        {{-- Review Header --}}
        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700">
            <div class="flex items-start gap-4">
                <a href="{{ route('movies.show', $review->movie) }}" class="block w-20 sm:w-24">
                    @if($review->movie && $review->movie->poster_url)
                        <img src="https://image.tmdb.org/t/p/w500/{{ $review->movie->poster_url }}"
                             alt="{{ $review->movie->name }} poster"
                             class="w-full rounded-lg shadow-md object-cover">
                    @else
                        <div class="aspect-[2/3] w-full rounded-lg bg-gray-700/60"></div>
                    @endif
                </a>
                <div class="flex-1">
                    <a href="{{ route('movies.show', $review->movie->slug) }}" class="text-2xl font-bold text-white hover:text-yellow-400 transition-colors">
                        {{ $review->movie->name }}
                    </a>
                    <div class="flex items-center gap-3 mt-2">
                        <a href="{{ route('profile.show', $review->user) }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-gray-300 font-medium">{{ $review->user->name }}</span>
                        </a>
                        <span class="text-gray-500">â€¢</span>
                        <time class="text-sm text-gray-400">{{ $review->created_at->format('M d, Y') }}</time>
                    </div>
                </div>
                
                {{-- Rating Badge --}}
                <div class="flex items-center gap-1 mb-2 ml-auto">
                    @for ($j = 0; $j < $review->rating; $j++)
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    @for ($j = $review->rating; $j < 5; $j++)
                        <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Review Content --}}
        <div class="px-6 py-6">
            @if($review->spoilers)
                <div x-data="{ showSpoiler: false }">
                    {{-- Spoiler Warning --}}
                    <div x-show="!showSpoiler" class="p-4 bg-yellow-500/10 border-2 border-yellow-500/30 rounded-lg">
                        <div class="flex items-start gap-3">
                            @svg('heroicon-o-exclamation-triangle', 'w-5 h-5 text-yellow-500 shrink-0')
                            <div class="flex-1">
                                <h3 class="font-semibold text-yellow-500 mb-1">Spoiler Warning</h3>
                                <p class="text-sm text-gray-300 mb-3">This review contains spoilers that may reveal important plot points.</p>
                                <button type="button" @click="showSpoiler = true"
                                    class="px-4 py-2 bg-yellow-500 text-gray-900 text-sm font-medium rounded-lg hover:bg-yellow-400 transition-colors"
                                >
                                    Show Review
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden Spoiler Content --}}
                    <div x-show="showSpoiler">
                        <div class="prose prose-invert max-w-none">
                            <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ $review->description }}</p>
                        </div>
                        <button type="button" @click="showSpoiler = false"
                            class="mt-4 px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Hide Review
                        </button>
                    </div>
                </div>
            @else
                <div class="prose prose-invert max-w-none">
                    <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ $review->description }}</p>
                </div>
            @endif
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
        </div>

    </article>

    {{-- Comments Section --}}
    <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-yellow-500">
                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97Z" clip-rule="evenodd" />
                </svg>
                Comments
                <span class="text-sm font-normal text-gray-400">({{ $review->comments->count() }})</span>
            </h2>
        </div>

        {{-- Comments List --}}
        @if($review->comments->count() > 0)
        <div class="divide-y divide-gray-700">
            @foreach($review->comments as $comment)
            <div class="px-6 py-4 hover:bg-gray-700/30 transition-colors">
                <div class="flex gap-3">
                    <div class="shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr($comment->user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('profile.show', $comment->user) }}" class="font-medium text-white hover:text-yellow-400 transition-colors">
                                {{ $comment->user->name }}
                            </a>
                            <span class="text-gray-500">â€¢</span>
                            <time class="text-sm text-gray-400">{{ $comment->created_at->diffForHumans() }}</time>
                        </div>
                        <p class="text-gray-300 leading-relaxed">{{ $comment->description }}</p>
                          @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin))
                            <div class="mt-2 flex gap-3 text-sm">
                                <button type="button" class="text-yellow-400 hover:text-yellow-300"
                                        onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')">
                                    Edit
                                </button>

                                <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-400 hover:text-red-300">Delete</button>
                                </form>
                            </div>

                        <form id="edit-comment-{{ $comment->id }}" action="{{ route('comments.update', $comment) }}"
                            method="POST" class="mt-3 hidden">
                            @csrf
                            @method('PATCH')
                            <textarea name="comment" rows="3"
                                        class="w-full rounded-lg bg-gray-800 border-gray-700 text-white">{{ $comment->description }}</textarea>
                            <div class="mt-2 flex gap-2">
                                <button class="px-3 py-1 rounded bg-yellow-500 text-gray-900 hover:bg-yellow-400 transition-colors">Save</button>
                                <button type="button"
                                        class="px-3 py-1 rounded bg-gray-700 text-white"
                                        onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.add('hidden')">
                                Cancel
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                        
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-600 mx-auto mb-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
            </svg>
            <p class="text-gray-400">No comments yet. Be the first to comment!</p>
        </div>
        @endif

        {{-- Add Comment Form --}}
        @auth
        <div class="bg-gray-900/30 px-6 py-6 border-t border-gray-700">
            <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="review_id" value="{{ $review->id }}">

                @if (session('warning'))
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <p class="text-red-400 text-sm">{{ session('warning') }}</p>
                </div>
                @endif

                @if (session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                    <p class="text-green-400 text-sm">{{ session('success') }}</p>
                </div>
                @endif

                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-300 mb-2">
                        Add your comment
                    </label>
                    <textarea id="comment" name="comment" rows="4"
                        class="block w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400
                               focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition
                               disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Share your thoughts about this review..."
                    >{{ old('comment') }}</textarea>
                    @error('comment')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="px-6 py-3 bg-yellow-500 text-gray-900 font-medium rounded-lg hover:bg-yellow-400 
                           focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-800
                           transition-colors"
                >
                    Post Comment
                </button>
            </form>
        </div>
        @else
        <div class="bg-gray-900/30 px-6 py-6 border-t border-gray-700 text-center">
            <p class="text-gray-400 mb-3">You must be logged in to comment</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-yellow-500 text-gray-900 font-medium rounded-lg hover:bg-yellow-400 transition-colors">
                Log In
            </a>
        </div>
        @endauth
    </div>
</div>

@endsection
