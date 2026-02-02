<header class="sticky top-0 w-full py-4 px-4 sm:px-6 z-50 bg-gradient-to-b from-neutral-800 to-neutral-900 backdrop-blur-md border-b border-gray-800">
  <nav class="relative w-full flex justify-between items-center gap-4">
    
    <!-- Left Section: Logo + Desktop Navigation -->
    <div class="flex items-center gap-6">
      <!-- Logo -->
      <a href="/" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
        <span class="text-lg sm:text-xl font-bold text-white tracking-tight">FilmStack</span>
      </a>

      <!-- Desktop Navigation -->
      <div class="hidden lg:flex items-center gap-6">
        <a href="{{ route('movies.index') }}" class="text-gray-300 hover:text-white transition-colors font-medium text-md">Movies</a>
        <a href="/reviews" class="text-gray-300 hover:text-white transition-colors font-medium text-md">Reviews</a>
        <a href="/lists" class="text-gray-300 hover:text-white transition-colors font-medium text-md">Lists</a>
        <a href="/feed" class="text-gray-300 hover:text-white transition-colors font-medium text-md">Feed</a>
      </div>
    </div>

    <!-- Center: Search Bar (Desktop Only) -->
    <div class="hidden md:flex flex-1 max-w-2xl mx-6">
      <form class="relative w-full" method="GET" action="{{ route('movies.search') }}">
        @csrf
        <label for="search" class="sr-only">Search movies, directors and actors</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
            @svg('heroicon-o-magnifying-glass', 'w-5 h-5 text-gray-400')
          </div>
          
          <input type="search" id="search" name="search"
            class="block w-full pl-12 pr-24 py-3 text-sm text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700
             rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
            placeholder="Search movies, directors, actors" 
            autocomplete="off"
          />
          
          <button type="submit" 
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-yellow-700 text-white font-medium
             rounded-lg text-sm px-4 py-2 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
            Search
          </button>
        </div>
      </form>
    </div>

    <!-- Right Section: Actions -->
    <div class="flex items-center gap-3">
      
      <!-- Mobile Search Button -->
      <button onclick="document.getElementById('mobile-search').classList.toggle('hidden')" 
        class="md:hidden p-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
        type="button" aria-label="Toggle search"
      >
        @svg('heroicon-o-magnifying-glass', 'w-5 h-5')
      </button>

      <!-- Sign In Button (Desktop) / User Profile -->
      @guest
        <a href="{{ url('/login') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white 
          bg-yellow-500/90 hover:bg-yellow-400 rounded-lg">
          @svg('heroicon-o-arrow-right-on-rectangle', 'w-4 h-4')
          Sign In
        </a>
        
        <!-- Mobile Sign In Icon -->
        <a href="{{ url('/login') }}" class="sm:hidden p-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors" aria-label="Sign in">
          @svg('heroicon-o-arrow-right-on-rectangle', 'w-5 h-5')
        </a>
      @endguest

      @auth
        <a href="{{ route('dashboard') }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full overflow-hidden ring-2 ring-gray-700 hover:ring-blue-500 transition-all">
          @if(auth()->user()->image)
            <img src="{{ asset('storage/' . auth()->user()->image) }}"
                alt="{{ auth()->user()->name}}"
                class="w-full h-full object-cover">
          @else 
            <img src="{{ asset('images/person-placeholder.png') }}"
                alt="placeholder img"
                class="w-full h-full object-cover">
          @endif
        </a>
      @endauth

      <!-- Mobile Menu Button -->
      <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" 
        class="lg:hidden p-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors" 
        type="button" aria-label="Toggle menu"
      >
        @svg('heroicon-o-bars-3', 'w-6 h-6')
      </button>
    </div>
  </nav>

  <!-- Mobile Search Bar -->
  <div id="mobile-search" class="hidden md:hidden mt-4 pt-4 border-t border-gray-800">
    <form class="relative" method="GET" action="{{ route('movies.search') }}">
      @csrf
      <label for="mobile-search-input" class="sr-only">Search movies</label>
      <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
          @svg('heroicon-o-magnifying-glass', 'w-5 h-5 text-gray-400')
        </div>
        
        <input  type="search" id="mobile-search-input" name="search"
          class="block w-full pl-12 pr-20 py-3 text-sm text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
          placeholder="Search movies..."  autocomplete="off"
        />
        
        <button  type="submit" 
          class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-3 py-2 transition-colors">
          Search
        </button>
      </div>
    </form>
  </div>

  <!-- Mobile Navigation Menu -->
  <div id="mobile-menu" class="lg:hidden hidden mt-4 pt-4 border-t border-gray-800">
    <div class="flex flex-col gap-3">
      <a href="{{ route('movies.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
        @svg('heroicon-o-film', 'w-5 h-5')
        <span class="font-medium">Movies</span>
      </a>
      
      <a href="/reviews" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
        @svg('heroicon-o-star', 'w-5 h-5')
        <span class="font-medium">Reviews</span>
      </a>
      
      <a href="/lists" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
        @svg('heroicon-o-list-bullet', 'w-5 h-5')
        <span class="font-medium">Lists</span>
      </a>
      
      <a href="/feed" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
        @svg('heroicon-o-rectangle-stack', 'w-5 h-5')
        <span class="font-medium">Feed</span>
      </a>

      @guest
      <a href="{{ url('/login') }}" class="flex items-center gap-3 px-4 py-3 mt-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
        @svg('heroicon-o-arrow-right-on-rectangle', 'w-5 h-5')
        <span class="font-medium">Sign In</span>
      </a>
      @endguest
    </div>
  </div>
</header>
