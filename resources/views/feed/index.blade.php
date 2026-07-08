@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl text-white font-bold mb-6">Your Feed</h1>
        
        <div class="space-y-6">
            @forelse($activities as $post)
                @if($post->activityable_type == 'App\Models\Review' && $post->activityable)

                    <x-review  :review="$post->activityable" />

                  @elseif($post->activityable_type == 'App\Models\MovieList')
                    <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="block">
                            <div class="flex gap-4 p-6">
                                {{-- Movie Poster --}}
                                <div class="shrink-0">
                                
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
                                                    <span class="text-white font-normal">created a list</span>
                                                    <span class="text-yellow-400"><a href="{{ route('lists.show', $post->activityable->id) }}">{{ $post->activityable->name }}</a></span>
                                                </h2>
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <a href="" 
                                                    class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center">
                                                            <!-- <span class="text-white text-sm font-semibold">{{ substr($post->user->name, 0, 1) }}</span> -->
                                                            <img src="{{ $post->user->image ? asset('storage/' . $post->user->image) : asset('images/person-placeholder.png') }}" alt="" class="w-full h-full object-cover">
                                                        </div>
                                                        <span class="text-gray-300 text-sm font-medium">{{ $post->activityable->user->name }}</span>
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
                    <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="block">
                            <div class="flex gap-4 p-6">

                                {{-- Card Content --}}  
                                    <div class="flex-1 min-w-0">
                                        {{-- Card Header --}}
                                        <div class="flex items-start justify-between gap-4 mb-4">
                                            <div class="flex-1">
                                                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                                                    <span class="text-yellow-400 cursor-pointer">
                                                        {{$post->user->name}}
                                                    </span>
                                                    <span class="text-white font-normal">followed you!</span>
                                                </h2>
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <a href="" 
                                                    onclick="event.stopPropagation()"
                                                    class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center">
                                                            <img src="{{ $post->user->image ? asset('storage/' . $post->user->image) : asset('images/person-placeholder.png') }}" alt="" class="w-full h-full object-cover">
                                                        </div>
                                                        <span class="text-gray-300 text-sm font-medium"></span>
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