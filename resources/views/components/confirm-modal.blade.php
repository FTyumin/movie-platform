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
        class="fixed inset-0 z-40 bg-black/70"
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
            class="w-full max-w-sm rounded-box border border-white/9 bg-base-200 p-6 shadow-2xl sm:p-7"
            @click.stop
        >
            <div class="flex items-start gap-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-selector bg-error/15 text-error">
                    @svg('heroicon-o-exclamation-triangle', 'h-5 w-5')
                </span>
                <div>
                    <h3 class="font-display text-lg font-semibold uppercase tracking-[0.02em] text-base-content">
                        {{ $title }}
                    </h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-base-content/60">
                        {{ $message }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="open = false"
                    type="button"
                    class="rounded-selector border border-white/9 bg-base-100 px-5 py-2.5 font-display text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-base-content/70 transition hover:bg-base-300"
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
                        class="rounded-selector bg-error px-5 py-2.5 font-display text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-error-content transition hover:brightness-110"
                    >
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
