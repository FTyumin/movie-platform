@extends('layouts.app')

@section('content')

<div class="min-h-screen max-w-6xl mx-auto px-4 py-8">
    {{-- Header Section --}}
    <div class="p-6">
        <div class="mb-12">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 flex items-center gap-3">
                        @svg('heroicon-o-list-bullet', 'h-8 w-8 text-yellow-400')
                        Movie Lists
                    </h1>
                    <p class="text-xl text-gray-400">
                        Explore curated collections created by the community
                    </p>
                </div>
                @if(Auth::check())
                    <a href="{{ route('lists.create') }}" class="bg-yellow-500 text-gray-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-black transform hover:scale-105 inline-flex items-center gap-2">
                        @svg('heroicon-o-plus', 'w-5 h-5')
                        Create New List
                    </a>
                @endif
            </div>
        </div>

        {{-- Lists Grid --}}
        @if(empty($lists))

            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-24 h-24 bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl flex items-center justify-center mb-6">
                    @svg('heroicon-o-list-bullet', 'w-12 h-12 text-gray-500')
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No Lists Yet</h3>
                <p class="text-gray-400 mb-6 text-center max-w-md">
                    Be the first to create a movie list and share your favorite films with the community!
                </p>

                @if(Auth::check())
                    <a href="{{ route('lists.create') }}" class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-black transform hover:scale-105 inline-flex items-center gap-2">
                        @svg('heroicon-o-plus', 'w-5 h-5')
                        Create Your First List
                    </a>
                @endif

            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($lists as $list)
                    <a href="{{ route('lists.show', $list) }}" class="group">
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6 hover:bg-gray-800/70 hover:border-yellow-500/40 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl hover:shadow-yellow-500/10 h-full flex flex-col">
                            {{-- List Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                    @svg('heroicon-o-list-bullet', 'w-6 h-6 text-gray-900')
                                </div>
                                
                                <div class="flex items-center gap-2 text-xs text-gray-400 bg-gray-700/50 px-3 py-1 rounded-full">
                                    @svg('heroicon-o-film', 'w-3 h-3 text-gray-400')
                                    {{ $list->movies->count() ?? 0 }} movies
                                </div>
                            </div>

                            {{-- Movie Posters Preview --}}
                            @if($list->movies->count() > 0)
                                <div class="mb-4 flex gap-2 overflow-hidden">
                                    @foreach($list->movies->take(3) as $movie)
                                        <div class="flex-1 aspect-[2/3] rounded-lg overflow-hidden bg-gray-700 relative group/poster">
                                            <img 
                                                src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"
                                                alt="{{ $movie->title }}"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover/poster:scale-110"
                                            >
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover/poster:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                    @endforeach
                                    
                                    @if($list->movies->count() > 3)
                                        <div class="flex-1 aspect-[2/3] rounded-lg bg-gray-700/50 border-2 border-dashed border-gray-600 flex items-center justify-center">
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-gray-400">+{{ $list->movies->count() - 3 }}</p>
                                                <p class="text-xs text-gray-500 mt-1">more</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-yellow-400 transition-colors line-clamp-2">
                                {{ $list->name }}
                            </h3>

                            <p class="text-gray-400 text-sm mb-4 flex-grow line-clamp-3 leading-relaxed">
                                {{ $list->description ?? 'No description provided' }}
                            </p>

                            {{-- List Footer --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 overflow-hidden">
                                        <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}" alt="" class="h-8 w-8 object-cover rounded-xl">
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-300">{{ $list->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $list->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                @svg('heroicon-o-arrow-right', 'w-5 h-5 text-gray-400')
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
