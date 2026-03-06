@extends('layouts.app')

@section('title', 'Load Movies')

@section('content')
<div class="min-h-screen bg-black text-white flex items-center justify-center p-6">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-gray-800/60 backdrop-blur border border-gray-700 rounded-2xl p-8 shadow-xl">

            <h1 class="text-2xl font-bold mb-2">Load Movies</h1>
            <p class="text-gray-400 mb-6">
                Specify how many movies should be loaded.
            </p>

            <form method="POST" action="{{ route('movies.load.store') }}" class="space-y-6">
                @csrf

                <!-- Movie count -->
                <div>
                    <label for="count" class="block text-sm font-medium text-gray-300 mb-2">
                        Number of movies
                    </label>
                    <input type="number" id="count" name="count" value="{{ old('count', 50) }}"
                        min="1"
                        max="1000"
                        class="w-full px-4 py-3 rounded-lg bg-black  border border-gray-600
                               focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="e.g. 100"
                        required
                    >
                    <p class="text-xs text-gray-400 mt-2">
                        Recommended: 20–100 per load
                    </p>

                    <select name="method" class="bg-black">
                        <option value="top-rated">Top rated</option>
                        <option value="popular">Popular</option>
                        <option value="discover">Discover(standard)</option>
                        <option value="now-playing">Now Playing</option>

                    </select>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-yellow-400 text-black font-semibold py-3 rounded-lg
                           hover:bg-yellow-300 transition transform hover:scale-[1.02]"
                >
                    Start Loading
                </button>
            </form>
        </div>

        <!-- Back link -->
        <div class="text-center mt-6">
            <a href="/admin" class="text-gray-400 hover:text-white transition">
                ← Back to admin
            </a>
        </div>
    </div>
</div>
@endsection
