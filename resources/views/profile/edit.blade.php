@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-6">
    <div class="w-full max-w-2xl">
        
        {{-- Header Section --}}
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Edit Profile</h1>
            <p class="text-gray-400">Update your personal information</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/10 border border-green-500/50 rounded-xl backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-green-400 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        {{-- Display All Validation Errors --}}
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded-xl backdrop-blur-sm">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <span class="text-red-400 font-medium block mb-2">Please fix the following errors:</span>
                    <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl p-8">
            <form action="{{ route('profile.update') }}" enctype="multipart/form-data" method="post" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Profile Image Section --}}
                <div class="flex flex-col items-center mb-8 pb-8 border-b border-gray-700">
                    <div class="relative group">
                        {{-- Current Profile Image --}}
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-700  flex items-center justify-center">
                            @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="Profile" class="w-full h-full object-cover" id="imagePreview">
                            @else
                            <span class="text-4xl font-bold text-white">{{ substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>
                        
                        {{-- Upload Icon Overlay --}}
                        <label for="image" class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </label>
                    </div>
                    
                    <input type="file" class="hidden" name="image" id="image" accept="image/*" onchange="previewImage(this)">
                    
                    <label for="image" class="mt-4 px-4 py-2 bg-gray-700/50 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload New Photo
                    </label>
                    
                    <p class="mt-2 text-xs text-gray-400">JPG, PNG. Max size 2MB</p>
                </div>

                {{-- Name Field --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input  type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            required 
                            class="w-full pl-12 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400  transition-all"
                            placeholder="Enter your full name"
                        >
                    </div>

                </div>

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" id="email"  name="email" 
                            value="{{ old('email', $user->email) }}"
                            required 
                            class="w-full pl-12 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                            placeholder="Enter your email address"
                        >
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-700">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500  transform inline-flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Profile
                    </button>
                    
                    <a 
                        href="{{ route('profile.show', auth()->user()->id) }}" 
                        class="flex-1 sm:flex-initial bg-gray-700/50 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 inline-flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create new image element if it doesn't exist
                const imgContainer = input.closest('.relative').querySelector('.rounded-full');
                imgContainer.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-full h-full object-cover" id="imagePreview">`;
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
        
