@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black text-white flex justify-center px-6 py-20">
    <div class="w-full max-w-xl">

        {{-- Header --}}
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Create Movie List</h1>
            <p class="text-gray-400">
                Create a custom list to organize and share your favorite movies.
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-gray-900/70 backdrop-blur border border-gray-700 rounded-2xl p-8 shadow-xl">

            <form action="{{ route('lists.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        List name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-gray-800/60 border border-gray-600
                               text-white placeholder-gray-400
                               focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="e.g. Best Sci-Fi Movies"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-gray-800/60 border border-gray-600
                               text-white placeholder-gray-400
                               focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="What is this list about?"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Privacy --}}
                <div class="flex items-center justify-between rounded-xl bg-gray-800/40 border border-gray-700 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-white">Private list</p>
                        <p class="text-xs text-gray-400">
                            Private lists are visible to other users
                        </p>
                    </div>

                    <input type="hidden" name="is_private" value="0">
                    <input type="checkbox" id="is_private" name="is_private" value="1"
                        class="w-5 h-5 rounded border-gray-600 bg-gray-700
                               text-yellow-400 focus:ring-yellow-400"
                    >
                </div>

                {{-- Submit --}}
                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl
                               bg-yellow-400 text-gray-900 font-semibold
                               hover:bg-yellow-300 transition
                               focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-black"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Create List
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


@endsection