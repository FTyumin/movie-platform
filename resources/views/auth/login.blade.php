@extends('layouts.app')

@section('title', 'Log in')

{{-- This page manages its own gutters so the two cards can sit centered on
     an open black floor. --}}
@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="flex w-full max-w-4xl flex-col items-center gap-8 lg:flex-row lg:items-stretch lg:justify-center">

        {{-- ============================================================
             STUB — branding card, the ticket half of the pair.
             ============================================================ --}}
        <div class="relative w-full max-w-sm shrink-0 overflow-hidden rounded-box border border-white/[0.09] bg-base-200">

            {{-- the house print, thrown up behind the wordmark --}}
            <div class="relative h-56 w-full sm:h-64">
                <img src="{{ asset('heat.jpeg') }}" alt=""
                     class="pointer-events-none absolute inset-0 h-full w-full object-cover object-top opacity-70">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-base-200 via-base-200/15 to-transparent" aria-hidden="true"></div>

                <a href="/" class="relative z-10 inline-flex items-center gap-2 p-6 font-display text-lg font-black uppercase tracking-[-0.02em] text-primary">
                    <span class="h-4 w-[3px] bg-primary"></span>
                    Filmstack
                </a>
            </div>

            {{-- Perforated tear-line between the poster and the copy --}}
            <div class="relative h-px w-full" aria-hidden="true">
                <div class="absolute inset-x-6 top-0 h-px border-t-2 border-dashed border-white/[0.09]"></div>
                <span class="absolute -left-1 -top-[7px] h-3.5 w-3.5 rounded-full bg-base-100"></span>
                <span class="absolute -right-1 -top-[7px] h-3.5 w-3.5 rounded-full bg-base-100"></span>
            </div>

            <div class="p-6 sm:p-8">
                <p class="font-display text-[0.72rem] uppercase tracking-[0.22em] text-base-content/55">
                    Take your seat.
                </p>
                <span class="mt-3 block h-[3px] w-14 bg-primary"></span>

                <h1 class="mt-6 font-display text-3xl font-medium uppercase leading-[1.05] tracking-[0.04em] text-base-content">
                    Welcome back<br>to the house.
                </h1>

                <p class="mt-5 text-sm leading-relaxed text-base-content/60">
                    Watchlist, ratings, recommendations — everything a film lover needs, in one place.
                </p>

                <ul class="mt-8 space-y-4">
                    <li class="flex items-center gap-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
                            @svg('heroicon-o-bookmark', 'h-3.5 w-3.5')
                        </span>
                        <span class="text-sm text-base-content/75">Your personal watchlist</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
                            @svg('heroicon-o-pencil-square', 'h-3.5 w-3.5')
                        </span>
                        <span class="text-sm text-base-content/75">Reviews you've rated and written</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
                            @svg('heroicon-o-sparkles', 'h-3.5 w-3.5')
                        </span>
                        <span class="text-sm text-base-content/75">Recommendations built from your taste</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ============================================================
             WINDOW — sign-in / create-account, sliding between the two
             ============================================================ --}}
        <div class="w-full max-w-sm shrink-0 overflow-hidden rounded-box border border-white/[0.09] bg-base-200 p-6 sm:p-8"
             x-data="{ tab: {{ ($errors->has('name') || $errors->has('image') || old('name')) ? "'register'" : "'signin'" }} }">

            {{-- Sign in / Create account tabs --}}
            <div class="flex items-center gap-6 border-b border-white/[0.09]">
                <button type="button" @click="tab = 'signin'"
                    class="relative -mb-px border-b-2 pb-3 font-display text-[0.78rem] font-semibold uppercase tracking-[0.14em] transition-colors"
                    :class="tab === 'signin' ? 'border-primary text-base-content' : 'border-transparent text-base-content/45 hover:text-base-content/70'">
                    Sign in
                </button>
                <button type="button" @click="tab = 'register'"
                    class="relative -mb-px border-b-2 pb-3 font-display text-[0.78rem] uppercase tracking-[0.14em] transition-colors"
                    :class="tab === 'register' ? 'border-primary font-semibold text-base-content' : 'border-transparent text-base-content/45 hover:text-base-content/70'">
                    Create account
                </button>
            </div>

            <x-auth-session-status class="mt-6" :status="session('status')" />

            {{-- Sliding viewport --}}
            <div class="mt-6 overflow-hidden">
                <div class="flex w-[200%] items-start transition-transform duration-500 ease-in-out"
                     :class="tab === 'register' ? '-translate-x-1/2' : 'translate-x-0'">

                    {{-- ---------------- Sign in panel ---------------- --}}
                    <div class="w-1/2 shrink-0 pr-3">
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Email address
                                </label>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-envelope', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                                        required autocomplete="email"
                                        placeholder="you@example.com"
                                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div x-data="{ show: false }">
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <label for="password" class="block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                        Password
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary">
                                            Forgot it?
                                        </a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                                        required autocomplete="current-password"
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

                            {{-- Remember me --}}
                            <label for="remember" class="flex select-none items-center gap-2.5">
                                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                                    class="checkbox checkbox-sm checkbox-primary rounded-[0.3rem]">
                                <span class="text-sm text-base-content/65">Keep me signed in</span>
                            </label>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                                Sign in
                            </button>
                        </form>

                        <p class="mt-8 text-center text-sm text-base-content/55">
                            New to Filmstack?
                            <button type="button" @click="tab = 'register'" class="font-medium text-primary transition-colors hover:brightness-110">
                                Create an account
                            </button>
                        </p>
                    </div>

                    {{-- ---------------- Create account panel ---------------- --}}
                    <div class="w-1/2 shrink-0 pl-3">
                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            {{-- Username --}}
                            <div>
                                <label for="name" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Username
                                </label>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-user', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                                        required autocomplete="name"
                                        placeholder="Your username"
                                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="register-email" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Email address
                                </label>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-envelope', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input id="register-email" type="email" name="email" value="{{ old('email') }}"
                                        required autocomplete="email"
                                        placeholder="you@example.com"
                                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Profile image --}}
                            <div>
                                <label for="image" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Profile image
                                </label>
                                <input id="image" type="file" name="image" accept="image/*"
                                    class="block w-full rounded-selector border border-white/[0.09] bg-base-100 text-sm text-base-content/60 file:mr-3 file:rounded-selector file:border-0 file:bg-primary/15 file:px-3 file:py-1.5 file:font-display file:text-[0.68rem] file:uppercase file:tracking-[0.14em] file:text-primary file:transition hover:file:brightness-110">
                                @error('image')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div x-data="{ show: false }">
                                <label for="register-password" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Password
                                </label>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input :type="show ? 'text' : 'password'" id="register-password" name="password"
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
                                <p class="mt-2 text-xs text-base-content/45">
                                    At least 8 characters, mixing letters and numbers.
                                </p>
                            </div>

                            {{-- Confirm password --}}
                            <div x-data="{ show: false }">
                                <label for="password_confirmation" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                                    Confirm password
                                </label>
                                <div class="flex items-center gap-2.5 rounded-selector border border-white/[0.09] bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                                    @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                                    <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                        required autocomplete="new-password"
                                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                        class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                                    <button type="button" @click="show = !show"
                                        class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                                        x-text="show ? 'Hide' : 'Show'">Show</button>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                                Create account
                            </button>
                        </form>

                        <p class="mt-8 text-center text-sm text-base-content/55">
                            Already have an account?
                            <button type="button" @click="tab = 'signin'" class="font-medium text-primary transition-colors hover:brightness-110">
                                Sign in
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
