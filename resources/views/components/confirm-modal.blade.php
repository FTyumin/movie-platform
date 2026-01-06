<div x-data="{ open: false }" class="inline-block">
    <!-- Trigger -->
    <span @click="open = true">
        {{ $trigger }}
    </span>

    <!-- Backdrop -->
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/60 z-40"
        @click="open = false"
    ></div>

    <!-- Modal -->
    <div
        x-cloak
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <div
            class="bg-gray-900 border border-gray-700 rounded-2xl shadow-xl max-w-sm w-full p-6"
            @click.stop
        >
            <h3 class="text-lg font-semibold text-white mb-2">
                {{ $title }}
            </h3>

            <p class="text-sm text-gray-400 mb-6">
                {{ $message }}
            </p>

            <div class="flex justify-end gap-3">
                <button
                    @click="open = false"
                    type="button"
                    class="px-4 py-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-white transition"
                >
                    Cancel
                </button>

                <form method="POST" action="{{ $action }}">
                    @csrf
                    @if($method !== 'POST')
                        @method($method)
                    @endif

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition"
                    >
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
