@extends('layouts.app')

@section('title', 'Edit List')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-xl shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-pencil-square', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Edit <span class="text-primary">{{ $list->name }}</span>
                </h1>
                <p class="text-sm text-base-content/55">Update your list's details and privacy.</p>
            </div>
        </div>

        <form action="{{ route('lists.update', $list) }}" method="POST" class="mt-6 space-y-5">
            @method('PATCH')
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    List name
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-tag', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input type="text" id="name" name="name" value="{{ old('name', $list->name) }}"
                        required maxlength="30"
                        placeholder="e.g. Best Sci-Fi Movies"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Description
                </label>
                <textarea id="description" name="description" rows="4" maxlength="300"
                    placeholder="What is this list about?"
                    class="w-full rounded-box border border-white/9 bg-base-100 px-4 py-3 text-sm text-base-content placeholder:text-base-content/40 transition focus:border-primary/50 focus:outline-none">{{ old('description', $list->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Privacy --}}
            <label for="is_private" class="flex cursor-pointer items-center justify-between gap-4 rounded-box border border-white/9 bg-base-100 px-4 py-3.5">
                <span>
                    <span class="block text-sm font-medium text-base-content">Private list</span>
                    <span class="mt-0.5 block text-xs text-base-content/55">Only you will be able to see this list</span>
                </span>
                <input type="hidden" name="is_private" value="0">
                <input type="checkbox" id="is_private" name="is_private" value="1" {{ old('is_private', $list->is_private) ? 'checked' : '' }}
                    class="checkbox checkbox-primary rounded-[0.3rem]">
            </label>

            {{-- Submit --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-selector bg-primary px-8 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
