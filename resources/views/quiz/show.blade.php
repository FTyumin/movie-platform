@extends('layouts.app')

@section('title', 'Preference quiz')

@section('content')

<div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-6 bg-black">
    <div class="w-full max-w-4xl">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-300 hover:text-white transition-colors mb-6">
            â† Back
        </a>
        
        {{-- Header Section --}}
        <div class="text-center mb-12">
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Tell us about your <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-amber-400">movie preferences</span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Help us personalize your experience by selecting your favorite genres. We'll recommend movies you'll love!
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-8 md:p-10 hover:bg-gray-800/60 transition-colors">
            <form method="POST" action="{{ route('quiz.store') }}" x-data="{ selected: [] }">
                @csrf

                {{-- Instructions --}}
                <div class="mb-8 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-white text-sm font-medium mb-1">Pro Tip</p>
                            <p class="text-white text-sm">Select at least 3 genres to get better recommendations. You can always update your preferences later!</p>
                        </div>
                    </div>
                </div>

                {{-- Genre Selection Title --}}
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                        Select Your Favorite Genres
                    </h3>
                    <p class="text-gray-400 text-sm">Choose all the genres you enjoy watching</p>
                </div>

                {{-- Genre Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
                    @foreach($genres as $genre)
                    <label class="relative cursor-pointer group ">
                        <input  type="checkbox" name="genres[]"  value="{{ $genre->id }}" 
                            {{ in_array($genre->id, old('genres', [])) ? 'checked' : '' }}
                            class="peer hidden"
                            @change="$event.target.checked ? selected.push({{ $genre->id }}) 
                                : selected = selected.filter(id => id !== {{ $genre->id }})"
                        >
                        
                        {{-- Genre Card --}}
                        <div class="h-full p-4 rounded-xl border border-gray-700
                            transition-all duration-300
                            peer-checked:border-yellow-500
                            peer-checked:ring-2 peer-checked:ring-yellow-500/40">
                            
                            {{-- Genre Icon --}}
                            <div class="flex flex-col items-center text-center">
                                <span class="text-sm font-semibold text-gray-300 peer-checked:text-white transition-colors">
                                    {{ $genre->name }}
                                </span>
                            </div>

                            {{-- Checkmark --}}
                            <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-green-500 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity duration-150 pointer-events-none" aria-hidden="true">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Selected Count Display --}}
                <div class="mb-6 p-4 bg-gray-700/30 border border-gray-600 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-300">Selected Genres</p>
                                <p class="text-xs text-gray-500">Minimum 3 recommended</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-white" x-text="selected.length"></p>
                            <p class="text-xs text-gray-500">genres</p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-700">
                    <button type="submit" :disabled="selected.length < 3" :class="selected.length < 3 
                            ? 'opacity-50 cursor-not-allowed' : ''"
                        class="flex-1 bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-gray-900 font-semibold py-4 px-8 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-800 transform inline-flex items-center justify-center gap-3 text-lg"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save My Preferences
                    </button>
                    
                    <a href="{{ route('dashboard') }}" 
                        class="sm:w-auto bg-gray-700/50 hover:bg-gray-700 text-white font-semibold py-4 px-8 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-800 inline-flex items-center justify-center gap-2">
                        Skip for Now
                    </a>
                </div>
            </form>
        </div>

        {{-- Additional Info --}}
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                You can update your preferences anytime from your profile settings</a>
            </p>
        </div>
    </div>
</div>
@endsection
