@extends('layouts.app')

@section('content')

<div class="relative z-10 min-h-screen">
    {{-- Back Button --}}
    <div class="py-6 px-6 lg:px-28">
        <a href="{{ route('lists.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors group">
            @svg('heroicon-o-arrow-left', 'w-5 h-5')
            Back to Lists
        </a>

    {{-- List Header --}}
    <div class="flex items-start gap-4 mb-4">
        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0">
            @svg('heroicon-o-list-bullet', 'w-8 h-8 text-gray-900')
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start gap-3 flex-wrap">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    {{ $list->name }}
                </h1>
                
                @auth
                    @if(Auth::id() === $list->user_id)
                        {{-- Inline edit button --}}
                        <a href="{{ route('lists.edit', $list) }}"
                        class="mt-1 inline-flex items-center gap-2 rounded-lg border border-yellow-500/30
                                bg-yellow-500/10 px-3 py-1.5 text-sm text-yellow-300
                                hover:bg-yellow-500/20 hover:text-yellow-200 transition">
                            @svg('heroicon-o-pencil-square', 'w-4 h-4')
                            Edit
                        </a>

                        {{-- Delete button --}}
                        <x-confirm-modal
                            title="Delete list?"
                            message="This will permanently delete the list and remove all movies from it. This action cannot be undone."
                            :action="route('lists.destroy', $list)"
                            method="DELETE"
                        >
                            <x-slot name="trigger">
                                <button
                                    class="mt-1 inline-flex items-center gap-2 rounded-lg
                                        bg-red-600/10 border border-red-500/30
                                        px-3 py-1.5 text-sm text-red-300
                                        hover:bg-red-600/20 transition">
                                    @svg('heroicon-o-trash', 'w-4 h-4')
                                    Delete
                                </button>
                            </x-slot>
                        </x-confirm-modal>
                    @endif
                @endauth
            </div>

            @if($list->description)
                <p class="mt-2 text-gray-300 leading-relaxed">
                    {{ $list->description }}
                </p>
            @endif

            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <img src="{{ $list->user->image ? asset('storage/' . $list->user->image) : asset('images/person-placeholder.png') }}"
                        class="h-8 w-8 object-cover rounded-xl">
                    <span class="text-gray-300 text-sm">
                        by <span class="font-medium">{{ $list->user->name }}</span>
                    </span>
                </div>
                <span class="text-gray-500 text-sm">•</span>
                <span class="text-gray-400 text-sm">{{ $list->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

        {{-- Movies Grid --}}
        @if($list->movies->count() > 0)
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                @svg('heroicon-o-film', 'w-6 h-6 text-yellow-400')
                Movies in this List
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($list->movies as $movie)
                    <div class="group relative">
                        <a href="{{ route('movies.show', $movie->slug) }}" class="block">
                            <div class="aspect-[2/3] bg-gray-700/50 rounded-lg overflow-hidden border border-gray-600/50 hover:border-yellow-500/40 transition-all group-hover:shadow-lg group-hover:shadow-yellow-500/20">
                                <img class="w-full h-full object-cover  transition-transform duration-300" 
                                    src="https://image.tmdb.org/t/p/w500/{{ $movie->poster_url }}"  
                                    alt="{{ $movie->title }}" 
                                    loading="lazy" />
                            </div>

                            {{-- Movie Info Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex flex-col justify-end p-3">
                                <h3 class="text-white font-semibold text-sm mb-1 line-clamp-2">
                                    {{ $movie->name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @svg('heroicon-s-star', 'w-3 h-3 text-yellow-400')
                                        <span class="text-white text-xs font-medium">{{ number_format($movie->tmdb_rating ?? 0, 1) }}</span>
                                    </div>
                                    <span class="text-gray-400 text-xs">{{ $movie->year ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </a>

                        {{-- Remove Button (only for list owner) --}}
                        @auth
                            @if(Auth::id() === $list->user_id)
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <x-confirm-modal
                                        title="Remove {{ $movie->name }}?"
                                        message="This movie will be removed from your list. This action cannot be undone."
                                        :action="route('lists.remove', [$list->id, $movie->id])"
                                        method="DELETE"
                                    >
                                        <x-slot name="trigger">
                                            <button class="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-full transition">
                                                @svg('heroicon-o-x-mark', 'w-4 h-4')
                                            </button>
                                        </x-slot>
                                    </x-confirm-modal>
                                </div>
                            @endif
                        @endauth
                        
                    </div>
                @endforeach

                 @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>
        @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20">
            <div class="w-24 h-24 bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl flex items-center justify-center mb-6">
                @svg('heroicon-o-film', 'w-12 h-12 text-gray-500')
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">No Movies Yet</h3>
            <p class="text-gray-400 mb-6 text-center max-w-md">
                This list is empty. Start adding your favorite movies to build your collection!
            </p>
            @auth
                @if(Auth::id() === $list->user_id)
                <a href="{{ route('movies.index') }}" class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold py-3 px-6 rounded-lg transition-all duration-300 inline-flex items-center gap-2">
                    @svg('heroicon-o-plus', 'w-5 h-5')
                    Add Movies
                </a>
                @endif
            @endauth
        </div>
        @endif
    </div>
</div>
@endsection
