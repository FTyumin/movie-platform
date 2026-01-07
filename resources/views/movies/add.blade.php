@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-black text-white flex items-center justify-center p-6">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-gray-800/60 backdrop-blur border border-gray-700 rounded-2xl p-8 shadow-xl">

            <h1 class="text-2xl font-bold mb-2">Add Movie</h1>
            <p class="text-gray-400 mb-6">
                Specify movie's TMDB ID
            </p>

            @if (session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/40 text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('movies.store') }}" class="space-y-6">
                @csrf

                <!-- Movie count -->
                <div>
                    <label for="movie_id" class="block text-sm font-medium text-gray-300 mb-2">
                        TMDB ID
                    </label>
                    <input type="" id="movie_id" name="movie_id"  
                        class="w-full px-4 py-3 rounded-lg bg-black  border border-gray-600 
                               focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="e.g. 100" required
                    >
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-yellow-400 text-black font-semibold py-3 rounded-lg
                           hover:bg-yellow-300 transition transform hover:scale-[1.02]">
                   Add Movie
                </button>
            </form>
        </div>

        <!-- Back link -->
        <div class="text-center mt-6">
            <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-white transition">
                ← Back to admin
            </a>
        </div>
    </div>
</div>

@endsection