@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl text-white font-bold mb-6">Your Feed</h1>
        
        <div class="space-y-6">
            @forelse($activities as $post)
                @if($post->activityable_type == 'App\Models\Review' && $post->activityable)

                    <x-review  :review="$post->activityable" />

                  @elseif($post->activityable_type == 'App\Models\MovieList')
                    <article class="flex items-center gap-4 rounded-box border border-white/6 bg-base-200 p-5 transition-colors hover:border-primary/30 sm:p-6">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/10 text-primary">
                            @svg('heroicon-o-rectangle-stack', 'h-5 w-5')
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-base-content/85">
                                <a href="{{ route('profile.show', $post->user) }}" class="font-medium text-base-content transition-colors hover:text-primary">{{ $post->user->name }}</a>
                                created a list
                                @if($post->activityable)
                                    <span class="font-medium text-primary">{{ $post->activityable->name }}</span>
                                @endif
                            </p>
                            <time class="mt-1 block text-xs text-base-content/40">{{ $post->created_at->diffForHumans() }}</time>
                        </div>

                        @if($post->activityable)
                            <a href="{{ route('lists.show', $post->activityable->id) }}"
                               class="shrink-0 rounded-selector border border-primary/50 px-4 py-2 font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-primary transition hover:bg-primary/10">
                                View list
                            </a>
                        @endif
                    </article>

                @elseif($post->activityable_type == 'Maize\Markable\Models\Favorite')
                    <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="block">
                            <div class="flex gap-4 p-6">
                                {{-- Movie Poster --}}
                                <div class="shrink-0">
                                    @if($post->activityable && $post->activityable->movie)
                                    <a href="{{ route('movies.show', $post->activityable->movie->slug) }}">
                                        <img src="https://image.tmdb.org/t/p/w200/{{ $post->activityable->movie->poster_url }}"
                                        alt="movie poster" 
                                        class="w-24 h-36 object-cover rounded-lg shadow-md">
                                    </a>
                                    @endif
                                </div>

                                {{-- Card Content --}}  
                                    <div class="flex-1 min-w-0">
                                        {{-- Card Header --}}
                                        <div class="flex items-start justify-between gap-4 mb-4">
                                            <div class="flex-1">
                                                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                                                    <span class="text-yellow-400 cursor-pointer">
                                                        {{$post->user->name}}
                                                    </span>
                                                    <span class="text-white font-normal">favorited</span>
                                                    @if($post->activityable && $post->activityable->movie)
                                                        <span class="text-yellow-400 cursor-pointer">{{ $post->activityable->movie->name }}</span>
                                                    @endif
                                                </h2>
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <a href="{{ route('profile.show', $post->user) }}" 
                                                    onclick="event.stopPropagation()"
                                                    class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center">
                                                            <!-- <span class="text-white text-sm font-semibold">{{ substr($post->user->name, 0, 1) }}</span> -->
                                                            <img src="{{ $post->user->image ? asset('storage/' . $post->user->image) : asset('images/person-placeholder.png') }}" alt="" class="w-full h-full object-cover">
                                                        </div>
                                                        <span class="text-gray-300 text-sm font-medium">{{ $post->user->name }}</span>
                                                    </a>
                                                    <span class="text-gray-500">â€¢</span>
                                                    <time class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</time>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </article>

                @elseif($post->activityable_type == 'App\Models\UserRelationship')
                    <article class="flex items-center gap-4 rounded-box border border-white/6 bg-base-200 p-5 transition-colors hover:border-primary/30 sm:p-6">
                        <a href="{{ route('profile.show', $post->user) }}" class="h-12 w-12 shrink-0 overflow-hidden rounded-full ring-1 ring-white/10">
                            <img src="{{ $post->user->image ? asset('storage/' . $post->user->image) : asset('images/person-placeholder.png') }}" alt="" class="h-full w-full object-cover">
                        </a>

                        <div class="min-w-0 flex-1">
                            <p class="text-base-content/85">
                                <a href="{{ route('profile.show', $post->user) }}" class="font-medium text-base-content transition-colors hover:text-primary">{{ $post->user->name }}</a>
                                started following you
                            </p>
                            <time class="mt-1 block text-xs text-base-content/40">{{ $post->created_at->diffForHumans() }}</time>
                        </div>

                        <a href="{{ route('profile.show', $post->user) }}"
                           class="shrink-0 rounded-selector border border-primary/50 px-4 py-2 font-display text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-primary transition hover:bg-primary/10">
                            View profile
                        </a>
                    </article>
                @endif

            @empty
                <div>
                    <p class="text-white">You don't follow anyone, yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    </div>
@endsection