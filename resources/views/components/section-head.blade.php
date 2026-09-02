@props([
    'title',
    'href' => null,
    'linkLabel' => 'View all',
])

{{-- Section marker: a letterspaced label sitting on a short amber rule.
     The rule is the only ornament the page repeats, so it does the work of
     telling you a new run of content has started. --}}
<div {{ $attributes->merge(['class' => 'mb-8 flex items-end justify-between gap-6']) }}>
    <div>
        <h2 class="font-display text-sm font-medium uppercase tracking-[0.22em] text-base-content">
            {{ $title }}
        </h2>
        <span class="mt-3 block h-[3px] w-14 bg-primary"></span>
    </div>

    @if($href)
        <a href="{{ $href }}"
           class="group inline-flex shrink-0 items-center gap-2 font-display text-[0.72rem] uppercase tracking-[0.18em] text-base-content/50 transition-colors hover:text-primary">
            {{ $linkLabel }}
            @svg('heroicon-o-arrow-right', 'h-3.5 w-3.5 transition-transform group-hover:translate-x-1')
        </a>
    @endif
</div>
