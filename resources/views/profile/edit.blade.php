@extends('layouts.app')

@section('title', 'Edit Profile')

@section('main-class', '')

@section('content')

<div class="cb-grain relative isolate flex min-h-[calc(100vh-4rem)] w-full justify-center px-6 py-16 sm:px-8">

    <div class="w-full max-w-2xl shrink-0 space-y-6">

        {{-- Profile information --}}
        <div class="overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                    @svg('heroicon-o-user-circle', 'h-6 w-6')
                </span>
                <div>
                    <h1 class="font-display text-2xl font-semibold uppercase tracking-[0.02em] text-base-content">
                        Edit profile
                    </h1>
                    <p class="text-sm text-base-content/55">Update your photo, name and email address.</p>
                </div>
            </div>

            @if (session('status') === 'profile-updated')
                <x-auth-session-status status="Profile updated." class="mt-6" />
            @endif

            <form action="{{ route('profile.update') }}" enctype="multipart/form-data" method="post" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                <div class="flex flex-col items-center gap-3 pb-2">
                    <div class="group relative">
                        <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border border-white/9 bg-base-100">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile" class="h-full w-full object-cover" id="imagePreview">
                            @else
                                <span class="font-display text-2xl font-semibold text-base-content/55">{{ substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>

                        <label for="image" class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-black/60 opacity-0 transition-opacity group-hover:opacity-100">
                            @svg('heroicon-o-camera', 'h-6 w-6 text-white')
                        </label>
                    </div>

                    <input type="file" class="hidden" name="image" id="image" accept="image/*" onchange="previewImage(this)">

                    <label for="image" class="font-display text-[0.72rem] uppercase tracking-[0.14em] text-primary transition-colors hover:brightness-110 cursor-pointer">
                        Change photo
                    </label>
                    @error('image')
                        <p class="text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                        Full name
                    </label>
                    <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                        @svg('heroicon-o-user', 'h-4 w-4 shrink-0 text-base-content/40')
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            required autocomplete="name"
                            placeholder="Enter your full name"
                            class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                        Email address
                    </label>
                    <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                        @svg('heroicon-o-envelope', 'h-4 w-4 shrink-0 text-base-content/40')
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            required autocomplete="username"
                            placeholder="Enter your email address"
                            class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 flex items-center justify-between gap-3 rounded-field bg-base-100 px-4 py-3">
                            <p class="text-sm text-base-content/60">Your email address is unverified.</p>
                            <form method="post" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="font-display text-[0.7rem] uppercase tracking-[0.14em] text-primary hover:brightness-110">
                                    Resend link
                                </button>
                            </form>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                            <x-auth-session-status status="A new verification link has been sent to your email address." class="mt-2" />
                        @endif
                    @endif
                </div>

                {{-- Submit --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="rounded-selector bg-primary px-8 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="overflow-hidden rounded-box border border-white/9 bg-base-200 p-6 sm:p-8">

            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-primary/15 text-primary">
                    @svg('heroicon-o-lock-closed', 'h-6 w-6')
                </span>
                <div>
                    <h2 class="font-display text-xl font-semibold uppercase tracking-[0.02em] text-base-content">
                        Change password
                    </h2>
                    <p class="text-sm text-base-content/55">Use a long, random password to stay secure.</p>
                </div>
            </div>

            @if (session('status') === 'password-updated')
                <x-auth-session-status status="Password updated." class="mt-6" />
            @endif

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('put')

                {{-- Current password --}}
                <div x-data="{ show: false }">
                    <label for="update_password_current_password" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                        Current password
                    </label>
                    <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                        @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                        <input :type="show ? 'text' : 'password'" id="update_password_current_password" name="current_password"
                            autocomplete="current-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                        <button type="button" @click="show = !show"
                            class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                            x-text="show ? 'Hide' : 'Show'">Show</button>
                    </div>
                    @error('current_password', 'updatePassword')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New password --}}
                <div x-data="{ show: false }">
                    <label for="update_password_password" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                        New password
                    </label>
                    <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                        @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                        <input :type="show ? 'text' : 'password'" id="update_password_password" name="password"
                            autocomplete="new-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                        <button type="button" @click="show = !show"
                            class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                            x-text="show ? 'Hide' : 'Show'">Show</button>
                    </div>
                    @error('password', 'updatePassword')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div x-data="{ show: false }">
                    <label for="update_password_password_confirmation" class="mb-2 block font-display text-[0.72rem] uppercase tracking-[0.16em] text-base-content/55">
                        Confirm new password
                    </label>
                    <div class="flex items-center gap-2.5 rounded-selector border border-white/9 bg-base-100 px-4 py-3 transition focus-within:border-primary/50">
                        @svg('heroicon-o-lock-closed', 'h-4 w-4 shrink-0 text-base-content/40')
                        <input :type="show ? 'text' : 'password'" id="update_password_password_confirmation" name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                            class="w-full bg-transparent text-sm text-base-content placeholder:text-base-content/40 focus:outline-none">
                        <button type="button" @click="show = !show"
                            class="shrink-0 font-display text-[0.68rem] uppercase tracking-[0.14em] text-base-content/45 transition-colors hover:text-primary"
                            x-text="show ? 'Hide' : 'Show'">Show</button>
                    </div>
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="rounded-selector bg-primary px-8 py-3.5 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-primary-content transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                        Update password
                    </button>
                </div>
            </form>
        </div>

        {{-- Delete account --}}
        <div class="overflow-hidden rounded-box border border-error/20 bg-base-200 p-6 sm:p-8">

            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-selector bg-error/10 text-error">
                    @svg('heroicon-o-exclamation-triangle', 'h-6 w-6')
                </span>
                <div>
                    <h2 class="font-display text-xl font-semibold uppercase tracking-[0.02em] text-base-content">
                        Delete account
                    </h2>
                    <p class="text-sm text-base-content/55">This action cannot be undone.</p>
                </div>
            </div>

            <p class="mt-4 text-sm leading-relaxed text-base-content/65">
                Once your account is deleted, all of its reviews, lists, marks and follows are permanently removed.
                Download or note anything you want to keep before continuing.
            </p>

            <x-confirm-modal title="Delete account?" message="Your account and all of its data will be deleted. This action cannot be undone."
                :action="route('profile.destroy')" method="DELETE">
                <x-slot name="trigger">
                    <button type="button"
                        class="mt-6 rounded-selector border border-error/30 bg-error/10 px-6 py-3 font-display text-[0.8rem] font-semibold uppercase tracking-[0.14em] text-error transition hover:bg-error/20">
                        Delete account
                    </button>
                </x-slot>
            </x-confirm-modal>
        </div>

    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                const imgContainer = input.closest('.relative').querySelector('.rounded-full');
                imgContainer.innerHTML = `<img src="${e.target.result}" alt="Profile" class="h-full w-full object-cover" id="imagePreview">`;
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
