@php
  // Nav is a flat list so the active-state rule lives in one place.
  $navLinks = [
    ['label' => 'Movies',  'href' => route('movies.index'), 'active' => request()->is('movies*')],
    ['label' => 'Reviews', 'href' => route('reviews'),      'active' => request()->is('reviews*')],
    ['label' => 'Lists',   'href' => route('lists.index'),  'active' => request()->is('lists*')],
    ['label' => 'Feed',    'href' => url('/feed'),          'active' => request()->is('feed*')],
  ];
@endphp

<header class="sticky top-0 z-50 w-full border-b border-white/[0.07] bg-base-100/85 backdrop-blur-xl">
  <nav class="mx-auto flex w-full max-w-[110rem] items-center gap-4 px-5 py-3.5 sm:px-8">

    {{-- Wordmark --}}
    <a href="/" class="shrink-0 font-display text-xl font-black uppercase tracking-[-0.02em] text-primary transition hover:brightness-110 sm:text-2xl">
      Filmstack
    </a>

    {{-- Desktop nav --}}
    <div class="ml-6 hidden items-center gap-7 lg:flex">
      @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}"
           @class([
             'relative py-1 font-display text-[0.8rem] font-medium uppercase tracking-[0.14em] transition-colors',
             'text-primary' => $link['active'],
             'text-base-content/55 hover:text-base-content' => ! $link['active'],
           ])
           @if($link['active']) aria-current="page" @endif>
          {{ $link['label'] }}
          @if($link['active'])
            <span class="absolute inset-x-0 -bottom-0.5 h-[2px] bg-primary"></span>
          @endif
        </a>
      @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('movies.search') }}" class="ml-auto hidden md:block">
      <label for="site-search" class="sr-only">Search films and people</label>
      <div class="flex w-56 items-center gap-2 rounded-selector border border-white/[0.07] bg-base-200 px-4 py-2 transition focus-within:border-primary/40 lg:w-72">
        @svg('heroicon-o-magnifying-glass', 'h-4 w-4 shrink-0 text-base-content/40')
        <input id="site-search" type="search" name="search" placeholder="Search…" autocomplete="off"
               class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/55 focus:outline-none" />
      </div>
    </form>

    {{-- Actions --}}
    <div class="ml-auto flex shrink-0 items-center gap-2 md:ml-0 md:pl-4">

      {{-- Mobile search toggle --}}
      <button type="button" aria-label="Search"
        onclick="document.getElementById('mobile-search').classList.toggle('hidden')"
        class="rounded-selector p-2 text-base-content/60 transition hover:bg-base-200 hover:text-base-content md:hidden">
        @svg('heroicon-o-magnifying-glass', 'h-5 w-5')
      </button>

      @auth
        <a href="{{ url('/feed') }}" aria-label="Activity feed"
           class="hidden rounded-selector p-2 text-base-content/60 transition hover:bg-base-200 hover:text-base-content sm:block">
          @svg('heroicon-o-bell', 'h-5 w-5')
        </a>

        <a href="{{ route('dashboard') }}" aria-label="Your profile"
           class="h-9 w-9 overflow-hidden rounded-full ring-1 ring-white/10 transition hover:ring-primary">
          <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('images/person-placeholder.png') }}"
               alt="" class="h-full w-full object-cover">
        </a>
      @endauth

      @guest
        <a href="{{ url('/login') }}"
           class="rounded-selector bg-primary px-5 py-2 font-display text-[0.8rem] font-semibold uppercase tracking-[0.12em] text-primary-content transition hover:brightness-110">
          Log in
        </a>
      @endguest

      {{-- Mobile menu toggle --}}
      <button type="button" aria-label="Menu"
        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
        class="rounded-selector p-2 text-base-content/60 transition hover:bg-base-200 hover:text-base-content lg:hidden">
        @svg('heroicon-o-bars-3', 'h-6 w-6')
      </button>
    </div>
  </nav>

  {{-- Mobile search --}}
  <div id="mobile-search" class="hidden border-t border-white/[0.07] px-5 py-4 md:hidden">
    <form method="GET" action="{{ route('movies.search') }}">
      <label for="mobile-search-input" class="sr-only">Search films and people</label>
      <div class="flex items-center gap-2 rounded-selector border border-white/[0.07] bg-base-200 px-4 py-2.5 focus-within:border-primary/40">
        @svg('heroicon-o-magnifying-glass', 'h-4 w-4 shrink-0 text-base-content/40')
        <input id="mobile-search-input" type="search" name="search" placeholder="Search…" autocomplete="off"
               class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/55 focus:outline-none" />
      </div>
    </form>
  </div>

  {{-- Mobile nav --}}
  <div id="mobile-menu" class="hidden border-t border-white/[0.07] px-5 py-4 lg:hidden">
    <div class="flex flex-col">
      @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}"
           @class([
             'rounded-field px-3 py-3 font-display text-sm uppercase tracking-[0.14em] transition-colors',
             'text-primary' => $link['active'],
             'text-base-content/70 hover:bg-base-200 hover:text-base-content' => ! $link['active'],
           ])>
          {{ $link['label'] }}
        </a>
      @endforeach

      @guest
        <a href="{{ url('/login') }}"
           class="mt-3 rounded-selector bg-primary px-3 py-3 text-center font-display text-sm font-semibold uppercase tracking-[0.12em] text-primary-content">
          Log in
        </a>
      @endguest
    </div>
  </div>
</header>
