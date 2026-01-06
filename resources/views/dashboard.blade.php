@extends('layouts.app')

@section('title', 'dashboard')

@section('content')

<section>
<div class="min-h-screen bg-black text-white">
    <main class="relative z-10">
        <div class="py-12 px-6 lg:px-28">
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2">
                    Welcome back, <span class="text-blue-400">{{ $user->name }}</span>!
                </h1>

                <!-- Followers / Following -->
                <p class="text-lg text-gray-300 mb-2 flex items-center gap-4">
                    <x-user-list-modal
                        title="Followers"
                        :users="$user->followers->map->follower->filter()"
                        empty-message="No followers yet."
                    >
                        <x-slot name="trigger">
                            <span class="flex items-center gap-1 cursor-pointer hover:text-white transition">
                                <x-heroicon-o-user-group class="w-5 h-5 text-gray-400" />
                                {{ count($user->followers) }} Followers
                            </span>
                        </x-slot>
                    </x-user-list-modal>

                    <x-user-list-modal
                        title="Following"
                        :users="$user->followees->map->followee->filter()"
                        empty-message="Not following anyone yet."
                    >
                        <x-slot name="trigger">
                            <span class="flex items-center gap-1 cursor-pointer hover:text-white transition">
                                <x-heroicon-o-user-plus class="w-5 h-5 text-gray-400" />
                                {{ count($user->followees) }} Following
                            </span>
                        </x-slot>
                    </x-user-list-modal>

                </p>

                <p class="text-xl text-gray-400">
                    Here's what's happening with your movie collection
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Watchlist Count -->
                <div class="bg-gray-800/50 glass border border-gray-700 rounded-xl p-6 hover:border-gray-600 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Watchlist</p>
                            <p class="text-3xl font-bold text-blue-400">{{ count(auth()->user()->wantToWatch) }} </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center">
                            @svg('heroicon-o-bookmark', 'w-6 h-6 text-blue-400')
                        </div>
                    </div>
                </div>

                <!-- Watched Movies -->
                <div class="bg-gray-800/50 glass border border-gray-700 rounded-xl p-6 hover:border-gray-600 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Watched</p>
                            <p class="text-3xl font-bold text-green-400">{{ count(auth()->user()->seenMovies) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center">
                            @svg('heroicon-o-check-circle', 'w-6 h-6 text-green-400')
                        </div>
                    </div>
                </div>

                <!-- Reviews Written -->
                <div class="bg-gray-800/50 glass border border-gray-700 rounded-xl p-6 hover:border-gray-600 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Reviews</p>
                            <p class="text-3xl font-bold text-purple-400">{{ count($reviews) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center">
                            @svg('heroicon-o-pencil-square', 'w-6 h-6 text-purple-400')
                        </div>
                    </div>
                </div>

                <!-- Average Rating Given -->
                <div class="bg-gray-800/50 glass border border-gray-700 rounded-xl p-6 hover:border-gray-600 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Average Rating</p>
                            <p class="text-3xl font-bold text-yellow-400">{{ $average_review }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-600/20 rounded-xl flex items-center justify-center">
                            @svg('heroicon-s-star', 'w-6 h-6 text-yellow-400')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="px-6 lg:px-28 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Watchlist -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                @svg('heroicon-o-bookmark', 'w-6 h-6 text-blue-400')
                                My Watchlist
                            </h2>
                            
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($watchList as $movie)
                            <a href="{{ route('movies.show', $movie->movie) }}" class="group">
                                <div class="group relative">
                                <div class="aspect-[2/3] bg-gray-700 rounded-lg overflow-hidden relative">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                    src="https://image.tmdb.org/t/p/w500/{{ $movie->movie->poster_url }}"  
                                    alt="Movie poster" />
                            
                                <!-- Watched Badge -->
                                <div class="absolute top-2 right-2 bg-green-600 rounded-full p-1">
                                    @svg('heroicon-s-check', 'w-3 h-3 text-white')
                                </div>
                            
                            </div>
                            
                            <h3 class="mt-2 text-sm font-medium text-white line-clamp-2">
                                {{ $movie->movie->name }}
                            </h3>
                            <p class="text-xs text-gray-400">Added {{$movie->created_at->diffForHumans()}}</p>
                        
                            </div>
                            @endforeach

                        </a>
                        </div>
                    </div>

                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-8 mt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                @svg('heroicon-o-heart', 'w-6 h-6 text-red-400')
                                Favorites
                            </h2>
                        </div>

                        @if($favorites->isEmpty())
                            <p class="text-sm text-gray-400">No favorites yet.</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($favorites as $movie)
                                    <a href="{{ route('movies.show', $movie->movie) }}" class="group">
                                        <div class="aspect-[2/3] bg-gray-700 rounded-lg overflow-hidden">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 src="https://image.tmdb.org/t/p/w500/{{ $movie->movie->poster_url }}"
                                                 alt="Movie poster" />
                                        </div>
                                        <h3 class="mt-2 text-sm font-medium text-white line-clamp-2">
                                            {{ $movie->movie->name }}
                                        </h3>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-8 mt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                @svg('heroicon-o-list-bullet', 'w-6 h-6 text-green-400')
                                Your Lists
                            </h2>
                            <a href="{{ route('lists.index') }}" class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors">
                                View All →
                            </a>
                        </div>

                        @if($lists->isEmpty())
                            <p class="text-sm text-gray-400">You haven't created any lists yet.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($lists as $list)
                                    <a href="{{ route('lists.show', $list) }}" class="flex items-center justify-between rounded-lg border border-gray-700 px-4 py-3 hover:border-gray-600 transition-colors">
                                        <span class="text-white font-medium">{{ $list->name }}</span>
                                        <span class="text-xs text-gray-400">{{ $list->movies_count }} {{ Str::plural('movie', $list->movies_count) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Recent Reviews -->
                <div>
                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-8 mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                @svg('heroicon-o-pencil-square', 'w-6 h-6 text-purple-400')
                                Recent Reviews
                            </h2>
                        </div>

                        <div class="space-y-4 flex flex-col gap-4">
                            @foreach($reviews as $review)
                            <a href="{{ route('reviews.show', $review) }}">
                                <div class="border border-gray-700 rounded-lg p-4 hover:border-gray-600 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <img class="w-12 h-16 object-cover rounded" 
                                            src="https://image.tmdb.org/t/p/w500/{{ $review->movie->poster_url }}"
                                            alt="Movie poster" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-white mb-1">
                                                {{ $review->movie->name }}
                                            </h4>
                                            
                                            <!-- Star Rating -->
                                            <div class="flex items-center gap-1 mb-2">
                                                @for ($j = 0; $j < $review->rating; $j++)
                                                @svg('heroicon-s-star', 'w-4 h-4 text-yellow-400')
                                                @endfor

                                                @for($j = $review->rating; $j < 5; $j++)
                                                    @svg('heroicon-s-star', 'w-4 h-4 text-gray-500')
                                                @endfor
                                                <span class="text-sm text-gray-400 ml-1">{{ $review->rating }}</span>
                                            </div>
                                            
                                            <p class="text-sm text-gray-300 line-clamp-2">
                                            </p>
                                            
                                            <time class="text-sm text-gray-400">{{ $review->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            
                            <!-- Profile edit -->
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="w-8 h-8 bg-purple-600/20 rounded-lg flex items-center justify-center">
                                    @svg('heroicon-o-pencil-square', 'w-4 h-4 text-purple-400')
                                </div>
                                <span class="text-sm font-medium">Edit profile</span>
                            </a>
                            
                            <!-- Send suggestion link -->
                            <a href="/suggestion" class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                    @svg('heroicon-o-paper-airplane', 'w-4 h-4 text-blue-400')
                                </div>
                                <span class="text-sm font-medium">Send movie suggestion</span>
                            </a>

                            <!-- Change favorite genres
                            <a href="/quiz" class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                    @svg('heroicon-o-heart', 'w-4 h-4 text-blue-400')
                                </div>
                                <span class="text-sm font-medium">Select favorite genres</span>
                            </a> -->

                            @if(auth()->user()->is_admin)
                                <a href="/admin" class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                                        @svg('heroicon-o-shield-check', 'w-5 h-5 text-yellow-400')
                                    </div>
                                    <span class="text-sm font-medium">Go to admin dashboard</span>
                                </a>
                            @endif
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="w-full"> 
                                @csrf                               
                                <button type="submit" class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors w-full">
                                    <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center">
                                        @svg('heroicon-o-arrow-right-on-rectangle', 'w-4 h-4 text-green-400')
                                    </div>   
                                    <span class="text-sm font-medium">Log Out</span>     
                                </button>     
                            </form>

                            <a class="flex items-center gap-3 p-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <x-confirm-modal title="Delete account?" message="Your account and all of its data will be deleted. This action cannot be undone."
                                    :action="route('profile.destroy')" method="DELETE">
                                    <x-slot name="trigger" class="w-max h-max">
                                        <button  class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 transition-colors w-full">
                                            @svg('monoicon-delete', 'h-4 text-red-400')
                                            <span class="text-sm font-medium">Delete Account</span>     
                                        </button>     
                                    </x-slot>
                                </x-confirm-modal>

                            </a>
                                
                        </div>
                    </div>

                        <!-- Notifications -->
                    @if(auth()->user()->notifications->isNotEmpty())
                    <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-6 mt-4">
                        <h3 class="text-lg font-bold text-white mb-4">Notifications</h3>
                        <div class="space-y-3">
                            @foreach(auth()->user()->notifications as $notification)
                                <div class="p-4 bg-gray-800 rounded">
                                    <p class="text-white">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <span class="text-sm text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Recently Watched Movies -->
        <div class="px-6 lg:px-28 pb-12">
            <div class="bg-gray-800/50 glass border border-gray-700 rounded-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        @svg('heroicon-o-check-circle', 'w-6 h-6 text-green-400')
                        Recently Watched
                    </h2>

                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
                    @forelse($seen as $movie)
                    <a href="{{ route('movies.show', $movie->movie->slug) }}">
                        <div class="group relative">
                            <div class="aspect-[2/3] bg-gray-700 rounded-lg overflow-hidden relative">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                    src="https://image.tmdb.org/t/p/w500/{{ $movie->movie->poster_url }}" 
                                    alt="Movie poster" />
                                
                                <!-- Watched Badge -->
                                <div class="absolute top-2 right-2 bg-green-600 rounded-full p-1">
                                    @svg('heroicon-s-check', 'w-3 h-3 text-white')
                                </div>
                            </div>
                            
                            <h3 class="mt-2 text-sm font-medium line-clamp-2">
                                {{ $movie->movie->name }}
                            </h3>
                            <p class="text-xs text-gray-400">Watched {{ $movie->created_at->diffForHumans() }}</p>
                            
                        </div>
                    </a>
                    @empty
                    
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>
</section>

@endsection
