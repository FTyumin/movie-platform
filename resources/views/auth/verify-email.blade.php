@extends('layouts.app')

@section('title', 'Verify email')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full items-center justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-md shrink-0 overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                @svg('heroicon-o-envelope-open', 'h-6 w-6')
            </span>
            <div>
                <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                    Verify your email
                </h1>
                <p class="text-sm text-base-content/55">One more step before you can get started.</p>
            </div>
        </div>

        <p class="mt-6 text-sm leading-relaxed text-base-content/65">
            Thanks for signing up! Before getting started, could you verify your email address by clicking the
            link we just emailed to you? If you didn't receive the email, we'll gladly send you another.
        </p>

        @if (session('status') == 'verification-link-sent')
            <x-auth-session-status class="mt-6" status="A new verification link has been sent to the email address you provided during registration." />
        @endif

        <div class="mt-6 flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="rounded-selector bg-primary px-6 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                    Resend verification email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="font-display text-[0.72rem] uppercase tracking-[0.14em] text-base-content/55 underline decoration-white/20 underline-offset-4 transition-colors hover:text-primary">
                    Log out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
