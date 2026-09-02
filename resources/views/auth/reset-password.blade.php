@extends('layouts.app')

@section('title', 'Reset password')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-md shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-lock-closed', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Reset your password
                </h1>
                <p class="text-sm text-base-content/55">Choose a new password for your account.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div>
                <label for="email" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Email address
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-envelope', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                        required autofocus autocomplete="username"
                        placeholder="you@example.com"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- New password --}}
            <div x-data="{ show: false }">
                <label for="password" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    New password
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                        required autocomplete="new-password"
                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                    <button type="button" @click="show = !show"
                        class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                        x-text="show ? 'Hide' : 'Show'">Show</button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                    Confirm password
                </label>
                <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                    @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                    <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                        required autocomplete="new-password"
                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                    <button type="button" @click="show = !show"
                        class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                        x-text="show ? 'Hide' : 'Show'">Show</button>
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                Reset password
            </button>
        </form>
    </div>
</div>
@endsection
