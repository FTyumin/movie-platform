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
        class="fixed inset-0 bg-black/60 z-40"
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
            class="bg-gray-900 border border-gray-700 rounded-2xl shadow-xl max-w-md w-full p-6"
            @click.stop
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
                <button type="button"
                    @click="open = false"
                    class="text-gray-400 hover:text-white transition" aria-label="Close">
                    ✕
                </button>
            </div>

            <div class="max-h-80 overflow-y-auto pr-1">
                @forelse($users as $user)
                    <a
                        href="{{ route('profile.show', $user) }}"
                        class="flex items-center gap-3 py-3 border-b border-gray-800 last:border-b-0 hover:bg-gray-800/50 rounded-lg px-2 transition"
                    >
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-700 shrink-0">
                            <img
                                src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/person-placeholder.png') }}"
                                alt="{{ $user->name }}"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <span class="text-white font-medium">{{ $user->name }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">{{ $emptyMessage }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
