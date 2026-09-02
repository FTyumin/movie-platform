@props([
    'title',
    'users',
    'emptyMessage' => 'No users yet.',
])

<div x-data="{ open: false }" class="inline-block">
    <span @click="open = true">
        {{ $trigger }}
    </span>

    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-black/70"
        @click="open = false"
    ></div>

    <div
        x-cloak
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <div
            class="w-full max-w-md rounded-box border border-white/9 bg-base-200 p-6 shadow-2xl"
            @click.stop
        >
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-display text-lg font-semibold uppercase tracking-[0.02em] text-base-content">{{ $title }}</h3>
                <button type="button"
                    @click="open = false"
                    class="flex h-8 w-8 items-center justify-center rounded-selector text-base-content/45 transition hover:bg-base-300 hover:text-base-content" aria-label="Close">
                    @svg('heroicon-o-x-mark', 'h-4 w-4')
                </button>
            </div>

            <div class="max-h-80 overflow-y-auto pr-1">
                @forelse($users as $user)
                    <a
                        href="{{ route('profile.show', $user) }}"
                        class="flex items-center gap-3 rounded-selector border-b border-white/6 px-2 py-3 transition last:border-b-0 hover:bg-base-300"
                    >
                        <img
                            src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/person-placeholder.png') }}"
                            alt=""
                            class="h-10 w-10 shrink-0 rounded-full object-cover ring-1 ring-white/10"
                        />
                        <span class="text-sm font-medium text-base-content">{{ $user->name }}</span>
                    </a>
                @empty
                    <p class="text-sm text-base-content/50">{{ $emptyMessage }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
