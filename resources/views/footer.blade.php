<footer class="border-t border-white/[0.07] bg-base-100">
    <div class="mx-auto flex w-full max-w-[110rem] flex-col gap-6 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between">

        <div>
            <a href="/" class="font-display text-lg font-black uppercase tracking-[-0.02em] text-primary">Filmstack</a>
            <p class="mt-1.5 font-display text-[0.7rem] uppercase tracking-[0.16em] text-base-content/55">
                &copy; {{ date('Y') }} Filmstack &middot; The theater is yours
            </p>
        </div>

        <nav class="flex flex-wrap gap-x-7 gap-y-3" aria-label="Footer">
            <a href="{{ route('movies.index') }}" class="font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/50 transition-colors hover:text-primary">Movies</a>
            <a href="{{ route('reviews') }}" class="font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/50 transition-colors hover:text-primary">Reviews</a>
            <a href="{{ route('lists.index') }}" class="font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/50 transition-colors hover:text-primary">Lists</a>
            <a href="{{ route('people.index') }}" class="font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/50 transition-colors hover:text-primary">People</a>
        </nav>
    </div>
</footer>
